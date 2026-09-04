<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Dsp\DspMeasurementMethods as M;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function adsDspMeasurement(): M
{
    return new M(new FakeIntegration(
        accessToken: 'ads-access-token',
        refreshToken: 'ads-refresh',
        settings: [],
        active: true,
        expired: false,
    ), 'client-abc');
}

/**
 * @param  array<string, string|null>  $headers
 */
function adsDspMeasurementAssertSent(string $method, string $url, array $headers = [], mixed $body = null): void
{
    Http::assertSent(function (Request $req) use ($method, $url, $headers, $body) {
        if ($req->method() !== $method || $req->url() !== $url) {
            return false;
        }

        if ($req->header('Amazon-Advertising-API-ClientId')[0] !== 'client-abc'
            || $req->header('Authorization')[0] !== 'Bearer ads-access-token'
            || $req->header('Amazon-Advertising-API-Scope')[0] !== '111') {
            return false;
        }

        // BaseMethods::http() sempre manda acceptJson(); o media type próprio vem
        // como segundo valor do Accept — conferimos presença, não posição.
        foreach ($headers as $name => $expected) {
            $values = $req->header($name);
            $ok = $expected === null ? $values === [] : in_array($expected, $values, true);
            if (! $ok) {
                return false;
            }
        }

        return $body === null || json_decode($req->body(), true) === $body;
    });
}

beforeEach(function () {
    Http::preventStrayRequests();
});

const ADS_DSP_M_HOST = 'https://advertising-api.amazon.com';

describe('DspMeasurementMethods — elegibilidade', function () {
    $cases = [
        ['checkAudienceResearchEligibility', '/dsp/measurement/eligibility/audienceResearch', M::ELIGIBILITY_V1_2],
        ['checkBrandLiftEligibility', '/dsp/measurement/eligibility/brandLift', M::ELIGIBILITY_V1_1],
        ['checkCreativeTestingEligibility', '/dsp/measurement/eligibility/creativeTesting', M::ELIGIBILITY_V1_2],
        ['checkOmnichannelMetricsEligibility', '/dsp/measurement/eligibility/omnichannelMetrics', M::ELIGIBILITY_V1_3],
        ['checkPlanningEligibility', '/measurement/planning/eligibility', M::ELIGIBILITY_V1_3],
    ];

    foreach ($cases as [$method, $path, $mediaType]) {
        it("{$method}: POST {$path} com {$mediaType}, paginação na query e body", function () use ($method, $path, $mediaType) {
            Http::fake(['advertising-api.amazon.com/*' => Http::response(['eligibilityStatuses' => []])]);

            $body = ['orderIds' => ['o1'], 'vendorTypeFilters' => ['AMAZON']];
            adsDspMeasurement()->{$method}('111', $body, nextToken: 'n1', maxResults: 5);

            adsDspMeasurementAssertSent('POST', ADS_DSP_M_HOST.$path.'?nextToken=n1&maxResults=5', [
                'Content-Type' => $mediaType,
                'Accept' => $mediaType,
                'Amazon-Ads-AccountId' => null,
            ], $body);
        });
    }

    it('manda Amazon-Ads-AccountId quando informado', function () {
        Http::fake(['advertising-api.amazon.com/*' => Http::response([])]);

        adsDspMeasurement()->checkBrandLiftEligibility('111', ['orderIds' => ['o1']], adsAccountId: 'amzn1.ads-account.g.abc');

        adsDspMeasurementAssertSent('POST', ADS_DSP_M_HOST.'/dsp/measurement/eligibility/brandLift', ['Amazon-Ads-AccountId' => 'amzn1.ads-account.g.abc']);
    });
});

describe('DspMeasurementMethods — estudos por tipo', function () {
    $lists = [
        ['getAudienceResearchStudies', '/dsp/measurement/studies/audienceResearch', 'studyIds', M::STUDY_V1_2],
        ['getBrandLiftStudies', '/dsp/measurement/studies/brandLift', 'studyIdFilters', M::STUDY_V1_3],
        ['getCreativeTestingStudies', '/dsp/measurement/studies/creativeTesting', 'studyIds', M::STUDY_V1_2],
        ['getOmnichannelMetricsStudies', '/dsp/measurement/studies/omnichannelMetrics', 'studyIds', M::STUDY_V1_3],
        ['getStudies', '/measurement/studies', 'studyIds', M::STUDY_V1_3],
    ];

    foreach ($lists as [$method, $path, $idsParam, $accept]) {
        it("{$method}: GET {$path} com {$idsParam} comma-delimited, advertiserId e paginação", function () use ($method, $path, $idsParam, $accept) {
            Http::fake(['advertising-api.amazon.com/*' => Http::response(['studies' => [], 'nextToken' => null])]);

            adsDspMeasurement()->{$method}('111', ['s1', 's2'], 'adv1', 'n1', 20);

            adsDspMeasurementAssertSent('GET', ADS_DSP_M_HOST.$path."?{$idsParam}=s1%2Cs2&advertiserId=adv1&nextToken=n1&maxResults=20", [
                'Accept' => $accept,
                'Content-Type' => null,
            ]);
        });
    }

    $singleWrites = [
        ['createAudienceResearchStudy', 'POST', '/dsp/measurement/studies/audienceResearch', M::STUDY_V1_2],
        ['createCreativeTestingStudy', 'POST', '/dsp/measurement/studies/creativeTesting', M::STUDY_V1_2],
    ];

    foreach ($singleWrites as [$method, $verb, $path, $mediaType]) {
        it("{$method}: {$verb} {$path} manda um estudo no body ({$mediaType})", function () use ($method, $verb, $path, $mediaType) {
            Http::fake(['advertising-api.amazon.com/*' => Http::response(['id' => 's1'])]);

            $study = ['name' => 'Estudo', 'advertiserId' => 'adv1', 'vendorProductId' => 'vp1', 'status' => 'DRAFT'];
            adsDspMeasurement()->{$method}('111', $study);

            adsDspMeasurementAssertSent($verb, ADS_DSP_M_HOST.$path, ['Content-Type' => $mediaType, 'Accept' => $mediaType], $study);
        });
    }

    $singleUpdates = [
        ['updateAudienceResearchStudy', '/dsp/measurement/studies/audienceResearch'],
        ['updateCreativeTestingStudy', '/dsp/measurement/studies/creativeTesting'],
    ];

    foreach ($singleUpdates as [$method, $path]) {
        it("{$method}: PUT {$path}/{studyId} com studyId codificado", function () use ($method, $path) {
            Http::fake(['advertising-api.amazon.com/*' => Http::response(['id' => 's 1'])]);

            $study = ['name' => 'Estudo v2', 'status' => 'PENDING'];
            adsDspMeasurement()->{$method}('111', 's 1', $study);

            adsDspMeasurementAssertSent('PUT', ADS_DSP_M_HOST.$path.'/s%201', ['Content-Type' => M::STUDY_V1_2, 'Accept' => M::STUDY_V1_2], $study);
        });
    }

    $batchWrites = [
        ['createBrandLiftStudies', 'POST', '/dsp/measurement/studies/brandLift'],
        ['updateBrandLiftStudies', 'PUT', '/dsp/measurement/studies/brandLift'],
        ['createOmnichannelMetricsStudies', 'POST', '/dsp/measurement/studies/omnichannelMetrics'],
        ['updateOmnichannelMetricsStudies', 'PUT', '/dsp/measurement/studies/omnichannelMetrics'],
        ['createSurveys', 'POST', '/measurement/studies/surveys'],
        ['updateSurveys', 'PUT', '/measurement/studies/surveys'],
    ];

    foreach ($batchWrites as [$method, $verb, $path]) {
        it("{$method}: {$verb} {$path} em lote com studymanagement v1.3 (sobrescrevível)", function () use ($method, $verb, $path) {
            Http::fake(['advertising-api.amazon.com/*' => Http::response(['success' => [], 'error' => []])]);

            $items = [['id' => 's1', 'name' => 'A'], ['id' => 's2', 'name' => 'B']];
            adsDspMeasurement()->{$method}('111', $items);
            adsDspMeasurementAssertSent($verb, ADS_DSP_M_HOST.$path, ['Content-Type' => M::STUDY_V1_3, 'Accept' => M::STUDY_V1_3], $items);

            adsDspMeasurement()->{$method}('111', $items, null, 'application/vnd.studymanagement.v1+json');
            adsDspMeasurementAssertSent($verb, ADS_DSP_M_HOST.$path, ['Content-Type' => 'application/vnd.studymanagement.v1+json', 'Accept' => 'application/vnd.studymanagement.v1+json'], $items);
        });
    }

    $results = [
        ['getAudienceResearchStudyResult', '/dsp/measurement/studies/audienceResearch/s1/result', M::RESULT_V1_2],
        ['getBrandLiftStudyResult', '/measurement/studies/brandLift/s1/result', M::RESULT_V1_1],
        ['getCreativeTestingStudyResult', '/dsp/measurement/studies/creativeTesting/s1/result', M::RESULT_V1_2],
        ['getOmnichannelMetricsStudyResult', '/dsp/measurement/studies/omnichannelMetrics/s1/result', M::RESULT_V1_3],
    ];

    foreach ($results as [$method, $path, $accept]) {
        it("{$method}: GET {$path} com Accept {$accept}", function () use ($method, $path, $accept) {
            Http::fake(['advertising-api.amazon.com/*' => Http::response(['studyId' => 's1', 'results' => []])]);

            $out = adsDspMeasurement()->{$method}('111', 's1');

            expect($out['studyId'])->toBe('s1');
            adsDspMeasurementAssertSent('GET', ADS_DSP_M_HOST.$path, ['Accept' => $accept]);
        });
    }

    it('cancelStudies: DELETE /measurement/studies?studyIds=… sem body', function () {
        Http::fake(['advertising-api.amazon.com/measurement/studies*' => Http::response(['success' => [['id' => 's1']]])]);

        adsDspMeasurement()->cancelStudies('111', ['s1', 's2']);

        adsDspMeasurementAssertSent('DELETE', ADS_DSP_M_HOST.'/measurement/studies?studyIds=s1%2Cs2', ['Accept' => M::STUDY_V1_3, 'Content-Type' => null]);
    });

    it('getSurveys: por surveyIds ou studyId, com paginação', function () {
        Http::fake(['advertising-api.amazon.com/measurement/studies/surveys*' => Http::response(['surveys' => []])]);

        adsDspMeasurement()->getSurveys('111', surveyIds: ['sv1'], studyId: 's1', nextToken: 'n1', maxResults: 3);

        adsDspMeasurementAssertSent('GET', ADS_DSP_M_HOST.'/measurement/studies/surveys?surveyIds=sv1&studyId=s1&nextToken=n1&maxResults=3', ['Accept' => M::STUDY_V1_3]);
    });
});

describe('DspMeasurementMethods — vendor products', function () {
    it('listVendorProducts: POST com filtros no body e measurementvendor v1.1', function () {
        Http::fake(['advertising-api.amazon.com/measurement/vendorProducts/list*' => Http::response(['vendorProducts' => []])]);

        $body = ['studyTypeFilters' => ['BRAND_LIFT'], 'fundingTypeFilters' => ['AMAZON_FUNDED']];
        adsDspMeasurement()->listVendorProducts('111', $body, maxResults: 50);

        adsDspMeasurementAssertSent('POST', ADS_DSP_M_HOST.'/measurement/vendorProducts/list?maxResults=50', ['Content-Type' => M::VENDOR_V1_1, 'Accept' => M::VENDOR_V1_1], $body);
    });

    it('omnichannelMetricsBrandSearch: POST brands/list com ocmbrands v1.3', function () {
        Http::fake(['advertising-api.amazon.com/measurement/vendorProducts/omnichannelMetrics/brands/list*' => Http::response(['brands' => []])]);

        adsDspMeasurement()->omnichannelMetricsBrandSearch('111', ['brandNameSearch' => 'Soldiers'], nextToken: 'n1');

        adsDspMeasurementAssertSent('POST', ADS_DSP_M_HOST.'/measurement/vendorProducts/omnichannelMetrics/brands/list?nextToken=n1', ['Content-Type' => M::OCM_BRANDS_V1_3, 'Accept' => M::OCM_BRANDS_V1_3], ['brandNameSearch' => 'Soldiers']);
    });

    it('vendorProductPolicies: GET com vendorProductIds comma-delimited', function () {
        Http::fake(['advertising-api.amazon.com/measurement/vendorProducts/policies*' => Http::response(['policies' => []])]);

        adsDspMeasurement()->vendorProductPolicies('111', ['vp1', 'vp2'], maxResults: 2);

        adsDspMeasurementAssertSent('GET', ADS_DSP_M_HOST.'/measurement/vendorProducts/policies?vendorProductIds=vp1%2Cvp2&maxResults=2', ['Accept' => M::VENDOR_V1_1]);
    });

    it('vendorProductSurveyQuestionTemplates: GET com os dois filtros', function () {
        Http::fake(['advertising-api.amazon.com/measurement/vendorProducts/surveyQuestionTemplates*' => Http::response(['templates' => []])]);

        adsDspMeasurement()->vendorProductSurveyQuestionTemplates('111', ['vp1'], ['t1', 't2']);

        adsDspMeasurementAssertSent('GET', ADS_DSP_M_HOST.'/measurement/vendorProducts/surveyQuestionTemplates?vendorProductIds=vp1&surveyQuestionTemplateIds=t1%2Ct2', ['Accept' => M::VENDOR_V1_1]);
    });

    it('erro HTTP vira AmazonAdsRequestException', function () {
        Http::fake(['advertising-api.amazon.com/*' => Http::response(['message' => 'Study not found'], 404)]);

        adsDspMeasurement()->getBrandLiftStudyResult('111', 's9');
    })->throws(AmazonAdsRequestException::class, 'Study not found');
});

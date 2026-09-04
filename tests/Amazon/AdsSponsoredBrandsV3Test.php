<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\SponsoredBrands\SponsoredBrandsV3Methods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function adsSbV3Methods(): SponsoredBrandsV3Methods
{
    $integration = new FakeIntegration(
        accessToken: 'ads-access-token',
        refreshToken: 'ads-refresh',
        settings: [],
        active: true,
        expired: false,
    );

    return new SponsoredBrandsV3Methods($integration, 'client-abc');
}

/**
 * Confere verbo + URL completa + auth/scope + Accept (media type v3) + body exato.
 *
 * @param  array<mixed>|null  $body  JSON exato esperado (null = não confere)
 */
function adsSbV3AssertSent(string $verb, string $url, ?string $accept, ?array $body = null, array $extraHeaders = []): void
{
    Http::assertSent(function (Request $req) use ($verb, $url, $accept, $body, $extraHeaders) {
        if ($req->method() !== $verb || $req->url() !== $url) {
            return false;
        }

        if ($req->header('Amazon-Advertising-API-ClientId')[0] !== 'client-abc'
            || $req->header('Authorization')[0] !== 'Bearer ads-access-token'
            || $req->header('Amazon-Advertising-API-Scope')[0] !== '111') {
            return false;
        }

        // BaseMethods::http() já põe Accept: application/json; o media type próprio é acrescentado (Guzzle mantém os dois)
        if ($accept !== null && ! in_array($accept, $req->header('Accept'), true)) {
            return false;
        }

        foreach ($extraHeaders as $name => $value) {
            if (($req->header($name)[0] ?? null) !== $value) {
                return false;
            }
        }

        if ($body !== null) {
            if (! str_starts_with((string) ($req->header('Content-Type')[0] ?? ''), 'application/json')) {
                return false;
            }
            if (json_decode($req->body(), true) !== $body) {
                return false;
            }
        }

        return true;
    });
}

beforeEach(function () {
    Http::preventStrayRequests();
});

const ADS_SB_V3_HOST = 'https://advertising-api.amazon.com';

describe('SponsoredBrandsV3Methods — brands / asins / mídia / assets', function () {
    it('getBrands GET /brands?brandTypeFilter com Accept brand.v3', function () {
        Http::fake(['advertising-api.amazon.com/brands*' => Http::response([['brandId' => 'b1', 'brandEntityId' => 'ENTITY1']])]);
        $out = adsSbV3Methods()->getBrands('111', 'MANUFACTURER');
        expect($out[0]['brandEntityId'])->toBe('ENTITY1');
        adsSbV3AssertSent('GET', ADS_SB_V3_HOST.'/brands?brandTypeFilter=MANUFACTURER', 'application/vnd.brand.v3+json');
    });

    it('listAsins GET /pageAsins?pageUrl', function () {
        Http::fake(['advertising-api.amazon.com/pageAsins*' => Http::response(['asinList' => ['B01']])]);
        adsSbV3Methods()->listAsins('111', 'https://www.amazon.com.br/stores/page/abc');
        adsSbV3AssertSent('GET', ADS_SB_V3_HOST.'/pageAsins?pageUrl='.urlencode('https://www.amazon.com.br/stores/page/abc'), 'application/vnd.pageasins.v3+json');
    });

    it('describeMedia GET /media/describe?mediaId', function () {
        Http::fake(['advertising-api.amazon.com/media/describe*' => Http::response(['status' => 'Available'])]);
        $out = adsSbV3Methods()->describeMedia('111', 'm1');
        expect($out['status'])->toBe('Available');
        adsSbV3AssertSent('GET', ADS_SB_V3_HOST.'/media/describe?mediaId=m1', null);
    });

    it('listAssets GET /stores/assets?brandEntityId&mediaType', function () {
        Http::fake(['advertising-api.amazon.com/stores/assets*' => Http::response([['assetId' => 'as1']])]);
        adsSbV3Methods()->listAssets('111', 'ENTITY1', 'brandLogo');
        adsSbV3AssertSent('GET', ADS_SB_V3_HOST.'/stores/assets?brandEntityId=ENTITY1&mediaType=brandLogo', 'application/vnd.mediaasset.v3+json');
    });

    it('createAsset POST /stores/assets manda binário puro com Content-Type da imagem + Content-Disposition', function () {
        Http::fake(['advertising-api.amazon.com/stores/assets' => Http::response(['assetId' => 'as1'])]);
        $out = adsSbV3Methods()->createAsset('111', 'PNGBYTES', 'logo.png', 'image/png', ['mediaType' => 'brandLogo', 'brandEntityId' => 'ENTITY1']);
        expect($out['assetId'])->toBe('as1');
        Http::assertSent(fn (Request $req) => $req->method() === 'POST'
            && $req->url() === ADS_SB_V3_HOST.'/stores/assets'
            && $req->header('Amazon-Advertising-API-Scope')[0] === '111'
            && $req->header('Amazon-Advertising-API-ClientId')[0] === 'client-abc'
            && $req->header('Authorization')[0] === 'Bearer ads-access-token'
            && $req->header('Content-Type')[0] === 'image/png'
            && $req->header('Content-Disposition')[0] === 'logo.png'
            && $req->header('assetInfo')[0] === '{"mediaType":"brandLogo","brandEntityId":"ENTITY1"}'
            && $req->body() === 'PNGBYTES');
    });
});

describe('SponsoredBrandsV3Methods — keywords', function () {
    it('listKeywords GET /sb/keywords com filtros e Accept v3.2', function () {
        Http::fake(['advertising-api.amazon.com/sb/keywords*' => Http::response([['keywordId' => 1]])]);
        adsSbV3Methods()->listKeywords('111', ['startIndex' => 0, 'count' => 50, 'stateFilter' => 'enabled']);
        adsSbV3AssertSent('GET', ADS_SB_V3_HOST.'/sb/keywords?startIndex=0&count=50&stateFilter=enabled', SponsoredBrandsV3Methods::ACCEPT_KEYWORD);
    });

    it('createKeywords POST e updateKeywords PUT /sb/keywords com lista pura no body', function () {
        Http::fake(['advertising-api.amazon.com/sb/keywords' => Http::response([['keywordId' => 1, 'code' => 'SUCCESS']])]);
        $m = adsSbV3Methods();

        $kw = [['campaignId' => 1, 'adGroupId' => 2, 'keywordText' => 'whey', 'matchType' => 'broad', 'bid' => 1.5]];
        $m->createKeywords('111', $kw);
        adsSbV3AssertSent('POST', ADS_SB_V3_HOST.'/sb/keywords', SponsoredBrandsV3Methods::ACCEPT_KEYWORD_RESPONSE, $kw);

        $upd = [['keywordId' => 1, 'campaignId' => 1, 'adGroupId' => 2, 'state' => 'paused']];
        $m->updateKeywords('111', $upd);
        adsSbV3AssertSent('PUT', ADS_SB_V3_HOST.'/sb/keywords', SponsoredBrandsV3Methods::ACCEPT_KEYWORD_RESPONSE, $upd);
    });

    it('getKeyword GET e archiveKeyword DELETE /sb/keywords/{id}', function () {
        Http::fake(['advertising-api.amazon.com/sb/keywords/*' => Http::response(['keywordId' => 9])]);
        $m = adsSbV3Methods();

        $m->getKeyword('111', '9', 'pt_BR');
        adsSbV3AssertSent('GET', ADS_SB_V3_HOST.'/sb/keywords/9?locale=pt_BR', 'application/vnd.sbkeyword.v3+json');

        $m->archiveKeyword('111', '9');
        adsSbV3AssertSent('DELETE', ADS_SB_V3_HOST.'/sb/keywords/9', SponsoredBrandsV3Methods::ACCEPT_KEYWORD_RESPONSE);
    });

    it('negative keywords: list / create / update / get / archive', function () {
        Http::fake(['advertising-api.amazon.com/sb/negativeKeywords*' => Http::response([['keywordId' => 3]])]);
        $m = adsSbV3Methods();

        $m->listNegativeKeywords('111', ['campaignIdFilter' => '1']);
        adsSbV3AssertSent('GET', ADS_SB_V3_HOST.'/sb/negativeKeywords?campaignIdFilter=1', SponsoredBrandsV3Methods::ACCEPT_NEGATIVE_KEYWORD);

        $neg = [['campaignId' => 1, 'adGroupId' => 2, 'keywordText' => 'barato', 'matchType' => 'negativeExact']];
        $m->createNegativeKeywords('111', $neg);
        adsSbV3AssertSent('POST', ADS_SB_V3_HOST.'/sb/negativeKeywords', SponsoredBrandsV3Methods::ACCEPT_KEYWORD_RESPONSE, $neg);

        $upd = [['keywordId' => 3, 'campaignId' => 1, 'adGroupId' => 2, 'state' => 'archived']];
        $m->updateNegativeKeywords('111', $upd);
        adsSbV3AssertSent('PUT', ADS_SB_V3_HOST.'/sb/negativeKeywords', SponsoredBrandsV3Methods::ACCEPT_KEYWORD_RESPONSE, $upd);

        $m->getNegativeKeyword('111', '3');
        adsSbV3AssertSent('GET', ADS_SB_V3_HOST.'/sb/negativeKeywords/3', 'application/vnd.sbnegativekeyword.v3+json');

        $m->archiveNegativeKeyword('111', '3');
        adsSbV3AssertSent('DELETE', ADS_SB_V3_HOST.'/sb/negativeKeywords/3', SponsoredBrandsV3Methods::ACCEPT_KEYWORD_RESPONSE);
    });
});

describe('SponsoredBrandsV3Methods — targets / negative targets / themes', function () {
    it('listTargets POST /sb/targets/list com nextToken e filtros', function () {
        Http::fake(['advertising-api.amazon.com/sb/targets/list' => Http::response(['targets' => [], 'nextToken' => 'n2'])]);
        $body = ['maxResults' => 10, 'nextToken' => 'n1', 'filters' => [['filterType' => 'STATE', 'values' => ['ENABLED']]]];
        $out = adsSbV3Methods()->listTargets('111', $body);
        expect($out['nextToken'])->toBe('n2');
        adsSbV3AssertSent('POST', ADS_SB_V3_HOST.'/sb/targets/list', 'application/vnd.sblisttargetsresponse.v3.2+json', $body);
    });

    it('createTargets POST / updateTargets PUT / getTarget / archiveTarget', function () {
        Http::fake(['advertising-api.amazon.com/sb/targets*' => Http::response(['createTargetSuccessResults' => []])]);
        $m = adsSbV3Methods();

        $t = [['campaignId' => 1, 'adGroupId' => 2, 'expressions' => [['type' => 'asinSameAs', 'value' => 'B01']], 'bid' => 1.2]];
        $m->createTargets('111', $t);
        adsSbV3AssertSent('POST', ADS_SB_V3_HOST.'/sb/targets', 'application/vnd.sbcreatetargetsresponse.v3+json', ['targets' => $t]);

        $u = [['targetId' => 5, 'adGroupId' => 2, 'campaignId' => 1, 'state' => 'paused']];
        $m->updateTargets('111', $u);
        adsSbV3AssertSent('PUT', ADS_SB_V3_HOST.'/sb/targets', 'application/vnd.updatetargetsresponse.v3+json', ['targets' => $u]);

        $m->getTarget('111', '5');
        adsSbV3AssertSent('GET', ADS_SB_V3_HOST.'/sb/targets/5', 'application/vnd.sbtarget.v3+json');

        $m->archiveTarget('111', '5');
        adsSbV3AssertSent('DELETE', ADS_SB_V3_HOST.'/sb/targets/5', 'application/vnd.sbtargetresponse.v3+json');
    });

    it('negative targets: list / create / update / get / archive', function () {
        Http::fake(['advertising-api.amazon.com/sb/negativeTargets*' => Http::response(['negativeTargets' => []])]);
        $m = adsSbV3Methods();

        $m->listNegativeTargets('111', ['maxResults' => 5]);
        adsSbV3AssertSent('POST', ADS_SB_V3_HOST.'/sb/negativeTargets/list', 'application/vnd.sblistnegativetargetsresponse.v3.2+json', ['maxResults' => 5]);

        $n = [['campaignId' => 1, 'adGroupId' => 2, 'expressions' => [['type' => 'asinBrandSameAs', 'value' => 'brand']]]];
        $m->createNegativeTargets('111', $n);
        adsSbV3AssertSent('POST', ADS_SB_V3_HOST.'/sb/negativeTargets', 'application/vnd.sbcreatenegativetargetsrequest.v3+json', ['negativeTargets' => $n]);

        $u = [['targetId' => 7, 'adGroupId' => 2, 'state' => 'archived']];
        $m->updateNegativeTargets('111', $u);
        adsSbV3AssertSent('PUT', ADS_SB_V3_HOST.'/sb/negativeTargets', 'application/vnd.updatenegativetargetsresponse.v3+json', ['negativeTargets' => $u]);

        $m->getNegativeTarget('111', '7');
        adsSbV3AssertSent('GET', ADS_SB_V3_HOST.'/sb/negativeTargets/7', 'application/vnd.sbnegativetarget.v3+json');

        $m->archiveNegativeTarget('111', '7');
        adsSbV3AssertSent('DELETE', ADS_SB_V3_HOST.'/sb/negativeTargets/7', 'application/vnd.sbnegativetarget.v3+json');
    });

    it('themes: listThemes / createThemes / updateThemes', function () {
        Http::fake(['advertising-api.amazon.com/sb/themes*' => Http::response(['themes' => []])]);
        $m = adsSbV3Methods();

        $m->listThemes('111', ['campaignIdFilter' => ['include' => ['1']]]);
        adsSbV3AssertSent('POST', ADS_SB_V3_HOST.'/sb/themes/list', 'application/vnd.sbthemeslistresponse.v3+json', ['campaignIdFilter' => ['include' => ['1']]]);

        $c = [['adGroupId' => '2', 'campaignId' => '1', 'themeType' => 'KEYWORDS_RELATED_TO_YOUR_BRAND', 'bid' => 1.1]];
        $m->createThemes('111', $c);
        adsSbV3AssertSent('POST', ADS_SB_V3_HOST.'/sb/themes', 'application/vnd.sbthemescreateresponse.v3+json', ['themes' => $c]);

        $u = [['themeId' => 't1', 'adGroupId' => '2', 'state' => 'PAUSED']];
        $m->updateThemes('111', $u);
        adsSbV3AssertSent('PUT', ADS_SB_V3_HOST.'/sb/themes', 'application/vnd.sbthemesupdateresponse.v3+json', ['themes' => $u]);
    });
});

describe('SponsoredBrandsV3Methods — recomendações v3', function () {
    it('getBidsRecommendations POST /sb/recommendations/bids', function () {
        Http::fake(['advertising-api.amazon.com/sb/recommendations/bids' => Http::response(['keywordsBidsRecommendationSuccessResults' => []])]);
        $body = ['campaignId' => 1, 'keywords' => [['keywordText' => 'whey', 'matchType' => 'broad']], 'adFormat' => 'productCollection'];
        adsSbV3Methods()->getBidsRecommendations('111', $body);
        adsSbV3AssertSent('POST', ADS_SB_V3_HOST.'/sb/recommendations/bids', 'application/vnd.sbbidsrecommendation.v3+json', $body);
    });

    it('getProductRecommendations POST /sb/recommendations/targets/product/list', function () {
        Http::fake(['advertising-api.amazon.com/sb/recommendations/targets/product/list' => Http::response(['recommendedProducts' => []])]);
        adsSbV3Methods()->getProductRecommendations('111', ['maxResults' => 20, 'filters' => [['filterType' => 'ASINS', 'values' => ['B01']]]]);
        adsSbV3AssertSent('POST', ADS_SB_V3_HOST.'/sb/recommendations/targets/product/list', 'application/vnd.sbproductrecommendationsresponse.v3.1+json', ['maxResults' => 20, 'filters' => [['filterType' => 'ASINS', 'values' => ['B01']]]]);
    });

    it('getTargetingCategories POST /sb/recommendations/targets/category?locale', function () {
        Http::fake(['advertising-api.amazon.com/sb/recommendations/targets/category*' => Http::response(['categories' => []])]);
        adsSbV3Methods()->getTargetingCategories('111', ['B01', 'B02'], 'AMAZON', 'pt_BR');
        adsSbV3AssertSent('POST', ADS_SB_V3_HOST.'/sb/recommendations/targets/category?locale=pt_BR', 'application/vnd.sbcategoryrecommendationsresponse.v3.2+json', ['asins' => ['B01', 'B02'], 'supplySource' => 'AMAZON']);
    });

    it('getBrandRecommendations POST /sb/recommendations/targets/brand', function () {
        Http::fake(['advertising-api.amazon.com/sb/recommendations/targets/brand' => Http::response(['brands' => []])]);
        adsSbV3Methods()->getBrandRecommendations('111', ['keyword' => 'whey']);
        adsSbV3AssertSent('POST', ADS_SB_V3_HOST.'/sb/recommendations/targets/brand', 'application/vnd.sbbrandrecommendationsresponse.v3.1+json', ['keyword' => 'whey']);
    });
});

describe('SponsoredBrandsV3Methods — moderação + relatórios v2 (deprecated)', function () {
    it('getCampaignModeration GET /sb/moderation/campaigns/{id}', function () {
        Http::fake(['advertising-api.amazon.com/sb/moderation/campaigns/*' => Http::response(['campaignId' => 1, 'status' => 'APPROVED'])]);
        $out = adsSbV3Methods()->getCampaignModeration('111', '1');
        expect($out['status'])->toBe('APPROVED');
        adsSbV3AssertSent('GET', ADS_SB_V3_HOST.'/sb/moderation/campaigns/1', 'application/vnd.sbmoderation.v3+json');
    });

    it('requestReport POST /v2/hsa/{recordType}/report e getReport GET /v2/reports/{id}', function () {
        Http::fake([
            'advertising-api.amazon.com/v2/hsa/campaigns/report' => Http::response(['reportId' => 'r1', 'status' => 'IN_PROGRESS']),
            'advertising-api.amazon.com/v2/reports/r1' => Http::response(['reportId' => 'r1', 'status' => 'SUCCESS', 'location' => 'https://x/y']),
        ]);
        $m = adsSbV3Methods();

        $out = $m->requestReport('111', 'campaigns', '20260901', 'impressions,clicks,cost', ['segment' => 'placement']);
        expect($out['reportId'])->toBe('r1');
        adsSbV3AssertSent('POST', ADS_SB_V3_HOST.'/v2/hsa/campaigns/report', 'application/json', ['reportDate' => '20260901', 'metrics' => 'impressions,clicks,cost', 'segment' => 'placement']);

        $status = $m->getReport('111', 'r1');
        expect($status['status'])->toBe('SUCCESS');
        adsSbV3AssertSent('GET', ADS_SB_V3_HOST.'/v2/reports/r1', 'application/json');
    });

    it('downloadReport GET /v2/reports/{id}/download devolve as linhas (gzip ou JSON puro)', function () {
        $rows = [['campaignId' => 1, 'cost' => 12.5]];
        Http::fake(['advertising-api.amazon.com/v2/reports/r1/download' => Http::response(gzencode((string) json_encode($rows)))]);

        $out = adsSbV3Methods()->downloadReport('111', 'r1');

        expect($out)->toBe($rows);
        adsSbV3AssertSent('GET', ADS_SB_V3_HOST.'/v2/reports/r1/download', null);
    });

    it('downloadReport com corpo inválido lança AmazonAdsRequestException', function () {
        Http::fake(['advertising-api.amazon.com/v2/reports/r1/download' => Http::response('not-json')]);
        adsSbV3Methods()->downloadReport('111', 'r1');
    })->throws(AmazonAdsRequestException::class);

    it('erro HTTP vira AmazonAdsRequestException com a mensagem do corpo', function () {
        Http::fake(['advertising-api.amazon.com/sb/keywords/9' => Http::response(['message' => 'Not found'], 404)]);
        adsSbV3Methods()->getKeyword('111', '9');
    })->throws(AmazonAdsRequestException::class, 'Not found');
});

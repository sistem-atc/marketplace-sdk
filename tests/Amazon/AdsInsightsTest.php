<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Insights\InsightsMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function adsInsightsMethods(): InsightsMethods
{
    $integration = new FakeIntegration(
        accessToken: 'ads-access-token',
        refreshToken: 'ads-refresh',
        settings: [],
        active: true,
        expired: false,
    );

    return new InsightsMethods($integration, 'client-abc');
}

function adsInsightsAuthOk(\Illuminate\Http\Client\Request $req, string $scope): bool
{
    return $req->header('Amazon-Advertising-API-ClientId')[0] === 'client-abc'
        && $req->header('Authorization')[0] === 'Bearer ads-access-token'
        && $req->header('Amazon-Advertising-API-Scope')[0] === $scope;
}

beforeEach(fn () => Http::preventStrayRequests());

describe('Ads InsightsMethods — Amazon Attribution', function () {
    it('getAdvertisersByProfile: GET /attribution/advertisers', function () {
        Http::fake(['advertising-api.amazon.com/attribution/advertisers' => Http::response(['advertisers' => [['advertiserId' => 'adv1']]])]);

        expect(adsInsightsMethods()->getAdvertisersByProfile('111')['advertisers'][0]['advertiserId'])->toBe('adv1');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://advertising-api.amazon.com/attribution/advertisers'
            && adsInsightsAuthOk($req, '111'));
    });

    it('getPublishers: GET /attribution/publishers', function () {
        Http::fake(['advertising-api.amazon.com/attribution/publishers' => Http::response(['publishers' => []])]);

        adsInsightsMethods()->getPublishers('111');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://advertising-api.amazon.com/attribution/publishers'
            && adsInsightsAuthOk($req, '111'));
    });

    it('getAttributionTagsByCampaign: POST /attribution/report com body do relatório', function () {
        Http::fake(['advertising-api.amazon.com/attribution/report' => Http::response(['reports' => [], 'size' => 0])]);

        adsInsightsMethods()->getAttributionTagsByCampaign('111', ['reportType' => 'PERFORMANCE', 'advertiserIds' => 'a,b', 'startDate' => '20260801', 'endDate' => '20260831']);

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/attribution/report'
            && $req->hasHeader('Content-Type', 'application/json')
            && $req['reportType'] === 'PERFORMANCE'
            && adsInsightsAuthOk($req, '111'));
    });

    it('tags macro / nonMacro: GET com publisherIds e advertiserIds em csv', function () {
        Http::fake(['advertising-api.amazon.com/attribution/tags/*' => Http::response(['tags' => []])]);

        adsInsightsMethods()->getPublisherAttributionTagTemplate('111', ['p1', 'p2'], [5, 6]);
        adsInsightsMethods()->getPublisherMacroAttributionTag('111', ['p1']);

        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://advertising-api.amazon.com/attribution/tags/macroTag?publisherIds=p1%2Cp2&advertiserIds=5%2C6'
            && adsInsightsAuthOk($req, '111'));
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://advertising-api.amazon.com/attribution/tags/nonMacroTemplateTag?publisherIds=p1'
            && adsInsightsAuthOk($req, '111'));
    });
});

describe('Ads InsightsMethods — Brand Metrics', function () {
    it('generateBrandMetricsReport: POST com media type insightsbrandmetrics versionado', function () {
        Http::fake(['advertising-api.amazon.com/insights/brandMetrics/report' => Http::response(['reportId' => 'bm-1', 'status' => 'IN_PROGRESS'])]);

        $out = adsInsightsMethods()->generateBrandMetricsReport('111', ['brandName' => 'Soldiers', 'categoryPath' => ['Health'], 'lookBackPeriod' => '1m'], version: 'v1.1');

        expect($out['reportId'])->toBe('bm-1');
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/insights/brandMetrics/report'
            && $req->hasHeader('Content-Type', 'application/vnd.insightsbrandmetrics.v1.1+json')
            && $req->hasHeader('Accept', 'application/vnd.insightsbrandmetrics.v1.1+json')
            && $req['brandName'] === 'Soldiers'
            && adsInsightsAuthOk($req, '111'));
    });

    it('getBrandMetricsReport: GET /insights/brandMetrics/report/{id} com Accept v1', function () {
        Http::fake(['advertising-api.amazon.com/insights/brandMetrics/report/bm-1' => Http::response(['reportId' => 'bm-1', 'status' => 'SUCCESSFUL', 'location' => 'https://s3/x'])]);

        expect(adsInsightsMethods()->getBrandMetricsReport('111', 'bm-1')['status'])->toBe('SUCCESSFUL');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://advertising-api.amazon.com/insights/brandMetrics/report/bm-1'
            && $req->hasHeader('Accept', 'application/vnd.insightsbrandmetrics.v1+json')
            && adsInsightsAuthOk($req, '111'));
    });
});

describe('Ads InsightsMethods — audience insights', function () {
    it('getAudiencesOverlappingAudiences: GET com adType obrigatório, csv em audienceCategory e Accept v2', function () {
        Http::fake(['advertising-api.amazon.com/insights/audiences/*' => Http::response(['overlappingAudiences' => [], 'nextToken' => 'n'])]);

        $out = adsInsightsMethods()->getAudiencesOverlappingAudiences('111', 'aud 1', 'DSP', ['maxResults' => 5, 'audienceCategory' => ['In-market', 'Lifestyle']]);

        expect($out['nextToken'])->toBe('n');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://advertising-api.amazon.com/insights/audiences/aud%201/overlappingAudiences?adType=DSP&maxResults=5&audienceCategory=In-market%2CLifestyle'
            && $req->hasHeader('Accept', 'application/vnd.insightsaudiencesoverlap.v2+json')
            && adsInsightsAuthOk($req, '111'));
    });
});

<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\SponsoredProducts\SponsoredProductsV2Methods as SPV2;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function adsSpV2(): SPV2
{
    return new SPV2(new FakeIntegration(
        accessToken: 'ads-access-token',
        refreshToken: 'ads-refresh',
        settings: [],
        active: true,
        expired: false,
    ), 'client-abc');
}

/** @param  array<string, mixed>|null  $body  null = não checa */
function adsSpV2AssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(function (Request $req) use ($method, $url, $body) {
        return $req->method() === $method
            && $req->url() === $url
            && $req->header('Amazon-Advertising-API-ClientId')[0] === 'client-abc'
            && $req->header('Authorization')[0] === 'Bearer ads-access-token'
            && $req->header('Amazon-Advertising-API-Scope')[0] === '111'
            && ($body === null || ($req->hasHeader('Content-Type', 'application/json') && json_decode($req->body(), true) === $body));
    });
}

beforeEach(function () {
    Http::preventStrayRequests();
});

$host = 'https://advertising-api.amazon.com';

describe('Amazon Ads — SponsoredProductsV2Methods (legado)', function () use ($host) {
    it('manda verbo, URL, Scope e body certos', function (Closure $call, string $method, string $url, ?array $body) {
        Http::fake(['advertising-api.amazon.com/*' => Http::response(['ok' => true])]);

        expect($call(adsSpV2()))->toBe(['ok' => true]);

        adsSpV2AssertSent($method, $url, $body);
    })->with([
        'getAdGroupBidRecommendations' => [fn (SPV2 $sp) => $sp->getAdGroupBidRecommendations('111', 'g1'), 'GET', "{$host}/v2/sp/adGroups/g1/bidRecommendations", null],
        'getKeywordBidRecommendations' => [fn (SPV2 $sp) => $sp->getKeywordBidRecommendations('111', 'k1'), 'GET', "{$host}/v2/sp/keywords/k1/bidRecommendations", null],
        'createKeywordBidRecommendations' => [fn (SPV2 $sp) => $sp->createKeywordBidRecommendations('111', 'g1', [['keyword' => 'whey', 'matchType' => 'exact']]), 'POST', "{$host}/v2/sp/keywords/bidRecommendations", ['adGroupId' => 'g1', 'keywords' => [['keyword' => 'whey', 'matchType' => 'exact']]]],
        'getAdGroupSuggestedKeywords' => [fn (SPV2 $sp) => $sp->getAdGroupSuggestedKeywords('111', 'g1', 50, 'enabled'), 'GET', "{$host}/v2/sp/adGroups/g1/suggested/keywords?maxNumSuggestions=50&adStateFilter=enabled", null],
        'getAdGroupSuggestedKeywordsEx' => [fn (SPV2 $sp) => $sp->getAdGroupSuggestedKeywordsEx('111', 'g1', 10, 'yes'), 'GET', "{$host}/v2/sp/adGroups/g1/suggested/keywords/extended?maxNumSuggestions=10&suggestBids=yes", null],
        'getAsinSuggestedKeywords' => [fn (SPV2 $sp) => $sp->getAsinSuggestedKeywords('111', 'B01', 5), 'GET', "{$host}/v2/sp/asins/B01/suggested/keywords?maxNumSuggestions=5", null],
        'bulkGetAsinSuggestedKeywords' => [fn (SPV2 $sp) => $sp->bulkGetAsinSuggestedKeywords('111', ['B01', 'B02'], 5), 'POST', "{$host}/v2/sp/asins/suggested/keywords", ['asins' => ['B01', 'B02'], 'maxNumSuggestions' => 5]],
        'getBidRecommendations' => [fn (SPV2 $sp) => $sp->getBidRecommendations('111', 'g1', [[['type' => 'asinSameAs', 'value' => 'B01']]]), 'POST', "{$host}/v2/sp/targets/bidRecommendations", ['adGroupId' => 'g1', 'expressions' => [[['type' => 'asinSameAs', 'value' => 'B01']]]]],
        'requestSnapshot' => [fn (SPV2 $sp) => $sp->requestSnapshot('111', 'campaigns', ['stateFilter' => 'enabled, paused']), 'POST', "{$host}/v2/sp/campaigns/snapshot", ['stateFilter' => 'enabled, paused']],
        'getSnapshotStatus' => [fn (SPV2 $sp) => $sp->getSnapshotStatus('111', 's1'), 'GET', "{$host}/v2/sp/snapshots/s1", null],
    ]);

    it('downloadSnapshot segue o redirect e devolve o JSON (gzip tolerado)', function () use ($host) {
        Http::fake([
            'advertising-api.amazon.com/v2/sp/snapshots/s1/download' => Http::response(gzencode(json_encode([['campaignId' => 1], ['campaignId' => 2]]))),
        ]);

        $rows = adsSpV2()->downloadSnapshot('111', 's1');

        expect($rows)->toHaveCount(2)->and($rows[1]['campaignId'])->toBe(2);

        adsSpV2AssertSent('GET', "{$host}/v2/sp/snapshots/s1/download");
    });

    it('erro HTTP vira AmazonAdsRequestException', function () {
        Http::fake(['advertising-api.amazon.com/v2/sp/snapshots/s9' => Http::response(['message' => 'Not Found'], 404)]);

        adsSpV2()->getSnapshotStatus('111', 's9');
    })->throws(AmazonAdsRequestException::class, 'Not Found');
});

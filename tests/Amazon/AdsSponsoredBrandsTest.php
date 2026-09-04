<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\SponsoredBrands\SponsoredBrandsMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function adsSbMethods(): SponsoredBrandsMethods
{
    $integration = new FakeIntegration(
        accessToken: 'ads-access-token',
        refreshToken: 'ads-refresh',
        settings: [],
        active: true,
        expired: false,
    );

    return new SponsoredBrandsMethods($integration, 'client-abc');
}

/**
 * Confere verbo + URL completa + headers de auth/scope + media type + body.
 *
 * @param  array<string, mixed>  $body  pares chave => valor esperados no JSON enviado
 */
function adsSbAssertSent(string $verb, string $url, ?string $contentType, ?string $accept = null, array $body = [], array $extraHeaders = []): void
{
    Http::assertSent(function (Request $req) use ($verb, $url, $contentType, $accept, $body, $extraHeaders) {
        if ($req->method() !== $verb || $req->url() !== $url) {
            return false;
        }

        if ($req->header('Amazon-Advertising-API-ClientId')[0] !== 'client-abc'
            || $req->header('Authorization')[0] !== 'Bearer ads-access-token'
            || $req->header('Amazon-Advertising-API-Scope')[0] !== '111') {
            return false;
        }

        if ($contentType !== null && ($req->header('Content-Type')[0] ?? null) !== $contentType) {
            return false;
        }

        // BaseMethods::http() já põe Accept: application/json; o media type próprio é acrescentado (Guzzle mantém os dois)
        $expectedAccept = $accept ?? $contentType;
        if ($expectedAccept !== null && ! in_array($expectedAccept, $req->header('Accept'), true)) {
            return false;
        }

        foreach ($extraHeaders as $name => $value) {
            if (($req->header($name)[0] ?? null) !== $value) {
                return false;
            }
        }

        if ($body !== []) {
            $sent = json_decode($req->body(), true);
            foreach ($body as $key => $value) {
                if (($sent[$key] ?? null) !== $value) {
                    return false;
                }
            }
        }

        return true;
    });
}

beforeEach(function () {
    Http::preventStrayRequests();
});

const ADS_SB_HOST = 'https://advertising-api.amazon.com';

describe('SponsoredBrandsMethods — campanhas/adGroups/ads v4', function () {
    it('createCampaigns POST /sb/v4/campaigns com media type de campanha', function () {
        Http::fake(['advertising-api.amazon.com/sb/v4/campaigns' => Http::response(['campaigns' => ['success' => [['campaignId' => 'c1']]]])]);

        $out = adsSbMethods()->createCampaigns('111', [['name' => 'SB 1', 'state' => 'ENABLED', 'budget' => 10, 'budgetType' => 'DAILY']]);

        expect($out['campaigns']['success'][0]['campaignId'])->toBe('c1');
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/v4/campaigns', SponsoredBrandsMethods::CT_CAMPAIGN, body: ['campaigns' => [['name' => 'SB 1', 'state' => 'ENABLED', 'budget' => 10, 'budgetType' => 'DAILY']]]);
    });

    it('updateCampaigns PUT /sb/v4/campaigns', function () {
        Http::fake(['advertising-api.amazon.com/sb/v4/campaigns' => Http::response(['campaigns' => []])]);
        adsSbMethods()->updateCampaigns('111', [['campaignId' => 'c1', 'state' => 'PAUSED']]);
        adsSbAssertSent('PUT', ADS_SB_HOST.'/sb/v4/campaigns', SponsoredBrandsMethods::CT_CAMPAIGN, body: ['campaigns' => [['campaignId' => 'c1', 'state' => 'PAUSED']]]);
    });

    it('deleteCampaigns POST /sb/v4/campaigns/delete com campaignIdFilter', function () {
        Http::fake(['advertising-api.amazon.com/sb/v4/campaigns/delete' => Http::response(['campaigns' => []])]);
        adsSbMethods()->deleteCampaigns('111', ['c1', 'c2']);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/v4/campaigns/delete', SponsoredBrandsMethods::CT_CAMPAIGN, body: ['campaignIdFilter' => ['include' => ['c1', 'c2']]]);
    });

    it('listCampaigns POST /sb/v4/campaigns/list com filtros e nextToken', function () {
        Http::fake(['advertising-api.amazon.com/sb/v4/campaigns/list' => Http::response(['campaigns' => [['campaignId' => 'c1']], 'nextToken' => 'n2'])]);
        $out = adsSbMethods()->listCampaigns('111', ['stateFilter' => ['include' => ['ENABLED']], 'maxResults' => 50, 'nextToken' => 'n1']);
        expect($out['nextToken'])->toBe('n2');
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/v4/campaigns/list', SponsoredBrandsMethods::CT_CAMPAIGN, body: ['stateFilter' => ['include' => ['ENABLED']], 'maxResults' => 50, 'nextToken' => 'n1']);
    });

    it('createAdGroups / updateAdGroups / deleteAdGroups / listAdGroups', function () {
        Http::fake(['advertising-api.amazon.com/sb/v4/adGroups*' => Http::response(['adGroups' => []])]);
        $m = adsSbMethods();

        $m->createAdGroups('111', [['campaignId' => 'c1', 'name' => 'g', 'state' => 'ENABLED']]);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/v4/adGroups', SponsoredBrandsMethods::CT_AD_GROUP, body: ['adGroups' => [['campaignId' => 'c1', 'name' => 'g', 'state' => 'ENABLED']]]);

        $m->updateAdGroups('111', [['adGroupId' => 'g1', 'state' => 'PAUSED']]);
        adsSbAssertSent('PUT', ADS_SB_HOST.'/sb/v4/adGroups', SponsoredBrandsMethods::CT_AD_GROUP, body: ['adGroups' => [['adGroupId' => 'g1', 'state' => 'PAUSED']]]);

        $m->deleteAdGroups('111', ['g1']);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/v4/adGroups/delete', SponsoredBrandsMethods::CT_AD_GROUP, body: ['adGroupIdFilter' => ['include' => ['g1']]]);

        $m->listAdGroups('111', ['campaignIdFilter' => ['include' => ['c1']]]);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/v4/adGroups/list', SponsoredBrandsMethods::CT_AD_GROUP, body: ['campaignIdFilter' => ['include' => ['c1']]]);
    });

    it('updateAds / deleteAds / listAds usam media type de ad', function () {
        Http::fake(['advertising-api.amazon.com/sb/v4/ads*' => Http::response(['ads' => []])]);
        $m = adsSbMethods();

        $m->updateAds('111', [['adId' => 'a1', 'state' => 'PAUSED']]);
        adsSbAssertSent('PUT', ADS_SB_HOST.'/sb/v4/ads', SponsoredBrandsMethods::CT_AD, body: ['ads' => [['adId' => 'a1', 'state' => 'PAUSED']]]);

        $m->deleteAds('111', ['a1']);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/v4/ads/delete', SponsoredBrandsMethods::CT_AD, body: ['adIdFilter' => ['include' => ['a1']]]);

        $m->listAds('111', ['adGroupIdFilter' => ['include' => ['g1']], 'creativeVersionToReturn' => 'LATEST']);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/v4/ads/list', SponsoredBrandsMethods::CT_AD, body: ['adGroupIdFilter' => ['include' => ['g1']], 'creativeVersionToReturn' => 'LATEST']);
    });

    it('create*Ads: um POST por formato em /sb/v4/ads/{formato}', function (string $method, string $segment) {
        Http::fake(['advertising-api.amazon.com/sb/v4/ads/*' => Http::response(['ads' => ['success' => [['adId' => 'a1']]]])]);
        $ads = [['adGroupId' => 'g1', 'name' => 'ad', 'state' => 'ENABLED', 'creative' => ['brandName' => 'Soldiers']]];

        $out = adsSbMethods()->{$method}('111', $ads);

        expect($out['ads']['success'][0]['adId'])->toBe('a1');
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/v4/ads/'.$segment, SponsoredBrandsMethods::CT_AD, body: ['ads' => $ads]);
    })->with([
        ['createProductCollectionAds', 'productCollection'],
        ['createExtendedProductCollectionAds', 'productCollectionExtended'],
        ['createStoreSpotlightAds', 'storeSpotlight'],
        ['createVideoAds', 'video'],
        ['createBrandVideoAds', 'brandVideo'],
        ['createAutoCollectionAds', 'autoCollection'],
        ['createManualCollectionAds', 'manualCollection'],
    ]);
});

describe('SponsoredBrandsMethods — creatives', function () {
    it('listCreatives POST /sb/ads/creatives/list com adId + filtros', function () {
        Http::fake(['advertising-api.amazon.com/sb/ads/creatives/list' => Http::response(['creatives' => []])]);
        adsSbMethods()->listCreatives('111', 'a1', ['creativeStatusFilter' => ['APPROVED'], 'maxResults' => 10]);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/ads/creatives/list', SponsoredBrandsMethods::CT_CREATIVE, body: ['adId' => 'a1', 'creativeStatusFilter' => ['APPROVED'], 'maxResults' => 10]);
    });

    it('create*Creative: POST /sb/ads/creatives/{formato} com adId + creative', function (string $method, string $segment) {
        Http::fake(['advertising-api.amazon.com/sb/ads/creatives/*' => Http::response(['creativeId' => 'cr1'])]);
        $creative = ['brandName' => 'Soldiers', 'headline' => 'Whey'];

        adsSbMethods()->{$method}('111', 'a1', $creative);

        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/ads/creatives/'.$segment, SponsoredBrandsMethods::CT_CREATIVE, body: ['adId' => 'a1', 'creative' => $creative]);
    })->with([
        ['createProductCollectionCreative', 'productCollection'],
        ['createExtendedProductCollectionCreative', 'productCollectionExtended'],
        ['createStoreSpotlightCreative', 'storeSpotlight'],
        ['createVideoCreative', 'video'],
        ['createBrandVideoCreative', 'brandVideo'],
    ]);

    it('updateAutoCollectionAdsCreative / updateManualCollectionAdsCreative em lote com media type de ad', function () {
        Http::fake(['advertising-api.amazon.com/sb/ads/creatives/*' => Http::response(['ads' => []])]);
        $ads = [['adId' => 'a1', 'creative' => ['headline' => 'x']]];
        $m = adsSbMethods();

        $m->updateAutoCollectionAdsCreative('111', $ads);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/ads/creatives/autoCollection', SponsoredBrandsMethods::CT_AD, body: ['ads' => $ads]);

        $m->updateManualCollectionAdsCreative('111', $ads);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/ads/creatives/manualCollection', SponsoredBrandsMethods::CT_AD, body: ['ads' => $ads]);
    });
});

describe('SponsoredBrandsMethods — budget rules / usage / recommendations', function () {
    it('getBudgetRules GET /sb/budgetRules?pageSize&nextToken', function () {
        Http::fake(['advertising-api.amazon.com/sb/budgetRules*' => Http::response(['associatedRules' => []])]);
        adsSbMethods()->getBudgetRules('111', 20, 'tok');
        adsSbAssertSent('GET', ADS_SB_HOST.'/sb/budgetRules?pageSize=20&nextToken=tok', null, 'application/json');
    });

    it('createBudgetRules POST e updateBudgetRules PUT /sb/budgetRules', function () {
        Http::fake(['advertising-api.amazon.com/sb/budgetRules' => Http::response(['responses' => []])]);
        $m = adsSbMethods();

        $m->createBudgetRules('111', [['name' => 'BF', 'ruleType' => 'SCHEDULE']]);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/budgetRules', 'application/json', body: ['budgetRulesDetails' => [['name' => 'BF', 'ruleType' => 'SCHEDULE']]]);

        $m->updateBudgetRules('111', [['ruleId' => 'r1', 'ruleState' => 'PAUSED']]);
        adsSbAssertSent('PUT', ADS_SB_HOST.'/sb/budgetRules', 'application/json', body: ['budgetRulesDetails' => [['ruleId' => 'r1', 'ruleState' => 'PAUSED']]]);
    });

    it('getBudgetRule e getCampaignsForBudgetRule GET por ruleId', function () {
        Http::fake(['advertising-api.amazon.com/sb/budgetRules/*' => Http::response(['ruleId' => 'r 1'])]);
        $m = adsSbMethods();

        $m->getBudgetRule('111', 'r 1');
        adsSbAssertSent('GET', ADS_SB_HOST.'/sb/budgetRules/r%201', null, 'application/json');

        $m->getCampaignsForBudgetRule('111', 'r 1', 30);
        adsSbAssertSent('GET', ADS_SB_HOST.'/sb/budgetRules/r%201/campaigns?pageSize=30', null, 'application/json');
    });

    it('listCampaignBudgetRules / associateBudgetRules / disassociateBudgetRule em /sb/campaigns/{id}/budgetRules', function () {
        Http::fake(['advertising-api.amazon.com/sb/campaigns/*' => Http::response(['associatedRules' => []])]);
        $m = adsSbMethods();

        $m->listCampaignBudgetRules('111', 'c1');
        adsSbAssertSent('GET', ADS_SB_HOST.'/sb/campaigns/c1/budgetRules', null, 'application/json');

        $m->associateBudgetRules('111', 'c1', ['r1', 'r2']);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/campaigns/c1/budgetRules', 'application/json', body: ['budgetRuleIds' => ['r1', 'r2']]);

        $m->disassociateBudgetRule('111', 'c1', 'r1');
        adsSbAssertSent('DELETE', ADS_SB_HOST.'/sb/campaigns/c1/budgetRules/r1', null, 'application/json');
    });

    it('getBudgetRulesRecommendation POST com media type v3 e Amazon-Ads-AccountId opcional', function () {
        Http::fake(['advertising-api.amazon.com/sb/campaigns/budgetRules/recommendations' => Http::response(['recommendations' => []])]);
        adsSbMethods()->getBudgetRulesRecommendation('111', 'c1', accountId: 'acct-9');
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/campaigns/budgetRules/recommendations', 'application/vnd.sbbudgetrulesrecommendation.v3+json', body: ['recommendationType' => 'EVENTS_FOR_EXISTING_CAMPAIGN', 'campaignId' => 'c1'], extraHeaders: ['Amazon-Ads-AccountId' => 'acct-9']);
    });

    it('campaignsBudgetUsage POST /sb/campaigns/budget/usage (v1)', function () {
        Http::fake(['advertising-api.amazon.com/sb/campaigns/budget/usage' => Http::response(['success' => []])]);
        adsSbMethods()->campaignsBudgetUsage('111', ['c1']);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/campaigns/budget/usage', 'application/vnd.sbcampaignbudgetusage.v1+json', body: ['campaignIds' => ['c1']]);
    });

    it('getBudgetRecommendations POST /sb/campaigns/budgetRecommendations (v4)', function () {
        Http::fake(['advertising-api.amazon.com/sb/campaigns/budgetRecommendations' => Http::response(['recommendations' => []])]);
        adsSbMethods()->getBudgetRecommendations('111', ['c1', 'c2']);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/campaigns/budgetRecommendations', 'application/vnd.sbbudgetrecommendation.v4+json', body: ['campaignIds' => ['c1', 'c2']]);
    });
});

describe('SponsoredBrandsMethods — insights / forecasts / recomendações', function () {
    it('campaignInsights POST /sb/campaigns/insights?nextToken', function () {
        Http::fake(['advertising-api.amazon.com/sb/campaigns/insights*' => Http::response(['insights' => []])]);
        $adGroups = [['adFormat' => 'PRODUCT_COLLECTION', 'keywords' => [['keywordText' => 'whey', 'matchType' => 'BROAD']]]];
        adsSbMethods()->campaignInsights('111', $adGroups, 'tok');
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/campaigns/insights?nextToken=tok', 'application/vnd.sbinsights.v4+json', body: ['adGroups' => $adGroups]);
    });

    it('campaignPerformanceForecasts POST /sb/forecasts', function () {
        Http::fake(['advertising-api.amazon.com/sb/forecasts' => Http::response(['forecasts' => []])]);
        $campaigns = [['forecastType' => 'CAMPAIGN', 'budget' => 50, 'adGroups' => []]];
        adsSbMethods()->campaignPerformanceForecasts('111', $campaigns);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/forecasts', 'application/vnd.sbforecasting.v4+json', body: ['campaigns' => $campaigns]);
    });

    it('getHeadlineRecommendations POST /sb/recommendations/creative/headline', function () {
        Http::fake(['advertising-api.amazon.com/sb/recommendations/creative/headline' => Http::response(['recommendations' => []])]);
        adsSbMethods()->getHeadlineRecommendations('111', ['asins' => ['B01'], 'maxNumSuggestions' => 3]);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/recommendations/creative/headline', 'application/json', body: ['asins' => ['B01'], 'maxNumSuggestions' => 3]);
    });

    it('getKeywordRecommendations POST /sb/recommendations/keyword (v3)', function () {
        Http::fake(['advertising-api.amazon.com/sb/recommendations/keyword' => Http::response(['recommendations' => []])]);
        adsSbMethods()->getKeywordRecommendations('111', ['asins' => ['B01'], 'creativeType' => 'PRODUCT_COLLECTION']);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/recommendations/keyword', 'application/vnd.sbkeywordrecommendation.v3+json', body: ['asins' => ['B01'], 'creativeType' => 'PRODUCT_COLLECTION']);
    });

    it('getOptimizationRecommendation POST /sb/recommendations/optimization (v4)', function () {
        Http::fake(['advertising-api.amazon.com/sb/recommendations/optimization' => Http::response(['recommendation' => []])]);
        adsSbMethods()->getOptimizationRecommendation('111', 'ROAS', [['pageType' => 'STORE', 'url' => 'https://amazon.com/stores/x']]);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/recommendations/optimization', 'application/vnd.sboptimizationrecommendationresource.v4+json', body: ['costControlMetric' => 'ROAS', 'landingPages' => [['pageType' => 'STORE', 'url' => 'https://amazon.com/stores/x']]]);
    });

    it('getNegativeBrandsRecommendations GET /sb/negativeTargets/brands/recommendations com Accept de targeting', function () {
        Http::fake(['advertising-api.amazon.com/sb/negativeTargets/brands/recommendations*' => Http::response(['brands' => []])]);
        adsSbMethods()->getNegativeBrandsRecommendations('111', 'tok');
        adsSbAssertSent('GET', ADS_SB_HOST.'/sb/negativeTargets/brands/recommendations?nextToken=tok', null, SponsoredBrandsMethods::CT_TARGETING);
    });
});

describe('SponsoredBrandsMethods — optimization rules v4', function () {
    it('create/update/associate/disassociate/list em /sb/rules/optimization', function () {
        Http::fake(['advertising-api.amazon.com/sb/rules/optimization*' => Http::response(['optimizationRules' => []])]);
        $m = adsSbMethods();
        $ct = SponsoredBrandsMethods::CT_RULE_OPTIMIZATION;

        $m->createOptimizationRules('111', [['entityType' => 'CAMPAIGN', 'entityId' => 'c1', 'conditions' => []]]);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/rules/optimization', $ct, body: ['optimizationRules' => [['entityType' => 'CAMPAIGN', 'entityId' => 'c1', 'conditions' => []]]]);

        $m->updateOptimizationRules('111', [['optimizationRuleId' => 'o1', 'conditions' => []]]);
        adsSbAssertSent('PUT', ADS_SB_HOST.'/sb/rules/optimization', $ct, body: ['optimizationRules' => [['optimizationRuleId' => 'o1', 'conditions' => []]]]);

        $assoc = [['entityType' => 'CAMPAIGN', 'entityId' => 'c1', 'optimizationRuleId' => 'o1']];
        $m->associateOptimizationRules('111', $assoc);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/rules/optimization/associate', $ct, body: ['optimizationRuleAssociations' => $assoc]);

        $m->disassociateOptimizationRules('111', $assoc);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/rules/optimization/disassociate', $ct, body: ['optimizationRuleDisassociations' => $assoc]);

        $m->listOptimizationRules('111', ['entityFilter' => ['entityType' => 'CAMPAIGN', 'entityId' => 'c1']]);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/rules/optimization/list', $ct, body: ['entityFilter' => ['entityType' => 'CAMPAIGN', 'entityId' => 'c1']]);
    });
});

describe('SponsoredBrandsMethods — targeting v4', function () {
    it('getTargetableCategories GET /sb/targets/categories?supplySource', function () {
        Http::fake(['advertising-api.amazon.com/sb/targets/categories*' => Http::response(['categories' => []])]);
        adsSbMethods()->getTargetableCategories('111', 'AMAZON', ['includeOnlyRootCategories' => 'true']);
        adsSbAssertSent('GET', ADS_SB_HOST.'/sb/targets/categories?supplySource=AMAZON&includeOnlyRootCategories=true', null, SponsoredBrandsMethods::CT_TARGETING);
    });

    it('getRefinementsForCategory GET /sb/targets/categories/{id}/refinements', function () {
        Http::fake(['advertising-api.amazon.com/sb/targets/categories/*' => Http::response(['brands' => []])]);
        adsSbMethods()->getRefinementsForCategory('111', '123', ['locale' => 'pt_BR']);
        adsSbAssertSent('GET', ADS_SB_HOST.'/sb/targets/categories/123/refinements?locale=pt_BR', null, SponsoredBrandsMethods::CT_TARGETING);
    });

    it('getTargetableAsinCounts POST /sb/targets/products/count', function () {
        Http::fake(['advertising-api.amazon.com/sb/targets/products/count' => Http::response(['count' => 42])]);
        $out = adsSbMethods()->getTargetableAsinCounts('111', '123', ['brands' => ['b1'], 'isPrimeShipping' => true]);
        expect($out['count'])->toBe(42);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/targets/products/count', SponsoredBrandsMethods::CT_TARGETING, body: ['category' => '123', 'brands' => ['b1'], 'isPrimeShipping' => true]);
    });
});

describe('SponsoredBrandsMethods — migração legada', function () {
    it('startMigrationJob / migrationJobResults / migrationJobStatus / migrationResults', function () {
        Http::fake(['advertising-api.amazon.com/sb/v4/legacyCampaigns/*' => Http::response(['jobId' => 'j1'])]);
        $m = adsSbMethods();
        $ct = SponsoredBrandsMethods::CT_MIGRATION;

        $m->startMigrationJob('111', ['c1'], true, ['isStagedMigration' => false]);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/v4/legacyCampaigns/migrationJob', $ct, body: ['campaignIds' => ['c1'], 'enableThemeTargeting' => true, 'isStagedMigration' => false]);

        $m->migrationJobResults('111', 'j1', 'tok');
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/v4/legacyCampaigns/migrationJob/results', $ct, body: ['jobId' => 'j1', 'nextToken' => 'tok']);

        $m->migrationJobStatus('111', 'j1');
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/v4/legacyCampaigns/migrationJob/status', $ct, body: ['jobId' => 'j1']);

        $m->migrationResults('111', 'tok');
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/v4/legacyCampaigns/overallMigrationResults', $ct, body: ['nextToken' => 'tok']);
    });

    it('listMigrations POST /sb/v4/migrations/list (sbAdMigration)', function () {
        Http::fake(['advertising-api.amazon.com/sb/v4/migrations/list' => Http::response(['migrations' => []])]);
        adsSbMethods()->listMigrations('111', ['adIdFilter' => ['include' => ['a1']]]);
        adsSbAssertSent('POST', ADS_SB_HOST.'/sb/v4/migrations/list', 'application/vnd.sbAdMigration.v4+json', body: ['adIdFilter' => ['include' => ['a1']]]);
    });
});

describe('SponsoredBrandsMethods — benchmark + pré-moderação', function () {
    it('getBenchmarkBrands GET /benchmarks/brands', function () {
        Http::fake(['advertising-api.amazon.com/benchmarks/brands*' => Http::response(['brands' => []])]);
        adsSbMethods()->getBenchmarkBrands('111', 'SB', 'p2');
        adsSbAssertSent('GET', ADS_SB_HOST.'/benchmarks/brands?programType=SB&nextPageToken=p2', null, 'application/vnd.brandlist.v1+json');
    });

    it('getBenchmarkTimeSeries POST /benchmarks/brands/{brand}/categories/{cat}', function () {
        Http::fake(['advertising-api.amazon.com/benchmarks/brands/*' => Http::response(['data' => []])]);
        adsSbMethods()->getBenchmarkTimeSeries('111', 'Soldiers Nutrition', '77', ['ROAS'], ['granularity' => 'WEEKLY']);
        adsSbAssertSent('POST', ADS_SB_HOST.'/benchmarks/brands/Soldiers%20Nutrition/categories/77', 'application/vnd.timeseriesdata.v1+json', body: ['metrics' => ['ROAS'], 'granularity' => 'WEEKLY']);
    });

    it('getBenchmarkReportData POST /benchmarks/brandsAndCategories', function () {
        Http::fake(['advertising-api.amazon.com/benchmarks/brandsAndCategories' => Http::response(['data' => []])]);
        adsSbMethods()->getBenchmarkReportData('111', ['metrics' => ['CTR'], 'startDate' => '2026-08-01']);
        adsSbAssertSent('POST', ADS_SB_HOST.'/benchmarks/brandsAndCategories', 'application/vnd.reportdata.v1+json', body: ['metrics' => ['CTR'], 'startDate' => '2026-08-01']);
    });

    it('preModeration POST /preModeration com Amazon-Ads-AccountId', function () {
        Http::fake(['advertising-api.amazon.com/preModeration' => Http::response(['preModerationStatus' => 'APPROVED'])]);
        $body = ['adProgram' => 'SB', 'locale' => 'pt_BR', 'textComponents' => [['id' => 't1', 'componentType' => 'HEADLINE', 'text' => 'Whey']]];
        $out = adsSbMethods()->preModeration('111', $body, 'acct-9');
        expect($out['preModerationStatus'])->toBe('APPROVED');
        adsSbAssertSent('POST', ADS_SB_HOST.'/preModeration', 'application/json', body: $body, extraHeaders: ['Amazon-Ads-AccountId' => 'acct-9']);
    });

    it('erro 4xx vira AmazonAdsRequestException', function () {
        Http::fake(['advertising-api.amazon.com/sb/v4/campaigns/list' => Http::response(['message' => 'Unsupported media type'], 415)]);
        adsSbMethods()->listCampaigns('111');
    })->throws(AmazonAdsRequestException::class, 'Unsupported media type');
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\SponsoredDisplay\SponsoredDisplayMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

const ADS_SD_BASE = 'https://advertising-api.amazon.com';
const ADS_SD_PROFILE = '111';

beforeEach(function () {
    Http::preventStrayRequests();
});

function adsSdIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ads-access-token',
        refreshToken: 'ads-refresh',
        settings: [],
        active: true,
        expired: false,
    );
}

function adsSd(): SponsoredDisplayMethods
{
    return new SponsoredDisplayMethods(adsSdIntegration(), 'client-abc');
}

/**
 * Confere verbo + URL completa + headers de auth/scope, e opcionalmente
 * body e media types (Content-Type/Accept).
 */
function adsSdAssertSent(
    string $method,
    string $url,
    ?array $body = null,
    ?string $contentType = null,
    ?string $accept = null,
    bool $scope = true,
): void {
    Http::assertSent(function ($r) use ($method, $url, $body, $contentType, $accept, $scope) {
        if ($r->method() !== $method || $r->url() !== $url) {
            return false;
        }
        if (! $r->hasHeader('Amazon-Advertising-API-ClientId', 'client-abc')
            || ! $r->hasHeader('Authorization', 'Bearer ads-access-token')) {
            return false;
        }
        if ($scope && ! $r->hasHeader('Amazon-Advertising-API-Scope', ADS_SD_PROFILE)) {
            return false;
        }
        if ($contentType !== null && ! $r->hasHeader('Content-Type', $contentType)) {
            return false;
        }
        if ($accept !== null && ! $r->hasHeader('Accept', $accept)) {
            return false;
        }
        if ($body !== null && json_decode($r->body(), true) != $body) { // loose: 1.0 pode virar 1 no json
            return false;
        }

        return true;
    });
}

// ---------------------------------------------------------------- campaigns

describe('SponsoredDisplay campaigns', function () {
    it('listCampaigns GET /sd/campaigns com filtros', function () {
        Http::fake([ADS_SD_BASE.'/sd/campaigns*' => Http::response([['campaignId' => 1]])]);

        $out = adsSd()->listCampaigns(ADS_SD_PROFILE, ['stateFilter' => 'enabled', 'count' => 10]);

        expect($out)->toHaveCount(1);
        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/campaigns?stateFilter=enabled&count=10');
    });

    it('listCampaignsEx GET /sd/campaigns/extended', function () {
        Http::fake([ADS_SD_BASE.'/sd/campaigns/extended*' => Http::response([])]);

        adsSd()->listCampaignsEx(ADS_SD_PROFILE, ['campaignIdFilter' => '1,2']);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/campaigns/extended?campaignIdFilter=1%2C2');
    });

    it('createCampaigns POST /sd/campaigns com lista no body', function () {
        Http::fake([ADS_SD_BASE.'/sd/campaigns' => Http::response([['code' => 'SUCCESS', 'campaignId' => 9]])]);

        $body = [['name' => 'C1', 'tactic' => 'T00020', 'budgetType' => 'daily', 'budget' => 10, 'startDate' => '20260901', 'state' => 'enabled']];
        $out = adsSd()->createCampaigns(ADS_SD_PROFILE, $body);

        expect($out[0]['campaignId'])->toBe(9);
        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/campaigns', $body, 'application/json');
    });

    it('updateCampaigns PUT /sd/campaigns', function () {
        Http::fake([ADS_SD_BASE.'/sd/campaigns' => Http::response([['code' => 'SUCCESS']])]);

        $body = [['campaignId' => 9, 'state' => 'paused']];
        adsSd()->updateCampaigns(ADS_SD_PROFILE, $body);

        adsSdAssertSent('PUT', ADS_SD_BASE.'/sd/campaigns', $body, 'application/json');
    });

    it('getCampaign GET /sd/campaigns/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/campaigns/9' => Http::response(['campaignId' => 9])]);

        expect(adsSd()->getCampaign(ADS_SD_PROFILE, '9')['campaignId'])->toBe(9);
        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/campaigns/9');
    });

    it('getCampaignResponseEx GET /sd/campaigns/extended/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/campaigns/extended/9' => Http::response(['campaignId' => 9])]);

        adsSd()->getCampaignResponseEx(ADS_SD_PROFILE, '9');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/campaigns/extended/9');
    });

    it('archiveCampaign DELETE /sd/campaigns/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/campaigns/9' => Http::response(['code' => 'SUCCESS'])]);

        adsSd()->archiveCampaign(ADS_SD_PROFILE, '9');

        adsSdAssertSent('DELETE', ADS_SD_BASE.'/sd/campaigns/9');
    });
});

// ---------------------------------------------------------------- ad groups

describe('SponsoredDisplay adGroups', function () {
    it('listAdGroups GET /sd/adGroups', function () {
        Http::fake([ADS_SD_BASE.'/sd/adGroups*' => Http::response([])]);

        adsSd()->listAdGroups(ADS_SD_PROFILE, ['campaignIdFilter' => '9']);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/adGroups?campaignIdFilter=9');
    });

    it('listAdGroupsEx GET /sd/adGroups/extended', function () {
        Http::fake([ADS_SD_BASE.'/sd/adGroups/extended*' => Http::response([])]);

        adsSd()->listAdGroupsEx(ADS_SD_PROFILE);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/adGroups/extended');
    });

    it('createAdGroups POST /sd/adGroups', function () {
        Http::fake([ADS_SD_BASE.'/sd/adGroups' => Http::response([['adGroupId' => 5]])]);

        $body = [['campaignId' => 9, 'name' => 'AG', 'defaultBid' => 1.5, 'state' => 'enabled']];
        adsSd()->createAdGroups(ADS_SD_PROFILE, $body);

        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/adGroups', $body, 'application/json');
    });

    it('updateAdGroups PUT /sd/adGroups', function () {
        Http::fake([ADS_SD_BASE.'/sd/adGroups' => Http::response([])]);

        $body = [['adGroupId' => 5, 'defaultBid' => 2.0]];
        adsSd()->updateAdGroups(ADS_SD_PROFILE, $body);

        adsSdAssertSent('PUT', ADS_SD_BASE.'/sd/adGroups', $body, 'application/json');
    });

    it('getAdGroup GET /sd/adGroups/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/adGroups/5' => Http::response(['adGroupId' => 5])]);

        adsSd()->getAdGroup(ADS_SD_PROFILE, '5');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/adGroups/5');
    });

    it('getAdGroupResponseEx GET /sd/adGroups/extended/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/adGroups/extended/5' => Http::response(['adGroupId' => 5])]);

        adsSd()->getAdGroupResponseEx(ADS_SD_PROFILE, '5');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/adGroups/extended/5');
    });

    it('archiveAdGroup DELETE /sd/adGroups/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/adGroups/5' => Http::response(['code' => 'SUCCESS'])]);

        adsSd()->archiveAdGroup(ADS_SD_PROFILE, '5');

        adsSdAssertSent('DELETE', ADS_SD_BASE.'/sd/adGroups/5');
    });
});

// ---------------------------------------------------------------- product ads

describe('SponsoredDisplay productAds', function () {
    it('listProductAds GET /sd/productAds', function () {
        Http::fake([ADS_SD_BASE.'/sd/productAds*' => Http::response([])]);

        adsSd()->listProductAds(ADS_SD_PROFILE, ['adGroupIdFilter' => '5']);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/productAds?adGroupIdFilter=5');
    });

    it('listProductAdsEx GET /sd/productAds/extended', function () {
        Http::fake([ADS_SD_BASE.'/sd/productAds/extended*' => Http::response([])]);

        adsSd()->listProductAdsEx(ADS_SD_PROFILE);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/productAds/extended');
    });

    it('createProductAds POST /sd/productAds', function () {
        Http::fake([ADS_SD_BASE.'/sd/productAds' => Http::response([['adId' => 7]])]);

        $body = [['adGroupId' => 5, 'sku' => 'SKU-1', 'state' => 'enabled']];
        adsSd()->createProductAds(ADS_SD_PROFILE, $body);

        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/productAds', $body, 'application/json');
    });

    it('updateProductAds PUT /sd/productAds', function () {
        Http::fake([ADS_SD_BASE.'/sd/productAds' => Http::response([])]);

        $body = [['adId' => 7, 'state' => 'paused']];
        adsSd()->updateProductAds(ADS_SD_PROFILE, $body);

        adsSdAssertSent('PUT', ADS_SD_BASE.'/sd/productAds', $body, 'application/json');
    });

    it('getProductAd GET /sd/productAds/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/productAds/7' => Http::response(['adId' => 7])]);

        adsSd()->getProductAd(ADS_SD_PROFILE, '7');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/productAds/7');
    });

    it('getProductAdResponseEx GET /sd/productAds/extended/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/productAds/extended/7' => Http::response(['adId' => 7])]);

        adsSd()->getProductAdResponseEx(ADS_SD_PROFILE, '7');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/productAds/extended/7');
    });

    it('archiveProductAd DELETE /sd/productAds/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/productAds/7' => Http::response(['code' => 'SUCCESS'])]);

        adsSd()->archiveProductAd(ADS_SD_PROFILE, '7');

        adsSdAssertSent('DELETE', ADS_SD_BASE.'/sd/productAds/7');
    });
});

// ---------------------------------------------------------------- targets

describe('SponsoredDisplay targets', function () {
    it('listTargetingClauses GET /sd/targets', function () {
        Http::fake([ADS_SD_BASE.'/sd/targets*' => Http::response([])]);

        adsSd()->listTargetingClauses(ADS_SD_PROFILE, ['stateFilter' => 'enabled']);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/targets?stateFilter=enabled');
    });

    it('listTargetingClausesEx GET /sd/targets/extended', function () {
        Http::fake([ADS_SD_BASE.'/sd/targets/extended*' => Http::response([])]);

        adsSd()->listTargetingClausesEx(ADS_SD_PROFILE);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/targets/extended');
    });

    it('createTargetingClauses POST /sd/targets', function () {
        Http::fake([ADS_SD_BASE.'/sd/targets' => Http::response([['targetId' => 3]])]);

        $body = [['adGroupId' => 5, 'state' => 'enabled', 'expressionType' => 'manual', 'expression' => [['type' => 'asinSameAs', 'value' => 'B000']], 'bid' => 1.0]];
        adsSd()->createTargetingClauses(ADS_SD_PROFILE, $body);

        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/targets', $body, 'application/json');
    });

    it('updateTargetingClauses PUT /sd/targets', function () {
        Http::fake([ADS_SD_BASE.'/sd/targets' => Http::response([])]);

        $body = [['targetId' => 3, 'bid' => 2.0]];
        adsSd()->updateTargetingClauses(ADS_SD_PROFILE, $body);

        adsSdAssertSent('PUT', ADS_SD_BASE.'/sd/targets', $body, 'application/json');
    });

    it('getTargets GET /sd/targets/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/targets/3' => Http::response(['targetId' => 3])]);

        adsSd()->getTargets(ADS_SD_PROFILE, '3');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/targets/3');
    });

    it('getTargetsEx GET /sd/targets/extended/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/targets/extended/3' => Http::response(['targetId' => 3])]);

        adsSd()->getTargetsEx(ADS_SD_PROFILE, '3');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/targets/extended/3');
    });

    it('archiveTargetingClause DELETE /sd/targets/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/targets/3' => Http::response(['code' => 'SUCCESS'])]);

        adsSd()->archiveTargetingClause(ADS_SD_PROFILE, '3');

        adsSdAssertSent('DELETE', ADS_SD_BASE.'/sd/targets/3');
    });
});

// ---------------------------------------------------------------- negative targets

describe('SponsoredDisplay negativeTargets', function () {
    it('listNegativeTargetingClauses GET /sd/negativeTargets', function () {
        Http::fake([ADS_SD_BASE.'/sd/negativeTargets*' => Http::response([])]);

        adsSd()->listNegativeTargetingClauses(ADS_SD_PROFILE, ['adGroupIdFilter' => '5']);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/negativeTargets?adGroupIdFilter=5');
    });

    it('listNegativeTargetingClausesEx GET /sd/negativeTargets/extended', function () {
        Http::fake([ADS_SD_BASE.'/sd/negativeTargets/extended*' => Http::response([])]);

        adsSd()->listNegativeTargetingClausesEx(ADS_SD_PROFILE);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/negativeTargets/extended');
    });

    it('createNegativeTargetingClauses POST /sd/negativeTargets', function () {
        Http::fake([ADS_SD_BASE.'/sd/negativeTargets' => Http::response([['targetId' => 4]])]);

        $body = [['adGroupId' => 5, 'state' => 'enabled', 'expressionType' => 'manual', 'expression' => [['type' => 'asinSameAs', 'value' => 'B001']]]];
        adsSd()->createNegativeTargetingClauses(ADS_SD_PROFILE, $body);

        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/negativeTargets', $body, 'application/json');
    });

    it('updateNegativeTargetingClauses PUT /sd/negativeTargets', function () {
        Http::fake([ADS_SD_BASE.'/sd/negativeTargets' => Http::response([])]);

        $body = [['targetId' => 4, 'state' => 'paused']];
        adsSd()->updateNegativeTargetingClauses(ADS_SD_PROFILE, $body);

        adsSdAssertSent('PUT', ADS_SD_BASE.'/sd/negativeTargets', $body, 'application/json');
    });

    it('getNegativeTargets GET /sd/negativeTargets/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/negativeTargets/4' => Http::response(['targetId' => 4])]);

        adsSd()->getNegativeTargets(ADS_SD_PROFILE, '4');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/negativeTargets/4');
    });

    it('getNegativeTargetsEx GET /sd/negativeTargets/extended/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/negativeTargets/extended/4' => Http::response(['targetId' => 4])]);

        adsSd()->getNegativeTargetsEx(ADS_SD_PROFILE, '4');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/negativeTargets/extended/4');
    });

    it('archiveNegativeTargetingClause DELETE /sd/negativeTargets/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/negativeTargets/4' => Http::response(['code' => 'SUCCESS'])]);

        adsSd()->archiveNegativeTargetingClause(ADS_SD_PROFILE, '4');

        adsSdAssertSent('DELETE', ADS_SD_BASE.'/sd/negativeTargets/4');
    });
});

// ---------------------------------------------------------------- creatives

describe('SponsoredDisplay creatives', function () {
    it('listCreatives GET /sd/creatives', function () {
        Http::fake([ADS_SD_BASE.'/sd/creatives*' => Http::response([])]);

        adsSd()->listCreatives(ADS_SD_PROFILE, ['adGroupIdFilter' => '5']);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/creatives?adGroupIdFilter=5');
    });

    it('createCreatives POST /sd/creatives', function () {
        Http::fake([ADS_SD_BASE.'/sd/creatives' => Http::response([['creativeId' => 'cr1']])]);

        $body = [['adGroupId' => 5, 'creativeType' => 'IMAGE', 'properties' => ['headline' => 'Oi']]];
        adsSd()->createCreatives(ADS_SD_PROFILE, $body);

        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/creatives', $body, 'application/json');
    });

    it('updateCreatives PUT /sd/creatives', function () {
        Http::fake([ADS_SD_BASE.'/sd/creatives' => Http::response([])]);

        $body = [['creativeId' => 'cr1', 'creativeType' => 'IMAGE', 'properties' => ['headline' => 'Novo']]];
        adsSd()->updateCreatives(ADS_SD_PROFILE, $body);

        adsSdAssertSent('PUT', ADS_SD_BASE.'/sd/creatives', $body, 'application/json');
    });

    it('postCreativePreview POST /sd/creatives/preview', function () {
        Http::fake([ADS_SD_BASE.'/sd/creatives/preview' => Http::response(['previews' => []])]);

        $body = ['creative' => ['creativeType' => 'IMAGE'], 'previewConfiguration' => ['adSize' => '300x250']];
        adsSd()->postCreativePreview(ADS_SD_PROFILE, $body);

        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/creatives/preview', $body, 'application/json');
    });

    it('listCreativeModerations GET /sd/moderation/creatives com language obrigatório', function () {
        Http::fake([ADS_SD_BASE.'/sd/moderation/creatives*' => Http::response(['creativeModerations' => []])]);

        adsSd()->listCreativeModerations(ADS_SD_PROFILE, 'pt_BR', ['count' => 5]);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/moderation/creatives?language=pt_BR&count=5');
    });
});

// ---------------------------------------------------------------- optimization rules

describe('SponsoredDisplay optimizationRules', function () {
    it('listOptimizationRules GET /sd/optimizationRules', function () {
        Http::fake([ADS_SD_BASE.'/sd/optimizationRules*' => Http::response([])]);

        adsSd()->listOptimizationRules(ADS_SD_PROFILE, ['adGroupIdFilter' => '5']);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/optimizationRules?adGroupIdFilter=5');
    });

    it('createOptimizationRules POST /sd/optimizationRules', function () {
        Http::fake([ADS_SD_BASE.'/sd/optimizationRules' => Http::response([['optimizationRuleId' => 'r1']])]);

        $body = [['ruleName' => 'R', 'ruleType' => 'BIDDING', 'ruleConditions' => []]];
        adsSd()->createOptimizationRules(ADS_SD_PROFILE, $body);

        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/optimizationRules', $body, 'application/json');
    });

    it('updateOptimizationRules PUT /sd/optimizationRules', function () {
        Http::fake([ADS_SD_BASE.'/sd/optimizationRules' => Http::response([])]);

        $body = [['optimizationRuleId' => 'r1', 'ruleName' => 'R2']];
        adsSd()->updateOptimizationRules(ADS_SD_PROFILE, $body);

        adsSdAssertSent('PUT', ADS_SD_BASE.'/sd/optimizationRules', $body, 'application/json');
    });

    it('getOptimizationRule GET /sd/optimizationRules/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/optimizationRules/r1' => Http::response(['optimizationRuleId' => 'r1'])]);

        adsSd()->getOptimizationRule(ADS_SD_PROFILE, 'r1');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/optimizationRules/r1');
    });

    it('listAdGroupOptimizationRules GET /sd/adGroups/{id}/optimizationRules', function () {
        Http::fake([ADS_SD_BASE.'/sd/adGroups/5/optimizationRules' => Http::response(['optimizationRules' => []])]);

        adsSd()->listAdGroupOptimizationRules(ADS_SD_PROFILE, '5');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/adGroups/5/optimizationRules');
    });

    it('associateOptimizationRulesWithAdGroup POST /sd/adGroups/{id}/optimizationRules', function () {
        Http::fake([ADS_SD_BASE.'/sd/adGroups/5/optimizationRules' => Http::response(['code' => 'SUCCESS'])]);

        adsSd()->associateOptimizationRulesWithAdGroup(ADS_SD_PROFILE, '5', ['r1', 'r2']);

        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/adGroups/5/optimizationRules', ['optimizationRuleIds' => ['r1', 'r2']], 'application/json');
    });

    it('disassociateOptimizationRulesFromAdGroup POST .../optimizationRules/disassociate', function () {
        Http::fake([ADS_SD_BASE.'/sd/adGroups/5/optimizationRules/disassociate' => Http::response(['code' => 'SUCCESS'])]);

        adsSd()->disassociateOptimizationRulesFromAdGroup(ADS_SD_PROFILE, '5', ['r1']);

        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/adGroups/5/optimizationRules/disassociate', ['optimizationRuleIds' => ['r1']], 'application/json');
    });
});

// ---------------------------------------------------------------- locations

describe('SponsoredDisplay locations', function () {
    it('listLocations GET /sd/locations', function () {
        Http::fake([ADS_SD_BASE.'/sd/locations*' => Http::response(['locations' => []])]);

        adsSd()->listLocations(ADS_SD_PROFILE, ['campaignIdFilter' => '9']);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/locations?campaignIdFilter=9');
    });

    it('createLocations POST /sd/locations', function () {
        Http::fake([ADS_SD_BASE.'/sd/locations' => Http::response(['locations' => []])]);

        $body = [['adGroupId' => 5, 'expression' => [['type' => 'COUNTRY', 'value' => 'BR']]]];
        adsSd()->createLocations(ADS_SD_PROFILE, $body);

        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/locations', $body, 'application/json');
    });

    it('archiveLocations POST /sd/locations/delete com include', function () {
        Http::fake([ADS_SD_BASE.'/sd/locations/delete' => Http::response(['locations' => []])]);

        adsSd()->archiveLocations(ADS_SD_PROFILE, ['l1', 'l2']);

        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/locations/delete', ['locationExpressionIdFilter' => ['include' => ['l1', 'l2']]], 'application/json');
    });
});

// ---------------------------------------------------------------- brand safety

describe('SponsoredDisplay brandSafety', function () {
    it('listDomains GET /sd/brandSafety/deny', function () {
        Http::fake([ADS_SD_BASE.'/sd/brandSafety/deny*' => Http::response(['domains' => []])]);

        adsSd()->listDomains(ADS_SD_PROFILE, ['startIndex' => 0, 'count' => 50]);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/brandSafety/deny?startIndex=0&count=50');
    });

    it('createBrandSafetyDenyListDomains POST /sd/brandSafety/deny', function () {
        Http::fake([ADS_SD_BASE.'/sd/brandSafety/deny' => Http::response(['requestId' => 'req-1'])]);

        $out = adsSd()->createBrandSafetyDenyListDomains(ADS_SD_PROFILE, ['ruim.com']);

        expect($out['requestId'])->toBe('req-1');
        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/brandSafety/deny', ['domains' => ['ruim.com']], 'application/json');
    });

    it('deleteBrandSafetyDenyList DELETE /sd/brandSafety/deny', function () {
        Http::fake([ADS_SD_BASE.'/sd/brandSafety/deny' => Http::response(['requestId' => 'req-2'])]);

        adsSd()->deleteBrandSafetyDenyList(ADS_SD_PROFILE);

        adsSdAssertSent('DELETE', ADS_SD_BASE.'/sd/brandSafety/deny');
    });

    it('listRequestStatus GET /sd/brandSafety/status', function () {
        Http::fake([ADS_SD_BASE.'/sd/brandSafety/status' => Http::response(['requests' => []])]);

        adsSd()->listRequestStatus(ADS_SD_PROFILE);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/brandSafety/status');
    });

    it('getRequestStatus GET /sd/brandSafety/{requestId}/status', function () {
        Http::fake([ADS_SD_BASE.'/sd/brandSafety/req-1/status' => Http::response(['status' => 'COMPLETED'])]);

        adsSd()->getRequestStatus(ADS_SD_PROFILE, 'req-1');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/brandSafety/req-1/status');
    });

    it('getRequestResults GET /sd/brandSafety/{requestId}/results', function () {
        Http::fake([ADS_SD_BASE.'/sd/brandSafety/req-1/results*' => Http::response(['results' => []])]);

        adsSd()->getRequestResults(ADS_SD_PROFILE, 'req-1', ['count' => 10]);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/brandSafety/req-1/results?count=10');
    });
});

// ---------------------------------------------------------------- budget rules

describe('SponsoredDisplay budgetRules', function () {
    it('getBudgetRulesForAdvertiser GET /sd/budgetRules com pageSize + nextToken', function () {
        Http::fake([ADS_SD_BASE.'/sd/budgetRules*' => Http::response(['associatedRules' => [], 'nextToken' => null])]);

        adsSd()->getBudgetRulesForAdvertiser(ADS_SD_PROFILE, 25, 'tok');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/budgetRules?pageSize=25&nextToken=tok');
    });

    it('createBudgetRulesForCampaigns POST /sd/budgetRules', function () {
        Http::fake([ADS_SD_BASE.'/sd/budgetRules' => Http::response(['responses' => []])]);

        $rules = [['name' => 'BR', 'ruleType' => 'SCHEDULE', 'ruleDetails' => [], 'ruleState' => 'ACTIVE']];
        adsSd()->createBudgetRulesForCampaigns(ADS_SD_PROFILE, $rules);

        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/budgetRules', ['budgetRulesDetails' => $rules], 'application/json');
    });

    it('updateBudgetRulesForCampaigns PUT /sd/budgetRules', function () {
        Http::fake([ADS_SD_BASE.'/sd/budgetRules' => Http::response(['responses' => []])]);

        $rules = [['ruleId' => 'br1', 'ruleState' => 'PAUSED']];
        adsSd()->updateBudgetRulesForCampaigns(ADS_SD_PROFILE, $rules);

        adsSdAssertSent('PUT', ADS_SD_BASE.'/sd/budgetRules', ['budgetRulesDetails' => $rules], 'application/json');
    });

    it('getBudgetRuleByRuleId GET /sd/budgetRules/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/budgetRules/br1' => Http::response(['budgetRule' => []])]);

        adsSd()->getBudgetRuleByRuleId(ADS_SD_PROFILE, 'br1');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/budgetRules/br1');
    });

    it('getCampaignsAssociatedWithBudgetRule GET /sd/budgetRules/{id}/campaigns', function () {
        Http::fake([ADS_SD_BASE.'/sd/budgetRules/br1/campaigns*' => Http::response(['campaigns' => []])]);

        adsSd()->getCampaignsAssociatedWithBudgetRule(ADS_SD_PROFILE, 'br1', 10);

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/budgetRules/br1/campaigns?pageSize=10');
    });

    it('listAssociatedBudgetRulesForCampaign GET /sd/campaigns/{id}/budgetRules', function () {
        Http::fake([ADS_SD_BASE.'/sd/campaigns/9/budgetRules' => Http::response(['associatedRules' => []])]);

        adsSd()->listAssociatedBudgetRulesForCampaign(ADS_SD_PROFILE, '9');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/campaigns/9/budgetRules');
    });

    it('createAssociatedBudgetRulesForCampaign POST /sd/campaigns/{id}/budgetRules', function () {
        Http::fake([ADS_SD_BASE.'/sd/campaigns/9/budgetRules' => Http::response(['associatedRules' => []])]);

        adsSd()->createAssociatedBudgetRulesForCampaign(ADS_SD_PROFILE, '9', ['br1']);

        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/campaigns/9/budgetRules', ['budgetRuleIds' => ['br1']], 'application/json');
    });

    it('disassociateAssociatedBudgetRuleForCampaign DELETE /sd/campaigns/{id}/budgetRules/{ruleId}', function () {
        Http::fake([ADS_SD_BASE.'/sd/campaigns/9/budgetRules/br1' => Http::response(['code' => 'SUCCESS'])]);

        adsSd()->disassociateAssociatedBudgetRuleForCampaign(ADS_SD_PROFILE, '9', 'br1');

        adsSdAssertSent('DELETE', ADS_SD_BASE.'/sd/campaigns/9/budgetRules/br1');
    });

    it('campaignsBudgetUsage POST /sd/campaigns/budget/usage com media type v1', function () {
        Http::fake([ADS_SD_BASE.'/sd/campaigns/budget/usage' => Http::response(['success' => [], 'error' => []], 207)]);

        adsSd()->campaignsBudgetUsage(ADS_SD_PROFILE, ['9']);

        adsSdAssertSent(
            'POST',
            ADS_SD_BASE.'/sd/campaigns/budget/usage',
            ['campaignIds' => ['9']],
            'application/vnd.sdcampaignbudgetusage.v1+json',
            'application/vnd.sdcampaignbudgetusage.v1+json',
        );
    });
});

// ---------------------------------------------------------------- recommendations / forecasts

describe('SponsoredDisplay recommendations e forecasts', function () {
    it('getBudgetRecommendations POST /sd/campaigns/budgetRecommendations com media type v3', function () {
        Http::fake([ADS_SD_BASE.'/sd/campaigns/budgetRecommendations' => Http::response(['budgetRecommendationsSuccessResults' => []])]);

        adsSd()->getBudgetRecommendations(ADS_SD_PROFILE, ['9']);

        adsSdAssertSent(
            'POST',
            ADS_SD_BASE.'/sd/campaigns/budgetRecommendations',
            ['campaignIds' => ['9']],
            'application/vnd.sdbudgetrecommendations.v3+json',
            'application/vnd.sdbudgetrecommendations.v3+json',
        );
    });

    it('getTargetRecommendations POST /sd/targets/recommendations com locale e media type v3.5', function () {
        Http::fake([ADS_SD_BASE.'/sd/targets/recommendations*' => Http::response(['recommendations' => []])]);

        $body = ['products' => [['asin' => 'B000']], 'tactic' => 'T00020'];
        adsSd()->getTargetRecommendations(ADS_SD_PROFILE, $body, 'pt_BR');

        adsSdAssertSent(
            'POST',
            ADS_SD_BASE.'/sd/targets/recommendations?locale=pt_BR',
            $body,
            'application/vnd.sdtargetingrecommendations.v3.5+json',
            'application/vnd.sdtargetingrecommendations.v3.5+json',
        );
    });

    it('getTargetBidRecommendations POST /sd/targets/bid/recommendations com media type v3.3', function () {
        Http::fake([ADS_SD_BASE.'/sd/targets/bid/recommendations' => Http::response(['bidRecommendations' => []], 207)]);

        $body = ['products' => [['asin' => 'B000']], 'targetingClauses' => [['targetingClause' => ['expressionType' => 'manual']]]];
        adsSd()->getTargetBidRecommendations(ADS_SD_PROFILE, $body);

        adsSdAssertSent(
            'POST',
            ADS_SD_BASE.'/sd/targets/bid/recommendations',
            $body,
            'application/vnd.sdtargetingrecommendations.v3.3+json',
            'application/vnd.sdtargetingrecommendations.v3.3+json',
        );
    });

    it('getHeadlineRecommendations POST /sd/recommendations/creative/headline com request/response v4.0', function () {
        Http::fake([ADS_SD_BASE.'/sd/recommendations/creative/headline' => Http::response(['recommendations' => []])]);

        $body = ['adFormat' => 'SPONSORED_DISPLAY', 'asins' => ['B000'], 'maxNumRecommendations' => 3];
        adsSd()->getHeadlineRecommendations(ADS_SD_PROFILE, $body);

        adsSdAssertSent(
            'POST',
            ADS_SD_BASE.'/sd/recommendations/creative/headline',
            $body,
            'application/vnd.sdheadlinerecommendationrequest.v4.0+json',
            'application/vnd.sdheadlinerecommendationresponse.v4.0+json',
        );
    });

    it('createForecast POST /sd/forecasts com media type sdforecasts.v3.1', function () {
        Http::fake([ADS_SD_BASE.'/sd/forecasts' => Http::response(['forecasts' => []])]);

        $body = ['campaign' => ['tactic' => 'T00020'], 'adGroup' => ['defaultBid' => 1.0], 'productAds' => [], 'targetingClauses' => []];
        adsSd()->createForecast(ADS_SD_PROFILE, $body);

        adsSdAssertSent(
            'POST',
            ADS_SD_BASE.'/sd/forecasts',
            $body,
            'application/vnd.sdforecasts.v3.1+json',
            'application/vnd.sdforecasts.v3.1+json',
        );
    });
});

// ---------------------------------------------------------------- snapshots + reports v2

describe('SponsoredDisplay snapshots e reports legado', function () {
    it('createSnapshot POST /sd/{recordType}/snapshot', function () {
        Http::fake([ADS_SD_BASE.'/sd/campaigns/snapshot' => Http::response(['snapshotId' => 'snap-1', 'status' => 'IN_PROGRESS'])]);

        $out = adsSd()->createSnapshot(ADS_SD_PROFILE, 'campaigns', ['stateFilter' => 'enabled']);

        expect($out['snapshotId'])->toBe('snap-1');
        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/campaigns/snapshot', ['stateFilter' => 'enabled'], 'application/json');
    });

    it('getSnapshotById GET /sd/snapshots/{id}', function () {
        Http::fake([ADS_SD_BASE.'/sd/snapshots/snap-1' => Http::response(['status' => 'SUCCESS'])]);

        adsSd()->getSnapshotById(ADS_SD_PROFILE, 'snap-1');

        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/snapshots/snap-1');
    });

    it('downloadSnapshotById GET /sd/snapshots/{id}/download', function () {
        Http::fake([ADS_SD_BASE.'/sd/snapshots/snap-1/download' => Http::response([['campaignId' => 9]])]);

        $out = adsSd()->downloadSnapshotById(ADS_SD_PROFILE, 'snap-1');

        expect($out[0]['campaignId'])->toBe(9);
        adsSdAssertSent('GET', ADS_SD_BASE.'/sd/snapshots/snap-1/download');
    });

    it('requestReport POST /sd/{recordType}/report (legado)', function () {
        Http::fake([ADS_SD_BASE.'/sd/campaigns/report' => Http::response(['reportId' => 'rep-1', 'status' => 'IN_PROGRESS'])]);

        $body = ['reportDate' => '20260901', 'tactic' => 'T00020', 'metrics' => 'impressions,clicks,cost'];
        $out = adsSd()->requestReport(ADS_SD_PROFILE, 'campaigns', $body);

        expect($out['reportId'])->toBe('rep-1');
        adsSdAssertSent('POST', ADS_SD_BASE.'/sd/campaigns/report', $body, 'application/json');
    });

    it('getReportStatus GET /v2/reports/{id}', function () {
        Http::fake([ADS_SD_BASE.'/v2/reports/rep-1' => Http::response(['reportId' => 'rep-1', 'status' => 'SUCCESS'])]);

        expect(adsSd()->getReportStatus(ADS_SD_PROFILE, 'rep-1')['status'])->toBe('SUCCESS');
        adsSdAssertSent('GET', ADS_SD_BASE.'/v2/reports/rep-1');
    });

    it('downloadReport GET /v2/reports/{id}/download descompacta o gzip JSON', function () {
        $gz = gzencode(json_encode([['campaignId' => 9, 'cost' => 1.5]]));
        Http::fake([ADS_SD_BASE.'/v2/reports/rep-1/download' => Http::response($gz)]);

        $rows = adsSd()->downloadReport(ADS_SD_PROFILE, 'rep-1');

        expect($rows)->toHaveCount(1)->and($rows[0]['cost'])->toBe(1.5);
        adsSdAssertSent('GET', ADS_SD_BASE.'/v2/reports/rep-1/download');
    });

    it('downloadReport com erro HTTP lança AmazonAdsRequestException', function () {
        Http::fake([ADS_SD_BASE.'/v2/reports/rep-x/download' => Http::response(['message' => 'not found'], 404)]);

        adsSd()->downloadReport(ADS_SD_PROFILE, 'rep-x');
    })->throws(AmazonAdsRequestException::class);

    it('erro 4xx numa rota /sd vira AmazonAdsRequestException com a mensagem', function () {
        Http::fake([ADS_SD_BASE.'/sd/campaigns/404' => Http::response(['message' => 'campaign not found'], 404)]);

        adsSd()->getCampaign(ADS_SD_PROFILE, '404');
    })->throws(AmazonAdsRequestException::class, 'campaign not found');
});

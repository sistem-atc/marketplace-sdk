<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\SponsoredProducts\SponsoredProductsMethods as SP;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function adsSpIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ads-access-token',
        refreshToken: 'ads-refresh',
        settings: [],
        active: true,
        expired: false,
    );
}

function adsSp(): SP
{
    return new SP(adsSpIntegration(), 'client-abc');
}

/**
 * Confere verbo, URL completa, auth, Scope, media types, headers extras e body.
 *
 * @param  array<string, mixed>|null  $body  null = não checa
 * @param  array<string, string>  $headers
 */
function adsSpAssertSent(string $method, string $url, ?string $scope = '111', ?string $contentType = null, ?string $accept = null, ?array $body = null, array $headers = []): void
{
    Http::assertSent(function (Request $req) use ($method, $url, $scope, $contentType, $accept, $body, $headers) {
        if ($req->method() !== $method || $req->url() !== $url) {
            return false;
        }
        if ($req->header('Amazon-Advertising-API-ClientId')[0] !== 'client-abc'
            || $req->header('Authorization')[0] !== 'Bearer ads-access-token') {
            return false;
        }
        if ($scope === null ? $req->hasHeader('Amazon-Advertising-API-Scope') : ($req->header('Amazon-Advertising-API-Scope')[0] ?? null) !== $scope) {
            return false;
        }
        if ($contentType !== null && ! $req->hasHeader('Content-Type', $contentType)) {
            return false;
        }
        $accept ??= $contentType; // regra do BaseMethods: Accept = Content-Type quando não informado
        if ($accept !== null && ! $req->hasHeader('Accept', $accept)) {
            return false;
        }
        foreach ($headers as $k => $v) {
            if (! $req->hasHeader($k, $v)) {
                return false;
            }
        }
        if ($body !== null && json_decode($req->body(), true) !== $body) {
            return false;
        }

        return true;
    });
}

beforeEach(function () {
    Http::preventStrayRequests();
});

$host = 'https://advertising-api.amazon.com';

/*
 * Cada caso: [chamada, verbo, URL, contentType, accept, body esperado, scope, headers extras]
 * accept null com contentType => Accept = contentType (regra do BaseMethods).
 */
$entityCases = [];
foreach ([
    'campaigns' => ['Campaigns', 'campaigns', 'campaignIdFilter', SP::CT_CAMPAIGN],
    'adGroups' => ['AdGroups', 'adGroups', 'adGroupIdFilter', SP::CT_AD_GROUP],
    'productAds' => ['ProductAds', 'productAds', 'adIdFilter', SP::CT_PRODUCT_AD],
    'keywords' => ['Keywords', 'keywords', 'keywordIdFilter', SP::CT_KEYWORD],
    'negativeKeywords' => ['NegativeKeywords', 'negativeKeywords', 'negativeKeywordIdFilter', SP::CT_NEGATIVE_KEYWORD],
    'campaignNegativeKeywords' => ['CampaignNegativeKeywords', 'campaignNegativeKeywords', 'campaignNegativeKeywordIdFilter', SP::CT_CAMPAIGN_NEGATIVE_KEYWORD],
    'targets' => ['TargetingClauses', 'targetingClauses', 'targetIdFilter', SP::CT_TARGETING_CLAUSE],
    'negativeTargets' => ['NegativeTargetingClauses', 'negativeTargetingClauses', 'negativeTargetIdFilter', SP::CT_NEGATIVE_TARGETING_CLAUSE],
    'campaignNegativeTargets' => ['CampaignNegativeTargetingClauses', 'campaignNegativeTargetingClauses', 'campaignNegativeTargetIdFilter', SP::CT_CAMPAIGN_NEGATIVE_TARGETING_CLAUSE],
] as $path => [$suffix, $key, $idFilter, $ct]) {
    $entityCases["create{$suffix}"] = [fn (SP $sp) => $sp->{"create{$suffix}"}('111', [['name' => 'x']], 'return=representation'), 'POST', "{$host}/sp/{$path}", $ct, null, [$key => [['name' => 'x']]], '111', ['Prefer' => 'return=representation']];
    $entityCases["update{$suffix}"] = [fn (SP $sp) => $sp->{"update{$suffix}"}('111', [['id' => '1', 'state' => 'PAUSED']]), 'PUT', "{$host}/sp/{$path}", $ct, null, [$key => [['id' => '1', 'state' => 'PAUSED']]], '111', []];
    $entityCases["delete{$suffix}"] = [fn (SP $sp) => $sp->{"delete{$suffix}"}('111', ['1', '2']), 'POST', "{$host}/sp/{$path}/delete", $ct, null, [$idFilter => ['include' => ['1', '2']]], '111', []];
    $entityCases["list{$suffix}"] = [fn (SP $sp) => $sp->{"list{$suffix}"}('111', ['maxResults' => 10, 'nextToken' => 'n1']), 'POST', "{$host}/sp/{$path}/list", $ct, null, ['maxResults' => 10, 'nextToken' => 'n1'], '111', []];
}

$otherCases = [
    // campanhas — extras
    'campaignsBudgetUsage' => [fn (SP $sp) => $sp->campaignsBudgetUsage('111', ['1']), 'POST', "{$host}/sp/campaigns/budget/usage", 'application/vnd.spcampaignbudgetusage.v1+json', null, ['campaignIds' => ['1']], '111', []],
    'getBudgetRecommendations' => [fn (SP $sp) => $sp->getBudgetRecommendations('111', ['1']), 'POST', "{$host}/sp/campaigns/budgetRecommendations", 'application/vnd.budgetrecommendation.v3+json', null, ['campaignIds' => ['1']], '111', []],
    'getBudgetRulesRecommendation' => [fn (SP $sp) => $sp->getBudgetRulesRecommendation('111', '1'), 'POST', "{$host}/sp/campaigns/budgetRules/recommendations", 'application/vnd.spbudgetrulesrecommendation.v3+json', null, ['campaignId' => '1'], '111', []],
    'getInitialBudgetRecommendation' => [fn (SP $sp) => $sp->getInitialBudgetRecommendation('111', ['targetingType' => 'AUTO']), 'POST', "{$host}/sp/campaigns/initialBudgetRecommendation", 'application/vnd.spinitialbudgetrecommendation.v3.4+json', null, ['targetingType' => 'AUTO'], '111', []],
    'listAssociatedBudgetRules' => [fn (SP $sp) => $sp->listAssociatedBudgetRules('111', 'c 1'), 'GET', "{$host}/sp/campaigns/c%201/budgetRules", null, null, null, '111', []],
    'createAssociatedBudgetRules' => [fn (SP $sp) => $sp->createAssociatedBudgetRules('111', 'c1', ['r1']), 'POST', "{$host}/sp/campaigns/c1/budgetRules", 'application/json', null, ['budgetRuleIds' => ['r1']], '111', []],
    'disassociateAssociatedBudgetRule' => [fn (SP $sp) => $sp->disassociateAssociatedBudgetRule('111', 'c1', 'r1'), 'DELETE', "{$host}/sp/campaigns/c1/budgetRules/r1", null, null, null, '111', []],
    'associateOptimizationRulesToCampaign' => [fn (SP $sp) => $sp->associateOptimizationRulesToCampaign('111', 'c1', ['o1']), 'POST', "{$host}/sp/campaigns/c1/optimizationRules", 'application/vnd.spoptimizationrules.v1+json', null, ['optimizationRuleIds' => ['o1']], '111', []],
    // recomendações de campanha
    'getCampaignRecommendations' => [fn (SP $sp) => $sp->getCampaignRecommendations('111', ['1', '2'], 'n1', '5'), 'GET', "{$host}/sp/campaign/recommendations?campaignIds=1%2C2&nextToken=n1&maxResults=5", null, 'application/vnd.spgetcampaignrecommendationsresponse.v1+json', null, '111', []],
    'fetchCampaignRecommendations' => [fn (SP $sp) => $sp->fetchCampaignRecommendations('111', ['maxResults' => 5]), 'POST', "{$host}/sp/campaign/recommendations", 'application/vnd.spgetcampaignrecommendationsrequest.v2+json', 'application/vnd.spgetcampaignrecommendationsresponse.v2+json', ['maxResults' => 5], '111', []],
    // regras de orçamento
    'getBudgetRulesForAdvertiser' => [fn (SP $sp) => $sp->getBudgetRulesForAdvertiser('111', 20, 'n1'), 'GET', "{$host}/sp/budgetRules?pageSize=20&nextToken=n1", null, null, null, '111', []],
    'createBudgetRules' => [fn (SP $sp) => $sp->createBudgetRules('111', [['name' => 'r']]), 'POST', "{$host}/sp/budgetRules", 'application/json', null, ['budgetRulesDetails' => [['name' => 'r']]], '111', []],
    'updateBudgetRules' => [fn (SP $sp) => $sp->updateBudgetRules('111', [['ruleId' => 'r1']]), 'PUT', "{$host}/sp/budgetRules", 'application/json', null, ['budgetRulesDetails' => [['ruleId' => 'r1']]], '111', []],
    'getBudgetRuleByRuleId' => [fn (SP $sp) => $sp->getBudgetRuleByRuleId('111', 'r1'), 'GET', "{$host}/sp/budgetRules/r1", null, null, null, '111', []],
    'getCampaignsAssociatedWithBudgetRule' => [fn (SP $sp) => $sp->getCampaignsAssociatedWithBudgetRule('111', 'r1', 10), 'GET', "{$host}/sp/budgetRules/r1/campaigns?pageSize=10", null, null, null, '111', []],
    'bulkBudgetRulesAssociation' => [fn (SP $sp) => $sp->bulkBudgetRulesAssociation('111', [['budgetRuleId' => 'r1', 'campaignId' => 'c1']]), 'POST', "{$host}/sp/budgetRulesAssociation", 'application/json', null, ['budgetRulesAssociations' => [['budgetRuleId' => 'r1', 'campaignId' => 'c1']]], '111', []],
    'bulkBudgetRulesDisassociation' => [fn (SP $sp) => $sp->bulkBudgetRulesDisassociation('111', [['budgetRuleId' => 'r1', 'campaignId' => 'c1']]), 'POST', "{$host}/sp/budgetRulesAssociation/delete", 'application/json', null, ['budgetRulesDisAssociations' => [['budgetRuleId' => 'r1', 'campaignId' => 'c1']]], '111', []],
    // product targeting
    'getNegativeBrands' => [fn (SP $sp) => $sp->getNegativeBrands('111'), 'GET', "{$host}/sp/negativeTargets/brands/recommendations", null, SP::ACCEPT_PRODUCT_TARGETING_V3, null, '111', []],
    'searchBrands' => [fn (SP $sp) => $sp->searchBrands('111', 'whey'), 'POST', "{$host}/sp/negativeTargets/brands/search", SP::CT_PRODUCT_TARGETING, SP::ACCEPT_PRODUCT_TARGETING_V3, ['keyword' => 'whey'], '111', []],
    'getTargetableCategories' => [fn (SP $sp) => $sp->getTargetableCategories('111', 'pt_BR'), 'GET', "{$host}/sp/targets/categories?locale=pt_BR", null, SP::ACCEPT_PRODUCT_TARGETING_V5, null, '111', []],
    'getCategoryRecommendationsForAsins' => [fn (SP $sp) => $sp->getCategoryRecommendationsForAsins('111', ['B01'], true, 'pt_BR', SP::ACCEPT_PRODUCT_TARGETING_V4), 'POST', "{$host}/sp/targets/categories/recommendations?locale=pt_BR", SP::CT_PRODUCT_TARGETING, SP::ACCEPT_PRODUCT_TARGETING_V4, ['asins' => ['B01'], 'includeAncestor' => true], '111', []],
    'getRefinementsForCategory' => [fn (SP $sp) => $sp->getRefinementsForCategory('111', 'cat/1'), 'GET', "{$host}/sp/targets/category/cat%2F1/refinements", null, SP::ACCEPT_PRODUCT_TARGETING_V4, null, '111', []],
    'getTargetableAsinCounts' => [fn (SP $sp) => $sp->getTargetableAsinCounts('111', ['category' => '9']), 'POST', "{$host}/sp/targets/products/count", SP::CT_PRODUCT_TARGETING, SP::ACCEPT_PRODUCT_TARGETING_V3, ['category' => '9'], '111', []],
    'getProductRecommendations' => [fn (SP $sp) => $sp->getProductRecommendations('111', ['adAsins' => ['B01'], 'count' => 5]), 'POST', "{$host}/sp/targets/products/recommendations", 'application/vnd.spproductrecommendation.v3+json', 'application/vnd.spproductrecommendationresponse.asins.v3+json', ['adAsins' => ['B01'], 'count' => 5], '111', []],
    // recomendações de lance/keyword
    'getThemeBasedBidRecommendationForAdGroup' => [fn (SP $sp) => $sp->getThemeBasedBidRecommendationForAdGroup('111', ['adGroupId' => 'g1'], 'v4'), 'POST', "{$host}/sp/targets/bid/recommendations", 'application/vnd.spthemebasedbidrecommendation.v4+json', null, ['adGroupId' => 'g1'], '111', []],
    'getRankedKeywordRecommendation' => [fn (SP $sp) => $sp->getRankedKeywordRecommendation('111', ['asins' => ['B01']], 'v5', ['Amazon-Advertising-API-MarketplaceId' => 'A2Q3Y263D00KWC']), 'POST', "{$host}/sp/targets/keywords/recommendations", 'application/vnd.spkeywordsrecommendation.v5+json', null, ['asins' => ['B01']], '111', ['Amazon-Advertising-API-MarketplaceId' => 'A2Q3Y263D00KWC']],
    'getMultiCountryThemeBasedBidRecommendationForAdGroup' => [fn (SP $sp) => $sp->getMultiCountryThemeBasedBidRecommendationForAdGroup('acc-1', ['countryCodes' => ['BR']]), 'POST', "{$host}/sp/global/targets/bid/recommendations", 'application/json', 'application/vnd.spthemebasedglobalbidrecommendation.v1+json', ['countryCodes' => ['BR']], null, ['Amazon-Ads-AccountId' => 'acc-1']],
    'getGlobalRankedKeywordRecommendation' => [fn (SP $sp) => $sp->getGlobalRankedKeywordRecommendation('111', 'acc-1', ['products' => []]), 'POST', "{$host}/sp/global/targets/keywords/recommendations/list", 'application/vnd.spkeywordsrecommendation.v5+json', null, ['products' => []], '111', ['Amazon-Ads-AccountId' => 'acc-1']],
    'getKeywordGroupRecommendations' => [fn (SP $sp) => $sp->getKeywordGroupRecommendations('111', ['asins' => ['B01'], 'countryCode' => 'BR'], 'acc-1', 'pt_BR'), 'POST', "{$host}/sp/targeting/recommendations/keywordGroups", 'application/vnd.spkeywordgroupsrecommendations.v1.0+json', null, ['asins' => ['B01'], 'countryCode' => 'BR'], '111', ['Amazon-Ads-AccountId' => 'acc-1', 'locale' => 'pt_BR']],
    // campaignOptimization (v1)
    'createCampaignOptimizationRule' => [fn (SP $sp) => $sp->createCampaignOptimizationRule('111', ['ruleName' => 'r']), 'POST', "{$host}/sp/rules/campaignOptimization", SP::CT_OPTIMIZATION_RULES, null, ['ruleName' => 'r'], '111', []],
    'updateCampaignOptimizationRule' => [fn (SP $sp) => $sp->updateCampaignOptimizationRule('111', ['campaignOptimizationId' => 'o1']), 'PUT', "{$host}/sp/rules/campaignOptimization", SP::CT_OPTIMIZATION_RULES, null, ['campaignOptimizationId' => 'o1'], '111', []],
    'getOptimizationRuleEligibility' => [fn (SP $sp) => $sp->getOptimizationRuleEligibility('111', ['c1'], true), 'POST', "{$host}/sp/rules/campaignOptimization/eligibility", SP::CT_OPTIMIZATION_RULES, null, ['campaignIds' => ['c1'], 'requirePerformanceMetrics' => true], '111', []],
    'getRuleNotification' => [fn (SP $sp) => $sp->getRuleNotification('111', ['c1']), 'POST', "{$host}/sp/rules/campaignOptimization/state", SP::CT_OPTIMIZATION_RULES, null, ['campaignIds' => ['c1']], '111', []],
    'getCampaignOptimizationRule' => [fn (SP $sp) => $sp->getCampaignOptimizationRule('111', 'o1'), 'GET', "{$host}/sp/rules/campaignOptimization/o1", null, SP::CT_OPTIMIZATION_RULES, null, '111', []],
    'deleteCampaignOptimizationRule' => [fn (SP $sp) => $sp->deleteCampaignOptimizationRule('111', 'o1'), 'DELETE', "{$host}/sp/rules/campaignOptimization/o1", null, SP::CT_OPTIMIZATION_RULES, null, '111', []],
    // rules/optimization (v1|v2)
    'createOptimizationRules' => [fn (SP $sp) => $sp->createOptimizationRules('111', [['ruleName' => 'r']]), 'POST', "{$host}/sp/rules/optimization", 'application/vnd.spoptimizationrules.v2+json', null, ['optimizationRules' => [['ruleName' => 'r']]], '111', []],
    'updateOptimizationRules' => [fn (SP $sp) => $sp->updateOptimizationRules('111', [['optimizationRuleId' => 'o1']], 'v1'), 'PUT', "{$host}/sp/rules/optimization", 'application/vnd.spoptimizationrules.v1+json', null, ['optimizationRules' => [['optimizationRuleId' => 'o1']]], '111', []],
    'searchOptimizationRules' => [fn (SP $sp) => $sp->searchOptimizationRules('111', ['maxResults' => 5]), 'POST', "{$host}/sp/rules/optimization/search", 'application/vnd.spoptimizationrules.v2+json', null, ['maxResults' => 5], '111', []],
    // target promotion groups
    'createTargetPromotionGroups' => [fn (SP $sp) => $sp->createTargetPromotionGroups('111', ['adGroupId' => 'g1'], 'v1'), 'POST', "{$host}/sp/targetPromotionGroups", 'application/vnd.sptargetpromotiongroup.v1+json', null, ['adGroupId' => 'g1'], '111', []],
    'listTargetPromotionGroups' => [fn (SP $sp) => $sp->listTargetPromotionGroups('111', ['maxResults' => 5]), 'POST', "{$host}/sp/targetPromotionGroups/list", 'application/vnd.sptargetpromotiongroup.v2+json', null, ['maxResults' => 5], '111', []],
    'getTargetPromotionGroupsRecommendations' => [fn (SP $sp) => $sp->getTargetPromotionGroupsRecommendations('111', ['maxResults' => 5]), 'POST', "{$host}/sp/targetPromotionGroups/recommendations", 'application/vnd.spTargetPromotionGroupsRecommendations.v1+json', null, ['maxResults' => 5], '111', []],
    'createTargetPromotionGroupTargets' => [fn (SP $sp) => $sp->createTargetPromotionGroupTargets('111', 'tpg1', [['target' => 'B01']]), 'POST', "{$host}/sp/targetPromotionGroups/targets", 'application/vnd.sptargetpromotiongrouptarget.v2+json', null, ['targetPromotionGroupId' => 'tpg1', 'targets' => [['target' => 'B01']]], '111', []],
    'listTargetPromotionGroupTargets' => [fn (SP $sp) => $sp->listTargetPromotionGroupTargets('111', ['nextToken' => 'n1'], 'v1'), 'POST', "{$host}/sp/targetPromotionGroups/targets/list", 'application/vnd.sptargetpromotiongrouptarget.v1+json', null, ['nextToken' => 'n1'], '111', []],
    // eventos
    'getAllRuleEvents' => [fn (SP $sp) => $sp->getAllRuleEvents('111', ['campaignIds' => ['c1']]), 'POST', "{$host}/sp/v1/events", 'application/json', null, ['campaignIds' => ['c1']], '111', []],
];

describe('Amazon Ads — SponsoredProductsMethods (v3)', function () use ($entityCases, $otherCases) {
    it('cobre as 80 operações v3 do gap', function () use ($entityCases, $otherCases) {
        expect(count($entityCases) + count($otherCases))->toBe(80);
    });

    it('manda verbo, URL, Scope, media type e body certos', function (Closure $call, string $method, string $url, ?string $ct, ?string $accept, ?array $body, ?string $scope, array $headers) {
        Http::fake(['advertising-api.amazon.com/*' => Http::response(['ok' => true])]);

        $result = $call(adsSp());

        expect($result)->toBe(['ok' => true]);

        adsSpAssertSent($method, $url, $scope, $ct, $accept, $body, $headers);
    })->with(array_merge($entityCases, $otherCases));

    it('erro HTTP vira AmazonAdsRequestException com a mensagem do corpo', function () {
        Http::fake(['advertising-api.amazon.com/sp/campaigns/list' => Http::response(['message' => 'Unsupported Media Type'], 415)]);

        adsSp()->listCampaigns('111');
    })->throws(AmazonAdsRequestException::class, 'Unsupported Media Type');
});

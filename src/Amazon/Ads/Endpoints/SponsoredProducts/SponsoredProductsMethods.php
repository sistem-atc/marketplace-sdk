<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads\Endpoints\SponsoredProducts;

use SistemAtc\Marketplaces\Amazon\Ads\Bases\BaseMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;

/**
 * Sponsored Products v3 da Amazon Ads API (`/sp/*`).
 *
 * Cobre campanhas, grupos de anúncio, anúncios de produto, keywords,
 * targets (positivos e negativos, de ad group e de campanha), regras de
 * orçamento, recomendações (orçamento, lance, keyword, categoria, produto),
 * regras de otimização, target promotion groups, uso de orçamento e eventos.
 *
 * Cada recurso v3 exige o media type próprio em Content-Type E Accept
 * (`application/vnd.spCampaign.v3+json`, `application/vnd.spAdGroup.v3+json`…);
 * os métodos já passam o certo. Toda rota leva o profileId no header
 * `Amazon-Advertising-API-Scope` (1º argumento), exceto a global de lance
 * multi-país, que usa `Amazon-Ads-AccountId`.
 *
 * Convenção dos CRUDs por entidade: `create*`/`update*` recebem a LISTA de
 * entidades e embrulham na chave da API; `delete*` recebe a lista de ids e
 * monta o `<x>IdFilter`; `list*` recebe o corpo de filtros (`*IdFilter`
 * {include:[]}, `stateFilter`, `nameFilter`, `maxResults`, `nextToken`,
 * `includeExtendedDataFields`). Paginação: `nextToken` no corpo/query,
 * devolvido no mesmo nome na resposta.
 */
class SponsoredProductsMethods extends BaseMethods
{
    public const CT_CAMPAIGN = 'application/vnd.spCampaign.v3+json';

    public const CT_AD_GROUP = 'application/vnd.spAdGroup.v3+json';

    public const CT_PRODUCT_AD = 'application/vnd.spProductAd.v3+json';

    public const CT_KEYWORD = 'application/vnd.spKeyword.v3+json';

    public const CT_NEGATIVE_KEYWORD = 'application/vnd.spNegativeKeyword.v3+json';

    public const CT_CAMPAIGN_NEGATIVE_KEYWORD = 'application/vnd.spCampaignNegativeKeyword.v3+json';

    public const CT_TARGETING_CLAUSE = 'application/vnd.spTargetingClause.v3+json';

    public const CT_NEGATIVE_TARGETING_CLAUSE = 'application/vnd.spNegativeTargetingClause.v3+json';

    public const CT_CAMPAIGN_NEGATIVE_TARGETING_CLAUSE = 'application/vnd.spCampaignNegativeTargetingClause.v3+json';

    public const CT_PRODUCT_TARGETING = 'application/vnd.spproducttargeting.v3+json';

    public const ACCEPT_PRODUCT_TARGETING_V3 = 'application/vnd.spproducttargetingresponse.v3+json';

    public const ACCEPT_PRODUCT_TARGETING_V4 = 'application/vnd.spproducttargetingresponse.v4+json';

    public const ACCEPT_PRODUCT_TARGETING_V5 = 'application/vnd.spproducttargetingresponse.v5+json';

    public const CT_OPTIMIZATION_RULES = 'application/vnd.optimizationrules.v1+json';

    /* ----------------------------------------------------------------
     | Campanhas — /sp/campaigns (application/vnd.spCampaign.v3+json)
     * ---------------------------------------------------------------- */

    /**
     * Cria campanhas (até 100 por chamada).
     *
     * @param  list<array<string, mixed>>  $campaigns  cada uma com name, targetingType (MANUAL|AUTO), state, budget {budgetType, budget}, startDate, endDate?, dynamicBidding?, portfolioId?, tags?
     * @param  string|null  $prefer  header Prefer (ex.: `return=representation`)
     * @return array<string, mixed>  {campaigns: {success: [], error: []}}
     *
     * @throws AmazonAdsRequestException
     */
    public function createCampaigns(string $profileId, array $campaigns, ?string $prefer = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaigns',
            profileId: $profileId,
            body: ['campaigns' => array_values($campaigns)],
            contentType: self::CT_CAMPAIGN,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Atualiza campanhas (até 100 por chamada).
     *
     * @param  list<array<string, mixed>>  $campaigns  cada uma com campaignId + campos a alterar (name, state, budget, endDate, dynamicBidding…)
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateCampaigns(string $profileId, array $campaigns, ?string $prefer = null): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sp/campaigns',
            profileId: $profileId,
            body: ['campaigns' => array_values($campaigns)],
            contentType: self::CT_CAMPAIGN,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Arquiva campanhas por id (`campaignIdFilter.include`).
     *
     * @param  list<string>  $campaignIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function deleteCampaigns(string $profileId, array $campaignIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaigns/delete',
            profileId: $profileId,
            body: ['campaignIdFilter' => ['include' => array_values($campaignIds)]],
            contentType: self::CT_CAMPAIGN,
        );
    }

    /**
     * Lista campanhas. Pagina por `nextToken`.
     *
     * @param  array<string, mixed>  $body  campaignIdFilter, stateFilter, nameFilter, portfolioIdFilter, includeExtendedDataFields, maxResults, nextToken
     * @return array<string, mixed>  {campaigns: [], totalResults, nextToken?}
     *
     * @throws AmazonAdsRequestException
     */
    public function listCampaigns(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaigns/list',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_CAMPAIGN,
        );
    }

    /**
     * Uso do orçamento (% consumido) das campanhas informadas.
     *
     * @param  list<string>  $campaignIds  até 100
     * @return array<string, mixed>  {success: [{campaignId, budget, budgetUsagePercent, usageUpdatedTimestamp}], error: []}
     *
     * @throws AmazonAdsRequestException
     */
    public function campaignsBudgetUsage(string $profileId, array $campaignIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaigns/budget/usage',
            profileId: $profileId,
            body: ['campaignIds' => array_values($campaignIds)],
            contentType: 'application/vnd.spcampaignbudgetusage.v1+json',
        );
    }

    /**
     * Recomendação de orçamento pra campanhas existentes (v3).
     *
     * @param  list<string>  $campaignIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getBudgetRecommendations(string $profileId, array $campaignIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaigns/budgetRecommendations',
            profileId: $profileId,
            body: ['campaignIds' => array_values($campaignIds)],
            contentType: 'application/vnd.budgetrecommendation.v3+json',
        );
    }

    /**
     * Recomendação de regras de orçamento pra uma campanha (v3).
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getBudgetRulesRecommendation(string $profileId, string $campaignId): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaigns/budgetRules/recommendations',
            profileId: $profileId,
            body: ['campaignId' => $campaignId],
            contentType: 'application/vnd.spbudgetrulesrecommendation.v3+json',
        );
    }

    /**
     * Recomendação de orçamento INICIAL pra campanha nova (v3.4). Path
     * `/sp/campaigns/initialBudgetRecommendation` (operationId da spec é
     * `getBudgetRecommendation`, renomeado pra não colidir com o plural).
     *
     * @param  array<string, mixed>  $body  targetingType (AUTO|MANUAL), adGroups [{adGroupId?, asins, targetingExpressions}], bidding?, startDate?, endDate?
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getInitialBudgetRecommendation(string $profileId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaigns/initialBudgetRecommendation',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.spinitialbudgetrecommendation.v3.4+json',
        );
    }

    /**
     * Regras de orçamento associadas a uma campanha.
     *
     * @return array<string, mixed>  {associatedRules: []}
     *
     * @throws AmazonAdsRequestException
     */
    public function listAssociatedBudgetRules(string $profileId, string $campaignId): array
    {
        return $this->request(
            method: 'GET',
            path: '/sp/campaigns/'.rawurlencode($campaignId).'/budgetRules',
            profileId: $profileId,
        );
    }

    /**
     * Associa regras de orçamento existentes a uma campanha.
     *
     * @param  list<string>  $budgetRuleIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createAssociatedBudgetRules(string $profileId, string $campaignId, array $budgetRuleIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaigns/'.rawurlencode($campaignId).'/budgetRules',
            profileId: $profileId,
            body: ['budgetRuleIds' => array_values($budgetRuleIds)],
        );
    }

    /**
     * Desassocia uma regra de orçamento de uma campanha.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function disassociateAssociatedBudgetRule(string $profileId, string $campaignId, string $budgetRuleId): array
    {
        return $this->request(
            method: 'DELETE',
            path: '/sp/campaigns/'.rawurlencode($campaignId).'/budgetRules/'.rawurlencode($budgetRuleId),
            profileId: $profileId,
        );
    }

    /**
     * Associa regras de otimização (`/sp/rules/optimization`) a uma campanha.
     *
     * @param  list<string>  $optimizationRuleIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function associateOptimizationRulesToCampaign(string $profileId, string $campaignId, array $optimizationRuleIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaigns/'.rawurlencode($campaignId).'/optimizationRules',
            profileId: $profileId,
            body: ['optimizationRuleIds' => array_values($optimizationRuleIds)],
            contentType: 'application/vnd.spoptimizationrules.v1+json',
        );
    }

    /* ----------------------------------------------------------------
     | Recomendações de campanha — /sp/campaign/recommendations
     * ---------------------------------------------------------------- */

    /**
     * Recomendações pra campanhas (v1, GET). `campaignIds` vai na query
     * separado por vírgula. Pagina por `nextToken`.
     *
     * @param  list<string>  $campaignIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getCampaignRecommendations(string $profileId, array $campaignIds = [], ?string $nextToken = null, ?string $maxResults = null): array
    {
        return $this->request(
            method: 'GET',
            path: '/sp/campaign/recommendations',
            profileId: $profileId,
            query: array_filter([
                'campaignIds' => $campaignIds !== [] ? implode(',', $campaignIds) : null,
                'nextToken' => $nextToken,
                'maxResults' => $maxResults,
            ], static fn ($v) => $v !== null),
            accept: 'application/vnd.spgetcampaignrecommendationsresponse.v1+json',
        );
    }

    /**
     * Recomendações pra campanhas (v2, POST com filtro por tipo).
     *
     * @param  array<string, mixed>  $body  campaigns [{campaignId, recommendationType}], maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function fetchCampaignRecommendations(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaign/recommendations',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.spgetcampaignrecommendationsrequest.v2+json',
            accept: 'application/vnd.spgetcampaignrecommendationsresponse.v2+json',
        );
    }

    /* ----------------------------------------------------------------
     | Regras de orçamento — /sp/budgetRules (application/json)
     * ---------------------------------------------------------------- */

    /**
     * Regras de orçamento do anunciante. Pagina por `nextToken`.
     *
     * @return array<string, mixed>  {budgetRulesForAdvertiserResponse: [], nextToken?}
     *
     * @throws AmazonAdsRequestException
     */
    public function getBudgetRulesForAdvertiser(string $profileId, int $pageSize = 30, ?string $nextToken = null): array
    {
        return $this->request(
            method: 'GET',
            path: '/sp/budgetRules',
            profileId: $profileId,
            query: array_filter(['pageSize' => $pageSize, 'nextToken' => $nextToken], static fn ($v) => $v !== null),
        );
    }

    /**
     * Cria regras de orçamento (até 25).
     *
     * @param  list<array<string, mixed>>  $budgetRulesDetails  cada uma com name, ruleType (SCHEDULE|PERFORMANCE), duration, recurrence, budgetIncreaseBy, performanceMeasureCondition?
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createBudgetRules(string $profileId, array $budgetRulesDetails): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/budgetRules',
            profileId: $profileId,
            body: ['budgetRulesDetails' => array_values($budgetRulesDetails)],
        );
    }

    /**
     * Atualiza regras de orçamento (até 25).
     *
     * @param  list<array<string, mixed>>  $budgetRulesDetails  cada uma com ruleId, ruleState, ruleDetails…
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateBudgetRules(string $profileId, array $budgetRulesDetails): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sp/budgetRules',
            profileId: $profileId,
            body: ['budgetRulesDetails' => array_values($budgetRulesDetails)],
        );
    }

    /**
     * Uma regra de orçamento pelo id.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getBudgetRuleByRuleId(string $profileId, string $budgetRuleId): array
    {
        return $this->request(
            method: 'GET',
            path: '/sp/budgetRules/'.rawurlencode($budgetRuleId),
            profileId: $profileId,
        );
    }

    /**
     * Campanhas associadas a uma regra de orçamento. Pagina por `nextToken`.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getCampaignsAssociatedWithBudgetRule(string $profileId, string $budgetRuleId, int $pageSize = 30, ?string $nextToken = null): array
    {
        return $this->request(
            method: 'GET',
            path: '/sp/budgetRules/'.rawurlencode($budgetRuleId).'/campaigns',
            profileId: $profileId,
            query: array_filter(['pageSize' => $pageSize, 'nextToken' => $nextToken], static fn ($v) => $v !== null),
        );
    }

    /**
     * Associa regras de orçamento a campanhas em lote.
     *
     * @param  list<array{budgetRuleId: string, campaignId: string}>  $associations
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function bulkBudgetRulesAssociation(string $profileId, array $associations): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/budgetRulesAssociation',
            profileId: $profileId,
            body: ['budgetRulesAssociations' => array_values($associations)],
        );
    }

    /**
     * Desassocia regras de orçamento de campanhas em lote.
     *
     * @param  list<array{budgetRuleId: string, campaignId: string}>  $disassociations
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function bulkBudgetRulesDisassociation(string $profileId, array $disassociations): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/budgetRulesAssociation/delete',
            profileId: $profileId,
            body: ['budgetRulesDisAssociations' => array_values($disassociations)],
        );
    }

    /* ----------------------------------------------------------------
     | Grupos de anúncio — /sp/adGroups (application/vnd.spAdGroup.v3+json)
     * ---------------------------------------------------------------- */

    /**
     * Cria grupos de anúncio.
     *
     * @param  list<array<string, mixed>>  $adGroups  cada um com campaignId, name, state, defaultBid
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createAdGroups(string $profileId, array $adGroups, ?string $prefer = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/adGroups',
            profileId: $profileId,
            body: ['adGroups' => array_values($adGroups)],
            contentType: self::CT_AD_GROUP,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Atualiza grupos de anúncio.
     *
     * @param  list<array<string, mixed>>  $adGroups  cada um com adGroupId + name/state/defaultBid
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateAdGroups(string $profileId, array $adGroups, ?string $prefer = null): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sp/adGroups',
            profileId: $profileId,
            body: ['adGroups' => array_values($adGroups)],
            contentType: self::CT_AD_GROUP,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Arquiva grupos de anúncio por id.
     *
     * @param  list<string>  $adGroupIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function deleteAdGroups(string $profileId, array $adGroupIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/adGroups/delete',
            profileId: $profileId,
            body: ['adGroupIdFilter' => ['include' => array_values($adGroupIds)]],
            contentType: self::CT_AD_GROUP,
        );
    }

    /**
     * Lista grupos de anúncio. Pagina por `nextToken`.
     *
     * @param  array<string, mixed>  $body  adGroupIdFilter, campaignIdFilter, campaignTargetingTypeFilter, stateFilter, nameFilter, includeExtendedDataFields, maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listAdGroups(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/adGroups/list',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_AD_GROUP,
        );
    }

    /* ----------------------------------------------------------------
     | Anúncios de produto — /sp/productAds (application/vnd.spProductAd.v3+json)
     * ---------------------------------------------------------------- */

    /**
     * Cria anúncios de produto.
     *
     * @param  list<array<string, mixed>>  $productAds  cada um com campaignId, adGroupId, state, sku (seller) ou asin (vendor), customText?
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createProductAds(string $profileId, array $productAds, ?string $prefer = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/productAds',
            profileId: $profileId,
            body: ['productAds' => array_values($productAds)],
            contentType: self::CT_PRODUCT_AD,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Atualiza anúncios de produto (só `state`).
     *
     * @param  list<array<string, mixed>>  $productAds  cada um com adId, state
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateProductAds(string $profileId, array $productAds, ?string $prefer = null): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sp/productAds',
            profileId: $profileId,
            body: ['productAds' => array_values($productAds)],
            contentType: self::CT_PRODUCT_AD,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Arquiva anúncios de produto por id.
     *
     * @param  list<string>  $adIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function deleteProductAds(string $profileId, array $adIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/productAds/delete',
            profileId: $profileId,
            body: ['adIdFilter' => ['include' => array_values($adIds)]],
            contentType: self::CT_PRODUCT_AD,
        );
    }

    /**
     * Lista anúncios de produto. Pagina por `nextToken`.
     *
     * @param  array<string, mixed>  $body  adIdFilter, adGroupIdFilter, campaignIdFilter, stateFilter, includeExtendedDataFields, maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listProductAds(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/productAds/list',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_PRODUCT_AD,
        );
    }

    /* ----------------------------------------------------------------
     | Keywords — /sp/keywords (application/vnd.spKeyword.v3+json)
     * ---------------------------------------------------------------- */

    /**
     * Cria keywords (até 1000).
     *
     * @param  list<array<string, mixed>>  $keywords  cada uma com campaignId, adGroupId, keywordText, matchType (EXACT|PHRASE|BROAD), state, bid?
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createKeywords(string $profileId, array $keywords, ?string $prefer = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/keywords',
            profileId: $profileId,
            body: ['keywords' => array_values($keywords)],
            contentType: self::CT_KEYWORD,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Atualiza keywords (bid/state).
     *
     * @param  list<array<string, mixed>>  $keywords  cada uma com keywordId + bid/state
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateKeywords(string $profileId, array $keywords, ?string $prefer = null): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sp/keywords',
            profileId: $profileId,
            body: ['keywords' => array_values($keywords)],
            contentType: self::CT_KEYWORD,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Arquiva keywords por id.
     *
     * @param  list<string>  $keywordIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function deleteKeywords(string $profileId, array $keywordIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/keywords/delete',
            profileId: $profileId,
            body: ['keywordIdFilter' => ['include' => array_values($keywordIds)]],
            contentType: self::CT_KEYWORD,
        );
    }

    /**
     * Lista keywords. Pagina por `nextToken`.
     *
     * @param  array<string, mixed>  $body  keywordIdFilter, adGroupIdFilter, campaignIdFilter, stateFilter, matchTypeFilter, keywordTextFilter, locale, includeExtendedDataFields, maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listKeywords(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/keywords/list',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_KEYWORD,
        );
    }

    /* ----------------------------------------------------------------
     | Keywords negativas de ad group — /sp/negativeKeywords
     * ---------------------------------------------------------------- */

    /**
     * Cria keywords negativas de grupo de anúncio.
     *
     * @param  list<array<string, mixed>>  $negativeKeywords  cada uma com campaignId, adGroupId, keywordText, matchType (NEGATIVE_EXACT|NEGATIVE_PHRASE), state
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createNegativeKeywords(string $profileId, array $negativeKeywords, ?string $prefer = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/negativeKeywords',
            profileId: $profileId,
            body: ['negativeKeywords' => array_values($negativeKeywords)],
            contentType: self::CT_NEGATIVE_KEYWORD,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Atualiza keywords negativas de grupo de anúncio (state).
     *
     * @param  list<array<string, mixed>>  $negativeKeywords  cada uma com keywordId, state
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateNegativeKeywords(string $profileId, array $negativeKeywords, ?string $prefer = null): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sp/negativeKeywords',
            profileId: $profileId,
            body: ['negativeKeywords' => array_values($negativeKeywords)],
            contentType: self::CT_NEGATIVE_KEYWORD,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Arquiva keywords negativas de grupo de anúncio por id.
     *
     * @param  list<string>  $keywordIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function deleteNegativeKeywords(string $profileId, array $keywordIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/negativeKeywords/delete',
            profileId: $profileId,
            body: ['negativeKeywordIdFilter' => ['include' => array_values($keywordIds)]],
            contentType: self::CT_NEGATIVE_KEYWORD,
        );
    }

    /**
     * Lista keywords negativas de grupo de anúncio. Pagina por `nextToken`.
     *
     * @param  array<string, mixed>  $body  negativeKeywordIdFilter, adGroupIdFilter, campaignIdFilter, stateFilter, matchTypeFilter, negativeKeywordTextFilter, locale, includeExtendedDataFields, maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listNegativeKeywords(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/negativeKeywords/list',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_NEGATIVE_KEYWORD,
        );
    }

    /* ----------------------------------------------------------------
     | Keywords negativas de campanha — /sp/campaignNegativeKeywords
     * ---------------------------------------------------------------- */

    /**
     * Cria keywords negativas de campanha.
     *
     * @param  list<array<string, mixed>>  $campaignNegativeKeywords  cada uma com campaignId, keywordText, matchType, state
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createCampaignNegativeKeywords(string $profileId, array $campaignNegativeKeywords, ?string $prefer = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaignNegativeKeywords',
            profileId: $profileId,
            body: ['campaignNegativeKeywords' => array_values($campaignNegativeKeywords)],
            contentType: self::CT_CAMPAIGN_NEGATIVE_KEYWORD,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Atualiza keywords negativas de campanha (state).
     *
     * @param  list<array<string, mixed>>  $campaignNegativeKeywords  cada uma com keywordId, state
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateCampaignNegativeKeywords(string $profileId, array $campaignNegativeKeywords, ?string $prefer = null): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sp/campaignNegativeKeywords',
            profileId: $profileId,
            body: ['campaignNegativeKeywords' => array_values($campaignNegativeKeywords)],
            contentType: self::CT_CAMPAIGN_NEGATIVE_KEYWORD,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Arquiva keywords negativas de campanha por id.
     *
     * @param  list<string>  $keywordIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function deleteCampaignNegativeKeywords(string $profileId, array $keywordIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaignNegativeKeywords/delete',
            profileId: $profileId,
            body: ['campaignNegativeKeywordIdFilter' => ['include' => array_values($keywordIds)]],
            contentType: self::CT_CAMPAIGN_NEGATIVE_KEYWORD,
        );
    }

    /**
     * Lista keywords negativas de campanha. Pagina por `nextToken`.
     *
     * @param  array<string, mixed>  $body  campaignNegativeKeywordIdFilter, campaignIdFilter, stateFilter, matchTypeFilter, campaignNegativeKeywordTextFilter, includeExtendedDataFields, maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listCampaignNegativeKeywords(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaignNegativeKeywords/list',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_CAMPAIGN_NEGATIVE_KEYWORD,
        );
    }

    /* ----------------------------------------------------------------
     | Targets (cláusulas de segmentação) — /sp/targets
     * ---------------------------------------------------------------- */

    /**
     * Cria cláusulas de segmentação (produto/categoria/auto).
     *
     * @param  list<array<string, mixed>>  $targetingClauses  cada uma com campaignId, adGroupId, expressionType (AUTO|MANUAL), expression [{type, value?}], state, bid?
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createTargetingClauses(string $profileId, array $targetingClauses, ?string $prefer = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/targets',
            profileId: $profileId,
            body: ['targetingClauses' => array_values($targetingClauses)],
            contentType: self::CT_TARGETING_CLAUSE,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Atualiza cláusulas de segmentação (bid/state/expression).
     *
     * @param  list<array<string, mixed>>  $targetingClauses  cada uma com targetId + bid/state/expression/expressionType
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateTargetingClauses(string $profileId, array $targetingClauses, ?string $prefer = null): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sp/targets',
            profileId: $profileId,
            body: ['targetingClauses' => array_values($targetingClauses)],
            contentType: self::CT_TARGETING_CLAUSE,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Arquiva cláusulas de segmentação por id.
     *
     * @param  list<string>  $targetIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function deleteTargetingClauses(string $profileId, array $targetIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/targets/delete',
            profileId: $profileId,
            body: ['targetIdFilter' => ['include' => array_values($targetIds)]],
            contentType: self::CT_TARGETING_CLAUSE,
        );
    }

    /**
     * Lista cláusulas de segmentação. Pagina por `nextToken`.
     *
     * @param  array<string, mixed>  $body  targetIdFilter, adGroupIdFilter, campaignIdFilter, stateFilter, expressionTypeFilter, asinFilter, includeExtendedDataFields, maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listTargetingClauses(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/targets/list',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_TARGETING_CLAUSE,
        );
    }

    /* ----------------------------------------------------------------
     | Targets negativos de ad group — /sp/negativeTargets
     * ---------------------------------------------------------------- */

    /**
     * Cria cláusulas negativas de grupo de anúncio.
     *
     * @param  list<array<string, mixed>>  $negativeTargetingClauses  cada uma com campaignId, adGroupId, expression [{type: ASIN_SAME_AS|ASIN_BRAND_SAME_AS, value}], state
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createNegativeTargetingClauses(string $profileId, array $negativeTargetingClauses, ?string $prefer = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/negativeTargets',
            profileId: $profileId,
            body: ['negativeTargetingClauses' => array_values($negativeTargetingClauses)],
            contentType: self::CT_NEGATIVE_TARGETING_CLAUSE,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Atualiza cláusulas negativas de grupo de anúncio.
     *
     * @param  list<array<string, mixed>>  $negativeTargetingClauses  cada uma com targetId + state/expression
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateNegativeTargetingClauses(string $profileId, array $negativeTargetingClauses, ?string $prefer = null): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sp/negativeTargets',
            profileId: $profileId,
            body: ['negativeTargetingClauses' => array_values($negativeTargetingClauses)],
            contentType: self::CT_NEGATIVE_TARGETING_CLAUSE,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Arquiva cláusulas negativas de grupo de anúncio por id.
     *
     * @param  list<string>  $targetIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function deleteNegativeTargetingClauses(string $profileId, array $targetIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/negativeTargets/delete',
            profileId: $profileId,
            body: ['negativeTargetIdFilter' => ['include' => array_values($targetIds)]],
            contentType: self::CT_NEGATIVE_TARGETING_CLAUSE,
        );
    }

    /**
     * Lista cláusulas negativas de grupo de anúncio. Pagina por `nextToken`.
     *
     * @param  array<string, mixed>  $body  negativeTargetIdFilter, adGroupIdFilter, campaignIdFilter, stateFilter, asinFilter, includeExtendedDataFields, maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listNegativeTargetingClauses(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/negativeTargets/list',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_NEGATIVE_TARGETING_CLAUSE,
        );
    }

    /* ----------------------------------------------------------------
     | Targets negativos de campanha — /sp/campaignNegativeTargets
     * ---------------------------------------------------------------- */

    /**
     * Cria cláusulas negativas de campanha.
     *
     * @param  list<array<string, mixed>>  $campaignNegativeTargetingClauses  cada uma com campaignId, expression, state
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createCampaignNegativeTargetingClauses(string $profileId, array $campaignNegativeTargetingClauses, ?string $prefer = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaignNegativeTargets',
            profileId: $profileId,
            body: ['campaignNegativeTargetingClauses' => array_values($campaignNegativeTargetingClauses)],
            contentType: self::CT_CAMPAIGN_NEGATIVE_TARGETING_CLAUSE,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Atualiza cláusulas negativas de campanha.
     *
     * @param  list<array<string, mixed>>  $campaignNegativeTargetingClauses  cada uma com targetId + state/expression
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateCampaignNegativeTargetingClauses(string $profileId, array $campaignNegativeTargetingClauses, ?string $prefer = null): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sp/campaignNegativeTargets',
            profileId: $profileId,
            body: ['campaignNegativeTargetingClauses' => array_values($campaignNegativeTargetingClauses)],
            contentType: self::CT_CAMPAIGN_NEGATIVE_TARGETING_CLAUSE,
            headers: $this->preferHeader($prefer),
        );
    }

    /**
     * Arquiva cláusulas negativas de campanha por id.
     *
     * @param  list<string>  $targetIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function deleteCampaignNegativeTargetingClauses(string $profileId, array $targetIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaignNegativeTargets/delete',
            profileId: $profileId,
            body: ['campaignNegativeTargetIdFilter' => ['include' => array_values($targetIds)]],
            contentType: self::CT_CAMPAIGN_NEGATIVE_TARGETING_CLAUSE,
        );
    }

    /**
     * Lista cláusulas negativas de campanha. Pagina por `nextToken`.
     *
     * @param  array<string, mixed>  $body  campaignNegativeTargetIdFilter, campaignIdFilter, stateFilter, asinFilter, includeExtendedDataFields, maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listCampaignNegativeTargetingClauses(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/campaignNegativeTargets/list',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_CAMPAIGN_NEGATIVE_TARGETING_CLAUSE,
        );
    }

    /* ----------------------------------------------------------------
     | Product targeting (categorias, marcas, refinamentos, contagem)
     * ---------------------------------------------------------------- */

    /**
     * Marcas sugeridas pra segmentação negativa (GET).
     *
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getNegativeBrands(string $profileId): array
    {
        return $this->request(
            method: 'GET',
            path: '/sp/negativeTargets/brands/recommendations',
            profileId: $profileId,
            accept: self::ACCEPT_PRODUCT_TARGETING_V3,
        );
    }

    /**
     * Busca marcas por texto pra segmentação negativa.
     *
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function searchBrands(string $profileId, string $keyword): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/negativeTargets/brands/search',
            profileId: $profileId,
            body: ['keyword' => $keyword],
            contentType: self::CT_PRODUCT_TARGETING,
            accept: self::ACCEPT_PRODUCT_TARGETING_V3,
        );
    }

    /**
     * Árvore de categorias segmentáveis. `$accept` escolhe a versão da
     * resposta (v3/v4/v5 — ver constantes ACCEPT_PRODUCT_TARGETING_*).
     *
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getTargetableCategories(string $profileId, ?string $locale = null, string $accept = self::ACCEPT_PRODUCT_TARGETING_V5): array
    {
        return $this->request(
            method: 'GET',
            path: '/sp/targets/categories',
            profileId: $profileId,
            query: $locale !== null ? ['locale' => $locale] : [],
            accept: $accept,
        );
    }

    /**
     * Categorias recomendadas pra uma lista de ASINs.
     *
     * @param  list<string>  $asins
     * @return array<string, mixed>|list<mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getCategoryRecommendationsForAsins(string $profileId, array $asins, bool $includeAncestor = false, ?string $locale = null, string $accept = self::ACCEPT_PRODUCT_TARGETING_V5): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/targets/categories/recommendations',
            profileId: $profileId,
            query: $locale !== null ? ['locale' => $locale] : [],
            body: ['asins' => array_values($asins), 'includeAncestor' => $includeAncestor],
            contentType: self::CT_PRODUCT_TARGETING,
            accept: $accept,
        );
    }

    /**
     * Refinamentos (marcas, faixas de preço/avaliação…) de uma categoria.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getRefinementsForCategory(string $profileId, string $categoryId, ?string $locale = null, string $accept = self::ACCEPT_PRODUCT_TARGETING_V4): array
    {
        return $this->request(
            method: 'GET',
            path: '/sp/targets/category/'.rawurlencode($categoryId).'/refinements',
            profileId: $profileId,
            query: $locale !== null ? ['locale' => $locale] : [],
            accept: $accept,
        );
    }

    /**
     * Quantidade de ASINs segmentáveis dado um conjunto de refinamentos.
     *
     * @param  array<string, mixed>  $body  category, brands [{id,name}], priceRange {min,max}, ratingRange, ageRanges, genres, isPrimeShipping
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getTargetableAsinCounts(string $profileId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/targets/products/count',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_PRODUCT_TARGETING,
            accept: self::ACCEPT_PRODUCT_TARGETING_V3,
        );
    }

    /**
     * Produtos (ASINs) recomendados pra segmentação. Pagina por `cursor`.
     * `$accept` escolhe a resposta por ASINs (padrão) ou por temas
     * (`application/vnd.spproductrecommendationresponse.themes.v3+json`).
     *
     * @param  array<string, mixed>  $body  adAsins [], count, cursor?, locale?
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getProductRecommendations(string $profileId, array $body, string $accept = 'application/vnd.spproductrecommendationresponse.asins.v3+json'): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/targets/products/recommendations',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.spproductrecommendation.v3+json',
            accept: $accept,
        );
    }

    /* ----------------------------------------------------------------
     | Recomendações de lance e keyword
     * ---------------------------------------------------------------- */

    /**
     * Recomendação de lance por tema pra um ad group (existente ou novo).
     * `$version` = v3|v4|v5 (media type `application/vnd.spthemebasedbidrecommendation.<v>+json`).
     *
     * @param  array<string, mixed>  $body  {campaignId, adGroupId, recommendationType: BIDS_FOR_EXISTING_AD_GROUP, targetingExpressions [{type, value?}]} ou {asins|productDetailsList, bidding, recommendationType: BIDS_FOR_NEW_AD_GROUP, targetingExpressions}; includeAnalysis (v5)
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getThemeBasedBidRecommendationForAdGroup(string $profileId, array $body, string $version = 'v5'): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/targets/bid/recommendations',
            profileId: $profileId,
            body: $body,
            contentType: "application/vnd.spthemebasedbidrecommendation.{$version}+json",
        );
    }

    /**
     * Keywords recomendadas (ranqueadas) pra ad group existente ou ASINs.
     * `$version` = v3|v4|v5 (media type `application/vnd.spkeywordsrecommendation.<v>+json`).
     *
     * @param  array<string, mixed>  $body  {recommendationType: KEYWORDS_FOR_ADGROUP, campaignId, adGroupId} ou {recommendationType: KEYWORDS_FOR_ASINS, asins|productDetailsList}; maxRecommendations, sortDimension, locale, bidsEnabled, biddingStrategy (v5)
     * @param  array<string, string>  $headers  opcionais: Amazon-Advertising-API-MarketplaceId, Amazon-Advertising-API-AdvertiserId
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getRankedKeywordRecommendation(string $profileId, array $body, string $version = 'v5', array $headers = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/targets/keywords/recommendations',
            profileId: $profileId,
            body: $body,
            contentType: "application/vnd.spkeywordsrecommendation.{$version}+json",
            headers: $headers,
        );
    }

    /**
     * Recomendação de lance por tema MULTI-PAÍS (campanhas globais). Não usa
     * Scope: o escopo vai no header `Amazon-Ads-AccountId` (id da conta global).
     *
     * @param  array<string, mixed>  $body  countryCodes [], recommendationType, targetingExpressions [{type, countryValues}], campaignId/adGroupId ou products/bidding, includeAnalysis
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getMultiCountryThemeBasedBidRecommendationForAdGroup(string $accountId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/global/targets/bid/recommendations',
            body: $body,
            contentType: 'application/json',
            accept: 'application/vnd.spthemebasedglobalbidrecommendation.v1+json',
            headers: ['Amazon-Ads-AccountId' => $accountId],
        );
    }

    /**
     * Keywords recomendadas pra campanhas GLOBAIS (multi-país). Exige Scope
     * E `Amazon-Ads-AccountId`.
     *
     * @param  array<string, mixed>  $body  products [] ou targets [{countryKeywords, matchType}], recommendationType…
     * @param  array<string, string>  $headers  opcionais: Amazon-Advertising-API-MarketplaceId, Amazon-Advertising-API-AdvertiserId
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getGlobalRankedKeywordRecommendation(string $profileId, string $accountId, array $body, array $headers = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/global/targets/keywords/recommendations/list',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.spkeywordsrecommendation.v5+json',
            headers: ['Amazon-Ads-AccountId' => $accountId] + $headers,
        );
    }

    /**
     * Grupos de keywords recomendados pra ASINs (v1.0). Scope e
     * `Amazon-Ads-AccountId` são opcionais na spec (um dos dois identifica a
     * conta). Pagina por `nextToken`.
     *
     * @param  array<string, mixed>  $body  asins [], countryCode, nextToken?
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getKeywordGroupRecommendations(?string $profileId, array $body, ?string $accountId = null, ?string $locale = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/targeting/recommendations/keywordGroups',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.spkeywordgroupsrecommendations.v1.0+json',
            headers: array_filter(['Amazon-Ads-AccountId' => $accountId, 'locale' => $locale], static fn ($v) => $v !== null),
        );
    }

    /* ----------------------------------------------------------------
     | Regras de otimização de campanha (legado v1) — /sp/rules/campaignOptimization
     * ---------------------------------------------------------------- */

    /**
     * Cria regra de otimização de campanha (`/sp/rules/campaignOptimization`,
     * media type `application/vnd.optimizationrules.v1+json`). Nome derivado
     * do path pra não colidir com createOptimizationRules().
     *
     * @param  array<string, mixed>  $body  ruleName, ruleType, ruleAction, recurrence, ruleCondition [{metricName, comparisonOperator, threshold}], campaignIds []
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createCampaignOptimizationRule(string $profileId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/rules/campaignOptimization',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_OPTIMIZATION_RULES,
        );
    }

    /**
     * Atualiza regra de otimização de campanha.
     *
     * @param  array<string, mixed>  $body  campaignOptimizationId + campos de createCampaignOptimizationRule()
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateCampaignOptimizationRule(string $profileId, array $body): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sp/rules/campaignOptimization',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_OPTIMIZATION_RULES,
        );
    }

    /**
     * Elegibilidade das campanhas pra regras de otimização.
     *
     * @param  list<string>  $campaignIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getOptimizationRuleEligibility(string $profileId, array $campaignIds, bool $requirePerformanceMetrics = false): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/rules/campaignOptimization/eligibility',
            profileId: $profileId,
            body: ['campaignIds' => array_values($campaignIds), 'requirePerformanceMetrics' => $requirePerformanceMetrics],
            contentType: self::CT_OPTIMIZATION_RULES,
        );
    }

    /**
     * Estado/notificações das regras de otimização por campanha
     * (`/sp/rules/campaignOptimization/state`).
     *
     * @param  list<string>  $campaignIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getRuleNotification(string $profileId, array $campaignIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/rules/campaignOptimization/state',
            profileId: $profileId,
            body: ['campaignIds' => array_values($campaignIds)],
            contentType: self::CT_OPTIMIZATION_RULES,
        );
    }

    /**
     * Uma regra de otimização de campanha pelo id.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getCampaignOptimizationRule(string $profileId, string $campaignOptimizationId): array
    {
        return $this->request(
            method: 'GET',
            path: '/sp/rules/campaignOptimization/'.rawurlencode($campaignOptimizationId),
            profileId: $profileId,
            accept: self::CT_OPTIMIZATION_RULES,
        );
    }

    /**
     * Apaga uma regra de otimização de campanha.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function deleteCampaignOptimizationRule(string $profileId, string $campaignOptimizationId): array
    {
        return $this->request(
            method: 'DELETE',
            path: '/sp/rules/campaignOptimization/'.rawurlencode($campaignOptimizationId),
            profileId: $profileId,
            accept: self::CT_OPTIMIZATION_RULES,
        );
    }

    /* ----------------------------------------------------------------
     | Regras de otimização — /sp/rules/optimization (v1|v2)
     * ---------------------------------------------------------------- */

    /**
     * Cria regras de otimização. `$version` = v1|v2
     * (`application/vnd.spoptimizationrules.<v>+json`).
     *
     * @param  list<array<string, mixed>>  $optimizationRules  cada uma com ruleName, ruleCategory, ruleSubCategory, status, recurrence, conditions, action, targeting (v2)
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createOptimizationRules(string $profileId, array $optimizationRules, string $version = 'v2'): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/rules/optimization',
            profileId: $profileId,
            body: ['optimizationRules' => array_values($optimizationRules)],
            contentType: "application/vnd.spoptimizationrules.{$version}+json",
        );
    }

    /**
     * Atualiza regras de otimização. `$version` = v1|v2.
     *
     * @param  list<array<string, mixed>>  $optimizationRules  cada uma com optimizationRuleId + campos a alterar
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateOptimizationRules(string $profileId, array $optimizationRules, string $version = 'v2'): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sp/rules/optimization',
            profileId: $profileId,
            body: ['optimizationRules' => array_values($optimizationRules)],
            contentType: "application/vnd.spoptimizationrules.{$version}+json",
        );
    }

    /**
     * Busca regras de otimização. `$version` = v1|v2. Pagina por `nextToken`.
     *
     * @param  array<string, mixed>  $body  optimizationRuleFilter, campaignFilter, maxResults (v2) | pageSize (v1), sortBy (v2), nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function searchOptimizationRules(string $profileId, array $body = [], string $version = 'v2'): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/rules/optimization/search',
            profileId: $profileId,
            body: $body,
            contentType: "application/vnd.spoptimizationrules.{$version}+json",
        );
    }

    /* ----------------------------------------------------------------
     | Target promotion groups — /sp/targetPromotionGroups (v1|v2)
     * ---------------------------------------------------------------- */

    /**
     * Cria target promotion group. `$version` = v1|v2
     * (`application/vnd.sptargetpromotiongroup.<v>+json`).
     *
     * @param  array<string, mixed>  $body  adGroupId, adIds [], targetPromotionGroupName (v2), existingCampaignDetails | newCampaignDetails
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createTargetPromotionGroups(string $profileId, array $body, string $version = 'v2'): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/targetPromotionGroups',
            profileId: $profileId,
            body: $body,
            contentType: "application/vnd.sptargetpromotiongroup.{$version}+json",
        );
    }

    /**
     * Lista target promotion groups. `$version` = v1|v2. Pagina por `nextToken`.
     *
     * @param  array<string, mixed>  $body  targetPromotionGroupIdFilter, adGroupIdFilter (v1) | sourceAdGroupIdFilter/destinationAdGroupIdFilter (v2), maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listTargetPromotionGroups(string $profileId, array $body = [], string $version = 'v2'): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/targetPromotionGroups/list',
            profileId: $profileId,
            body: $body,
            contentType: "application/vnd.sptargetpromotiongroup.{$version}+json",
        );
    }

    /**
     * Recomendações de target promotion groups (v1). Pagina por `nextToken`.
     *
     * @param  array<string, mixed>  $body  campaignIdFilter, adGroupIdFilter, adIdFilter, maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getTargetPromotionGroupsRecommendations(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/targetPromotionGroups/recommendations',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.spTargetPromotionGroupsRecommendations.v1+json',
        );
    }

    /**
     * Adiciona targets a um target promotion group. `$version` = v1|v2
     * (`application/vnd.sptargetpromotiongrouptarget.<v>+json`).
     *
     * @param  list<array<string, mixed>>  $targets  cada um com target, expressionType, bid
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createTargetPromotionGroupTargets(string $profileId, string $targetPromotionGroupId, array $targets, string $version = 'v2'): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/targetPromotionGroups/targets',
            profileId: $profileId,
            body: ['targetPromotionGroupId' => $targetPromotionGroupId, 'targets' => array_values($targets)],
            contentType: "application/vnd.sptargetpromotiongrouptarget.{$version}+json",
        );
    }

    /**
     * Lista targets de target promotion groups. `$version` = v1|v2. Pagina por `nextToken`.
     *
     * @param  array<string, mixed>  $body  targetPromotionGroupIdFilter, adGroupIdFilter, maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listTargetPromotionGroupTargets(string $profileId, array $body = [], string $version = 'v2'): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/targetPromotionGroups/targets/list',
            profileId: $profileId,
            body: $body,
            contentType: "application/vnd.sptargetpromotiongrouptarget.{$version}+json",
        );
    }

    /* ----------------------------------------------------------------
     | Eventos de regras — /sp/v1/events
     * ---------------------------------------------------------------- */

    /**
     * Eventos gerados pelas regras (orçamento/otimização) do anunciante.
     * A spec não tipa o corpo (objeto livre); passe filtros quando houver.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getAllRuleEvents(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sp/v1/events',
            profileId: $profileId,
            body: $body,
        );
    }

    /** @return array<string, string> */
    private function preferHeader(?string $prefer): array
    {
        return $prefer !== null ? ['Prefer' => $prefer] : [];
    }
}

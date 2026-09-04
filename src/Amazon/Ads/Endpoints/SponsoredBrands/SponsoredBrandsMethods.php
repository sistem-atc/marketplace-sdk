<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads\Endpoints\SponsoredBrands;

use SistemAtc\Marketplaces\Amazon\Ads\Bases\BaseMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;

/**
 * Sponsored Brands — versão ATUAL (v4 + rotas não versionadas da spec
 * `dest_SponsoredBrands_prod_3p`), mais Category Benchmark e Pre-Moderation.
 *
 * Todas as rotas exigem `Amazon-Advertising-API-Scope` (profileId, sempre o
 * primeiro argumento). As rotas v4 usam media types próprios em Content-Type
 * e Accept (`application/vnd.sbcampaignresource.v4+json`, `…sbadgroupresource…`,
 * `…sbadresource…`, `…sbAdCreativeResource…`); as listagens são POST com body
 * de filtros e paginam por `nextToken` (`maxResults` até 100).
 *
 * O que só existe em v3 (keywords, targets, negatives, themes, brands, stores,
 * moderation, relatórios v2) mora em SponsoredBrandsV3Methods.
 */
class SponsoredBrandsMethods extends BaseMethods
{
    public const CT_CAMPAIGN = 'application/vnd.sbcampaignresource.v4+json';

    public const CT_AD_GROUP = 'application/vnd.sbadgroupresource.v4+json';

    public const CT_AD = 'application/vnd.sbadresource.v4+json';

    public const CT_CREATIVE = 'application/vnd.sbAdCreativeResource.v4+json';

    public const CT_TARGETING = 'application/vnd.sbtargeting.v4+json';

    public const CT_RULE_OPTIMIZATION = 'application/vnd.sbruleoptimization.v4+json';

    public const CT_MIGRATION = 'application/vnd.sbmigrationapi.v4+json';

    // ------------------------------------------------------------------
    // Campaigns v4
    // ------------------------------------------------------------------

    /**
     * POST /sb/v4/campaigns — cria campanhas (até 100 por chamada).
     *
     * @param  list<array<string, mixed>>  $campaigns  cada uma com name, state, budget, budgetType, brandEntityId, startDate, endDate, bidding, goal, costType, portfolioId, productLocation…
     * @return array<string, mixed>  {campaigns: {success: [...], error: [...]}}
     *
     * @throws AmazonAdsRequestException
     */
    public function createCampaigns(string $profileId, array $campaigns): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/v4/campaigns',
            profileId: $profileId,
            body: ['campaigns' => $campaigns],
            contentType: self::CT_CAMPAIGN,
        );
    }

    /**
     * PUT /sb/v4/campaigns — atualiza campanhas (campaignId obrigatório em cada item).
     *
     * @param  list<array<string, mixed>>  $campaigns  campaignId + name, state, budget, startDate, endDate, bidding, portfolioId, tags
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateCampaigns(string $profileId, array $campaigns): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sb/v4/campaigns',
            profileId: $profileId,
            body: ['campaigns' => $campaigns],
            contentType: self::CT_CAMPAIGN,
        );
    }

    /**
     * POST /sb/v4/campaigns/delete — arquiva campanhas por id.
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
            path: '/sb/v4/campaigns/delete',
            profileId: $profileId,
            body: ['campaignIdFilter' => ['include' => array_values($campaignIds)]],
            contentType: self::CT_CAMPAIGN,
        );
    }

    /**
     * POST /sb/v4/campaigns/list — lista campanhas com filtros; pagina por nextToken.
     *
     * @param  array<string, mixed>  $body  campaignIdFilter, stateFilter, nameFilter, portfolioIdFilter, goalTypeFilter, includeExtendedDataFields, maxResults, nextToken
     * @return array<string, mixed>  {campaigns: [...], totalResults, nextToken?}
     *
     * @throws AmazonAdsRequestException
     */
    public function listCampaigns(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/v4/campaigns/list',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_CAMPAIGN,
        );
    }

    // ------------------------------------------------------------------
    // Ad groups v4
    // ------------------------------------------------------------------

    /**
     * POST /sb/v4/adGroups — cria grupos de anúncio.
     *
     * @param  list<array<string, mixed>>  $adGroups  campaignId, name, state
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createAdGroups(string $profileId, array $adGroups): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/v4/adGroups',
            profileId: $profileId,
            body: ['adGroups' => $adGroups],
            contentType: self::CT_AD_GROUP,
        );
    }

    /**
     * PUT /sb/v4/adGroups — atualiza grupos (adGroupId + name/state).
     *
     * @param  list<array<string, mixed>>  $adGroups
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateAdGroups(string $profileId, array $adGroups): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sb/v4/adGroups',
            profileId: $profileId,
            body: ['adGroups' => $adGroups],
            contentType: self::CT_AD_GROUP,
        );
    }

    /**
     * POST /sb/v4/adGroups/delete — arquiva grupos por id.
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
            path: '/sb/v4/adGroups/delete',
            profileId: $profileId,
            body: ['adGroupIdFilter' => ['include' => array_values($adGroupIds)]],
            contentType: self::CT_AD_GROUP,
        );
    }

    /**
     * POST /sb/v4/adGroups/list — lista grupos; pagina por nextToken.
     *
     * @param  array<string, mixed>  $body  adGroupIdFilter, campaignIdFilter, stateFilter, nameFilter, includeExtendedDataFields, maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listAdGroups(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/v4/adGroups/list',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_AD_GROUP,
        );
    }

    // ------------------------------------------------------------------
    // Ads v4
    // ------------------------------------------------------------------

    /**
     * PUT /sb/v4/ads — atualiza anúncios (adId + name/state).
     *
     * @param  list<array<string, mixed>>  $ads
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateAds(string $profileId, array $ads): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sb/v4/ads',
            profileId: $profileId,
            body: ['ads' => $ads],
            contentType: self::CT_AD,
        );
    }

    /**
     * POST /sb/v4/ads/delete — arquiva anúncios por id.
     *
     * @param  list<string>  $adIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function deleteAds(string $profileId, array $adIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/v4/ads/delete',
            profileId: $profileId,
            body: ['adIdFilter' => ['include' => array_values($adIds)]],
            contentType: self::CT_AD,
        );
    }

    /**
     * POST /sb/v4/ads/list — lista anúncios; pagina por nextToken.
     *
     * @param  array<string, mixed>  $body  adIdFilter, adGroupIdFilter, campaignIdFilter, stateFilter, nameFilter, creativeVersionToReturn, maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listAds(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/v4/ads/list',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_AD,
        );
    }

    /**
     * POST /sb/v4/ads/productCollection — cria anúncios Product Collection.
     *
     * @param  list<array<string, mixed>>  $ads  adGroupId, name, state, creative, landingPage
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createProductCollectionAds(string $profileId, array $ads): array
    {
        return $this->createAdsOf('productCollection', $profileId, $ads);
    }

    /**
     * POST /sb/v4/ads/productCollectionExtended — Product Collection com 1–5 imagens custom.
     *
     * @param  list<array<string, mixed>>  $ads  adGroupId, name, state, creative, landingPage
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createExtendedProductCollectionAds(string $profileId, array $ads): array
    {
        return $this->createAdsOf('productCollectionExtended', $profileId, $ads);
    }

    /**
     * POST /sb/v4/ads/storeSpotlight — cria anúncios Store Spotlight.
     *
     * @param  list<array<string, mixed>>  $ads  adGroupId, name, state, creative, landingPage
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createStoreSpotlightAds(string $profileId, array $ads): array
    {
        return $this->createAdsOf('storeSpotlight', $profileId, $ads);
    }

    /**
     * POST /sb/v4/ads/video — cria anúncios de vídeo.
     *
     * @param  list<array<string, mixed>>  $ads  adGroupId, name, state, creative
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createVideoAds(string $profileId, array $ads): array
    {
        return $this->createAdsOf('video', $profileId, $ads);
    }

    /**
     * POST /sb/v4/ads/brandVideo — cria anúncios Brand Video.
     *
     * @param  list<array<string, mixed>>  $ads  adGroupId, name, state, creative, landingPage
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createBrandVideoAds(string $profileId, array $ads): array
    {
        return $this->createAdsOf('brandVideo', $profileId, $ads);
    }

    /**
     * POST /sb/v4/ads/autoCollection — cria anúncios Auto Collection.
     *
     * @param  list<array<string, mixed>>  $ads  adGroupId, name, state, creative
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createAutoCollectionAds(string $profileId, array $ads): array
    {
        return $this->createAdsOf('autoCollection', $profileId, $ads);
    }

    /**
     * POST /sb/v4/ads/manualCollection — cria anúncios Manual Collection.
     *
     * @param  list<array<string, mixed>>  $ads  adGroupId, name, state, creative
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createManualCollectionAds(string $profileId, array $ads): array
    {
        return $this->createAdsOf('manualCollection', $profileId, $ads);
    }

    /**
     * @param  list<array<string, mixed>>  $ads
     * @return array<string, mixed>
     */
    private function createAdsOf(string $kind, string $profileId, array $ads): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/v4/ads/'.$kind,
            profileId: $profileId,
            body: ['ads' => $ads],
            contentType: self::CT_AD,
        );
    }

    // ------------------------------------------------------------------
    // Creatives (nova versão do criativo de um anúncio existente)
    // ------------------------------------------------------------------

    /**
     * POST /sb/ads/creatives/list — criativos (todas as versões) de um anúncio; pagina por nextToken.
     *
     * @param  array<string, mixed>  $filters  creativeStatusFilter, creativeTypeFilter, creativeVersionFilter, maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listCreatives(string $profileId, string $adId, array $filters = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/ads/creatives/list',
            profileId: $profileId,
            body: ['adId' => $adId] + $filters,
            contentType: self::CT_CREATIVE,
        );
    }

    /**
     * POST /sb/ads/creatives/productCollection — nova versão de criativo Product Collection.
     * Em deprecação na spec: prefira createExtendedProductCollectionCreative().
     *
     * @param  array<string, mixed>  $creative  asins, brandLogoAssetId, brandLogoCrop, brandName, customImageAssetId, customImageCrop, headline
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createProductCollectionCreative(string $profileId, string $adId, array $creative): array
    {
        return $this->createCreativeOf('productCollection', $profileId, $adId, $creative);
    }

    /**
     * POST /sb/ads/creatives/productCollectionExtended — nova versão de criativo Product Collection estendido.
     *
     * @param  array<string, mixed>  $creative  asins, companionAsins, brandLogoAssetId, brandName, customImages, headline(s), landingPage, collectionType, consentToTranslate…
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createExtendedProductCollectionCreative(string $profileId, string $adId, array $creative): array
    {
        return $this->createCreativeOf('productCollectionExtended', $profileId, $adId, $creative);
    }

    /**
     * POST /sb/ads/creatives/storeSpotlight — nova versão de criativo Store Spotlight.
     *
     * @param  array<string, mixed>  $creative  brandLogoAssetId, brandName, headline(s), subpages, landingPage…
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createStoreSpotlightCreative(string $profileId, string $adId, array $creative): array
    {
        return $this->createCreativeOf('storeSpotlight', $profileId, $adId, $creative);
    }

    /**
     * POST /sb/ads/creatives/video — nova versão de criativo de vídeo.
     *
     * @param  array<string, mixed>  $creative  videoAssetIds, brandLogoAssetId, brandName, headline…
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createVideoCreative(string $profileId, string $adId, array $creative): array
    {
        return $this->createCreativeOf('video', $profileId, $adId, $creative);
    }

    /**
     * POST /sb/ads/creatives/brandVideo — nova versão de criativo Brand Video.
     *
     * @param  array<string, mixed>  $creative  asins, videoAssetIds, brandLogoAssetId, brandName, headline, landingPage…
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createBrandVideoCreative(string $profileId, string $adId, array $creative): array
    {
        return $this->createCreativeOf('brandVideo', $profileId, $adId, $creative);
    }

    /**
     * @param  array<string, mixed>  $creative
     * @return array<string, mixed>
     */
    private function createCreativeOf(string $kind, string $profileId, string $adId, array $creative): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/ads/creatives/'.$kind,
            profileId: $profileId,
            body: ['adId' => $adId, 'creative' => $creative],
            contentType: self::CT_CREATIVE,
        );
    }

    /**
     * POST /sb/ads/creatives/autoCollection — nova versão do criativo de anúncios Auto Collection (lote).
     *
     * @param  list<array<string, mixed>>  $ads  cada um {adId, creative}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateAutoCollectionAdsCreative(string $profileId, array $ads): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/ads/creatives/autoCollection',
            profileId: $profileId,
            body: ['ads' => $ads],
            contentType: self::CT_AD,
        );
    }

    /**
     * POST /sb/ads/creatives/manualCollection — nova versão do criativo de anúncios Manual Collection (lote).
     *
     * @param  list<array<string, mixed>>  $ads  cada um {adId, creative}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateManualCollectionAdsCreative(string $profileId, array $ads): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/ads/creatives/manualCollection',
            profileId: $profileId,
            body: ['ads' => $ads],
            contentType: self::CT_AD,
        );
    }

    // ------------------------------------------------------------------
    // Budget rules
    // ------------------------------------------------------------------

    /**
     * GET /sb/budgetRules — regras de orçamento do anunciante; pagina por nextToken.
     *
     * @return array<string, mixed>  {associatedRules: [...], nextToken?}
     *
     * @throws AmazonAdsRequestException
     */
    public function getBudgetRules(string $profileId, int $pageSize = 30, ?string $nextToken = null): array
    {
        return $this->request(
            method: 'GET',
            path: '/sb/budgetRules',
            profileId: $profileId,
            query: array_filter(['pageSize' => $pageSize, 'nextToken' => $nextToken], fn ($v) => $v !== null),
            accept: 'application/json',
        );
    }

    /**
     * POST /sb/budgetRules — cria regras de orçamento.
     *
     * @param  list<array<string, mixed>>  $budgetRulesDetails  name, ruleType, duration, recurrence, budgetIncreaseBy, performanceMeasureCondition
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createBudgetRules(string $profileId, array $budgetRulesDetails): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/budgetRules',
            profileId: $profileId,
            body: ['budgetRulesDetails' => $budgetRulesDetails],
            contentType: 'application/json',
        );
    }

    /**
     * PUT /sb/budgetRules — atualiza regras (ruleId obrigatório).
     *
     * @param  list<array<string, mixed>>  $budgetRulesDetails  ruleId, ruleState, ruleDetails…
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateBudgetRules(string $profileId, array $budgetRulesDetails): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sb/budgetRules',
            profileId: $profileId,
            body: ['budgetRulesDetails' => $budgetRulesDetails],
            contentType: 'application/json',
        );
    }

    /**
     * GET /sb/budgetRules/{budgetRuleId} — uma regra de orçamento.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getBudgetRule(string $profileId, string $budgetRuleId): array
    {
        return $this->request(
            method: 'GET',
            path: '/sb/budgetRules/'.rawurlencode($budgetRuleId),
            profileId: $profileId,
            accept: 'application/json',
        );
    }

    /**
     * GET /sb/budgetRules/{budgetRuleId}/campaigns — campanhas associadas à regra; pagina por nextToken.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getCampaignsForBudgetRule(string $profileId, string $budgetRuleId, int $pageSize = 30, ?string $nextToken = null): array
    {
        return $this->request(
            method: 'GET',
            path: '/sb/budgetRules/'.rawurlencode($budgetRuleId).'/campaigns',
            profileId: $profileId,
            query: array_filter(['pageSize' => $pageSize, 'nextToken' => $nextToken], fn ($v) => $v !== null),
            accept: 'application/json',
        );
    }

    /**
     * GET /sb/campaigns/{campaignId}/budgetRules — regras associadas à campanha.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listCampaignBudgetRules(string $profileId, string $campaignId): array
    {
        return $this->request(
            method: 'GET',
            path: '/sb/campaigns/'.rawurlencode($campaignId).'/budgetRules',
            profileId: $profileId,
            accept: 'application/json',
        );
    }

    /**
     * POST /sb/campaigns/{campaignId}/budgetRules — associa regras à campanha.
     *
     * @param  list<string>  $budgetRuleIds
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function associateBudgetRules(string $profileId, string $campaignId, array $budgetRuleIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/campaigns/'.rawurlencode($campaignId).'/budgetRules',
            profileId: $profileId,
            body: ['budgetRuleIds' => array_values($budgetRuleIds)],
            contentType: 'application/json',
        );
    }

    /**
     * DELETE /sb/campaigns/{campaignId}/budgetRules/{budgetRuleId} — desassocia a regra da campanha.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function disassociateBudgetRule(string $profileId, string $campaignId, string $budgetRuleId): array
    {
        return $this->request(
            method: 'DELETE',
            path: '/sb/campaigns/'.rawurlencode($campaignId).'/budgetRules/'.rawurlencode($budgetRuleId),
            profileId: $profileId,
            accept: 'application/json',
        );
    }

    /**
     * POST /sb/campaigns/budgetRules/recommendations — eventos especiais com sugestão de aumento de orçamento
     * pra uma campanha (media type v3).
     *
     * @param  string|null  $accountId  header opcional Amazon-Ads-AccountId
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getBudgetRulesRecommendation(string $profileId, string $campaignId, string $recommendationType = 'EVENTS_FOR_EXISTING_CAMPAIGN', ?string $accountId = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/campaigns/budgetRules/recommendations',
            profileId: $profileId,
            body: ['recommendationType' => $recommendationType, 'campaignId' => $campaignId],
            contentType: 'application/vnd.sbbudgetrulesrecommendation.v3+json',
            headers: $accountId !== null ? ['Amazon-Ads-AccountId' => $accountId] : [],
        );
    }

    // ------------------------------------------------------------------
    // Budget: uso e recomendações
    // ------------------------------------------------------------------

    /**
     * POST /sb/campaigns/budget/usage — % de uso do orçamento por campanha (media type v1).
     *
     * @param  list<string>  $campaignIds
     * @return array<string, mixed>  {success: [...], error: [...]}
     *
     * @throws AmazonAdsRequestException
     */
    public function campaignsBudgetUsage(string $profileId, array $campaignIds): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/campaigns/budget/usage',
            profileId: $profileId,
            body: ['campaignIds' => array_values($campaignIds)],
            contentType: 'application/vnd.sbcampaignbudgetusage.v1+json',
        );
    }

    /**
     * POST /sb/campaigns/budgetRecommendations — recomendação de orçamento diário (v4).
     *
     * @param  list<string>  $campaignIds
     * @param  string|null  $accountId  header opcional Amazon-Ads-AccountId
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getBudgetRecommendations(string $profileId, array $campaignIds, ?string $accountId = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/campaigns/budgetRecommendations',
            profileId: $profileId,
            body: ['campaignIds' => array_values($campaignIds)],
            contentType: 'application/vnd.sbbudgetrecommendation.v4+json',
            headers: $accountId !== null ? ['Amazon-Ads-AccountId' => $accountId] : [],
        );
    }

    // ------------------------------------------------------------------
    // Insights / forecasts
    // ------------------------------------------------------------------

    /**
     * POST /sb/campaigns/insights — insights pra uma campanha hipotética (v4); pagina por nextToken na query.
     *
     * @param  list<array<string, mixed>>  $adGroups  cada um {adFormat, keywords[]}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function campaignInsights(string $profileId, array $adGroups, ?string $nextToken = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/campaigns/insights',
            profileId: $profileId,
            query: $nextToken !== null ? ['nextToken' => $nextToken] : [],
            body: ['adGroups' => $adGroups],
            contentType: 'application/vnd.sbinsights.v4+json',
        );
    }

    /**
     * POST /sb/forecasts — previsão de desempenho de campanhas novas (v4; hoje só 1 campanha por chamada).
     *
     * @param  list<array<string, mixed>>  $campaigns  forecastType, budget, budgetType, adGroups[], goal, startDate, endDate, optimizationRules
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function campaignPerformanceForecasts(string $profileId, array $campaigns): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/forecasts',
            profileId: $profileId,
            body: ['campaigns' => $campaigns],
            contentType: 'application/vnd.sbforecasting.v4+json',
        );
    }

    // ------------------------------------------------------------------
    // Recomendações
    // ------------------------------------------------------------------

    /**
     * POST /sb/recommendations/creative/headline — sugestões de headline pro criativo.
     *
     * @param  array<string, mixed>  $body  adFormat, asins[], storePages[], maxNumSuggestions
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getHeadlineRecommendations(string $profileId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/recommendations/creative/headline',
            profileId: $profileId,
            body: $body,
            contentType: 'application/json',
        );
    }

    /**
     * POST /sb/recommendations/keyword — sugestões de keyword (media type v3).
     * Body com EXATAMENTE um de `asins[]` ou `url`; opcionais creativeType, goal, locale, maxNumSuggestions.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getKeywordRecommendations(string $profileId, array $body): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/recommendations/keyword',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.sbkeywordrecommendation.v3+json',
        );
    }

    /**
     * POST /sb/recommendations/optimization — recomendação de regra de otimização (v4).
     *
     * @param  list<array<string, mixed>>  $landingPages  cada uma {pageType, url?, asins?}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getOptimizationRecommendation(string $profileId, string $costControlMetric, array $landingPages): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/recommendations/optimization',
            profileId: $profileId,
            body: ['costControlMetric' => $costControlMetric, 'landingPages' => $landingPages],
            contentType: 'application/vnd.sboptimizationrecommendationresource.v4+json',
        );
    }

    /**
     * GET /sb/negativeTargets/brands/recommendations — marcas sugeridas pra negativação (inclui as próprias); pagina por nextToken.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getNegativeBrandsRecommendations(string $profileId, ?string $nextToken = null): array
    {
        return $this->request(
            method: 'GET',
            path: '/sb/negativeTargets/brands/recommendations',
            profileId: $profileId,
            query: $nextToken !== null ? ['nextToken' => $nextToken] : [],
            accept: self::CT_TARGETING,
        );
    }

    // ------------------------------------------------------------------
    // Optimization rules v4
    // ------------------------------------------------------------------

    /**
     * POST /sb/rules/optimization — cria regras de otimização.
     *
     * @param  list<array<string, mixed>>  $optimizationRules  entityType, entityId, conditions[]
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function createOptimizationRules(string $profileId, array $optimizationRules): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/rules/optimization',
            profileId: $profileId,
            body: ['optimizationRules' => $optimizationRules],
            contentType: self::CT_RULE_OPTIMIZATION,
        );
    }

    /**
     * PUT /sb/rules/optimization — atualiza regras (optimizationRuleId + conditions).
     *
     * @param  list<array<string, mixed>>  $optimizationRules
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function updateOptimizationRules(string $profileId, array $optimizationRules): array
    {
        return $this->request(
            method: 'PUT',
            path: '/sb/rules/optimization',
            profileId: $profileId,
            body: ['optimizationRules' => $optimizationRules],
            contentType: self::CT_RULE_OPTIMIZATION,
        );
    }

    /**
     * POST /sb/rules/optimization/associate — associa regras a entidades.
     *
     * @param  list<array<string, mixed>>  $associations  cada uma {entityType, entityId, optimizationRuleId}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function associateOptimizationRules(string $profileId, array $associations): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/rules/optimization/associate',
            profileId: $profileId,
            body: ['optimizationRuleAssociations' => $associations],
            contentType: self::CT_RULE_OPTIMIZATION,
        );
    }

    /**
     * POST /sb/rules/optimization/disassociate — desassocia regras de entidades.
     *
     * @param  list<array<string, mixed>>  $disassociations  cada uma {entityType, entityId, optimizationRuleId}
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function disassociateOptimizationRules(string $profileId, array $disassociations): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/rules/optimization/disassociate',
            profileId: $profileId,
            body: ['optimizationRuleDisassociations' => $disassociations],
            contentType: self::CT_RULE_OPTIMIZATION,
        );
    }

    /**
     * POST /sb/rules/optimization/list — lista regras; pagina por nextToken.
     *
     * @param  array<string, mixed>  $body  entityFilter{entityType,entityId}, optimizationRuleIdFilter{include}, maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listOptimizationRules(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/rules/optimization/list',
            profileId: $profileId,
            body: $body,
            contentType: self::CT_RULE_OPTIMIZATION,
        );
    }

    // ------------------------------------------------------------------
    // Targeting v4 (categorias / refinamentos / contagem de ASINs)
    // ------------------------------------------------------------------

    /**
     * GET /sb/targets/categories — categorias segmentáveis (árvore); pagina por nextToken.
     *
     * @param  array<string, mixed>  $query  locale, includeOnlyRootCategories, parentCategoryRefinementId, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getTargetableCategories(string $profileId, string $supplySource = 'AMAZON', array $query = []): array
    {
        return $this->request(
            method: 'GET',
            path: '/sb/targets/categories',
            profileId: $profileId,
            query: ['supplySource' => $supplySource] + $query,
            accept: self::CT_TARGETING,
        );
    }

    /**
     * GET /sb/targets/categories/{categoryRefinementId}/refinements — refinamentos (marcas, faixas…) da categoria.
     *
     * @param  array<string, mixed>  $query  locale, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getRefinementsForCategory(string $profileId, string $categoryRefinementId, array $query = []): array
    {
        return $this->request(
            method: 'GET',
            path: '/sb/targets/categories/'.rawurlencode($categoryRefinementId).'/refinements',
            profileId: $profileId,
            query: $query,
            accept: self::CT_TARGETING,
        );
    }

    /**
     * POST /sb/targets/products/count — quantidade de ASINs segmentáveis dados os refinamentos.
     *
     * @param  array<string, mixed>  $refinements  brands[], ageRanges[], genres[], priceRange{min,max}, ratingRange{min,max}, isPrimeShipping
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getTargetableAsinCounts(string $profileId, string $category, array $refinements = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/targets/products/count',
            profileId: $profileId,
            body: ['category' => $category] + $refinements,
            contentType: self::CT_TARGETING,
        );
    }

    // ------------------------------------------------------------------
    // Migração de campanhas legadas (v3 → v4)
    // ------------------------------------------------------------------

    /**
     * POST /sb/v4/legacyCampaigns/migrationJob — inicia job de migração de campanhas v3.
     *
     * @param  list<string>  $campaignIds
     * @param  array<string, mixed>  $options  brandEntityId, isStagedMigration, newCampaignState
     * @return array<string, mixed>  {jobId…}
     *
     * @throws AmazonAdsRequestException
     */
    public function startMigrationJob(string $profileId, array $campaignIds, bool $enableThemeTargeting = false, array $options = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/v4/legacyCampaigns/migrationJob',
            profileId: $profileId,
            body: ['campaignIds' => array_values($campaignIds), 'enableThemeTargeting' => $enableThemeTargeting] + $options,
            contentType: self::CT_MIGRATION,
        );
    }

    /**
     * POST /sb/v4/legacyCampaigns/migrationJob/results — resultados do job; pagina por nextToken.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function migrationJobResults(string $profileId, string $jobId, ?string $nextToken = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/v4/legacyCampaigns/migrationJob/results',
            profileId: $profileId,
            body: array_filter(['jobId' => $jobId, 'nextToken' => $nextToken], fn ($v) => $v !== null),
            contentType: self::CT_MIGRATION,
        );
    }

    /**
     * POST /sb/v4/legacyCampaigns/migrationJob/status — status do job.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function migrationJobStatus(string $profileId, string $jobId): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/v4/legacyCampaigns/migrationJob/status',
            profileId: $profileId,
            body: ['jobId' => $jobId],
            contentType: self::CT_MIGRATION,
        );
    }

    /**
     * POST /sb/v4/legacyCampaigns/overallMigrationResults — resultado geral de todas as campanhas migradas; pagina por nextToken.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function migrationResults(string $profileId, ?string $nextToken = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/v4/legacyCampaigns/overallMigrationResults',
            profileId: $profileId,
            body: $nextToken !== null ? ['nextToken' => $nextToken] : [],
            contentType: self::CT_MIGRATION,
        );
    }

    /**
     * POST /sb/v4/migrations/list — anúncios Product Collection elegíveis à migração pra SB Collection; pagina por nextToken.
     *
     * @param  array<string, mixed>  $body  adIdFilter, adGroupIdFilter, campaignIdFilter, adStateFilter, migrationStatusFilter, maxResults, nextToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function listMigrations(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/sb/v4/migrations/list',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.sbAdMigration.v4+json',
        );
    }

    // ------------------------------------------------------------------
    // Category Benchmark (dest_SponsoredBrandsCategoryBenchmark)
    // ------------------------------------------------------------------

    /**
     * GET /benchmarks/brands — marcas do anunciante com benchmark disponível; pagina por nextPageToken.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getBenchmarkBrands(string $profileId, ?string $programType = null, ?string $nextPageToken = null): array
    {
        return $this->request(
            method: 'GET',
            path: '/benchmarks/brands',
            profileId: $profileId,
            query: array_filter(['programType' => $programType, 'nextPageToken' => $nextPageToken], fn ($v) => $v !== null),
            accept: 'application/vnd.brandlist.v1+json',
        );
    }

    /**
     * POST /benchmarks/brands/{brandName}/categories/{categoryId} — série temporal do benchmark (v1).
     *
     * @param  list<string>  $metrics
     * @param  array<string, mixed>  $options  startDate, endDate, granularity, window, programType, nextPageToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getBenchmarkTimeSeries(string $profileId, string $brandName, string $categoryId, array $metrics, array $options = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/benchmarks/brands/'.rawurlencode($brandName).'/categories/'.rawurlencode($categoryId),
            profileId: $profileId,
            body: ['metrics' => array_values($metrics)] + $options,
            contentType: 'application/vnd.timeseriesdata.v1+json',
        );
    }

    /**
     * POST /benchmarks/brandsAndCategories — relatório completo de benchmark por marca × categoria (v1); pagina por nextPageToken.
     *
     * @param  array<string, mixed>  $body  metrics[], startDate, endDate, programType, nextPageToken
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getBenchmarkReportData(string $profileId, array $body = []): array
    {
        return $this->request(
            method: 'POST',
            path: '/benchmarks/brandsAndCategories',
            profileId: $profileId,
            body: $body,
            contentType: 'application/vnd.reportdata.v1+json',
        );
    }

    // ------------------------------------------------------------------
    // Pre-Moderation (dest_PreModeration)
    // ------------------------------------------------------------------

    /**
     * POST /preModeration — pré-modera componentes do criativo antes de criar o anúncio.
     *
     * @param  array<string, mixed>  $body  adProgram, locale, textComponents[], imageComponents[], asinComponents[], dateComponents[], thirdPartyComponents[], recordId, targetLanguage
     * @param  string|null  $accountId  header opcional Amazon-Ads-AccountId
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function preModeration(string $profileId, array $body, ?string $accountId = null): array
    {
        return $this->request(
            method: 'POST',
            path: '/preModeration',
            profileId: $profileId,
            body: $body,
            contentType: 'application/json',
            headers: $accountId !== null ? ['Amazon-Ads-AccountId' => $accountId] : [],
        );
    }
}

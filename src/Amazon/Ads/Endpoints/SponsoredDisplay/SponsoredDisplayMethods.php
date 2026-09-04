<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads\Endpoints\SponsoredDisplay;

use SistemAtc\Marketplaces\Amazon\Ads\Bases\BaseMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;

/**
 * Sponsored Display (prefixo `/sd/`) da Amazon Ads API.
 *
 * Cobre campanhas, ad groups, product ads, targets/negative targets,
 * creatives, optimization rules, locations, brand safety, budget rules,
 * recomendações (budget/target/headline), forecasts, snapshots e o
 * reporting legado v2 (`/sd/{recordType}/report` + `/v2/reports`).
 *
 * Todas as rotas exigem o header `Amazon-Advertising-API-Scope` — por isso
 * `$profileId` é sempre o primeiro argumento. Listagens `/sd/*` paginam por
 * `startIndex`/`count` (offset), exceto budget rules, que usam `nextToken`.
 * A maioria das rotas aceita `application/json`; as que exigem media type
 * próprio (budget usage, budget recommendations, target recommendations,
 * headline recommendations, forecasts) já mandam o Content-Type/Accept certo.
 */
class SponsoredDisplayMethods extends BaseMethods
{
    // ------------------------------------------------------------------ campaigns

    /**
     * Lista campanhas SD. Query: startIndex, count, stateFilter,
     * name, campaignIdFilter, portfolioIdFilter (csv).
     *
     * @param  array<string, mixed>  $query
     * @return list<array<string, mixed>>
     */
    public function listCampaigns(string $profileId, array $query = []): array
    {
        return $this->request('GET', '/sd/campaigns', $profileId, $query);
    }

    /**
     * Lista campanhas com campos estendidos (creationDate, servingStatus…).
     *
     * @param  array<string, mixed>  $query  mesmos filtros de listCampaigns()
     * @return list<array<string, mixed>>
     */
    public function listCampaignsEx(string $profileId, array $query = []): array
    {
        return $this->request('GET', '/sd/campaigns/extended', $profileId, $query);
    }

    /**
     * Cria até 100 campanhas. Body: lista de {name, tactic, budgetType,
     * budget, startDate, endDate?, state, costType?, portfolioId?}.
     *
     * @param  list<array<string, mixed>>  $campaigns
     * @return list<array<string, mixed>>  code/campaignId por item
     */
    public function createCampaigns(string $profileId, array $campaigns): array
    {
        return $this->request('POST', '/sd/campaigns', $profileId, body: $campaigns);
    }

    /**
     * Atualiza campanhas (campaignId obrigatório em cada item).
     *
     * @param  list<array<string, mixed>>  $campaigns
     * @return list<array<string, mixed>>
     */
    public function updateCampaigns(string $profileId, array $campaigns): array
    {
        return $this->request('PUT', '/sd/campaigns', $profileId, body: $campaigns);
    }

    /** @return array<string, mixed> */
    public function getCampaign(string $profileId, string $campaignId): array
    {
        return $this->request('GET', '/sd/campaigns/'.rawurlencode($campaignId), $profileId);
    }

    /** @return array<string, mixed> */
    public function getCampaignResponseEx(string $profileId, string $campaignId): array
    {
        return $this->request('GET', '/sd/campaigns/extended/'.rawurlencode($campaignId), $profileId);
    }

    /**
     * Arquiva a campanha (state=archived; irreversível).
     *
     * @return array<string, mixed>
     */
    public function archiveCampaign(string $profileId, string $campaignId): array
    {
        return $this->request('DELETE', '/sd/campaigns/'.rawurlencode($campaignId), $profileId);
    }

    // ------------------------------------------------------------------ ad groups

    /**
     * Lista ad groups. Query: startIndex, count, stateFilter, campaignIdFilter,
     * adGroupIdFilter, name.
     *
     * @param  array<string, mixed>  $query
     * @return list<array<string, mixed>>
     */
    public function listAdGroups(string $profileId, array $query = []): array
    {
        return $this->request('GET', '/sd/adGroups', $profileId, $query);
    }

    /**
     * @param  array<string, mixed>  $query  mesmos filtros de listAdGroups()
     * @return list<array<string, mixed>>
     */
    public function listAdGroupsEx(string $profileId, array $query = []): array
    {
        return $this->request('GET', '/sd/adGroups/extended', $profileId, $query);
    }

    /**
     * Cria ad groups. Body: lista de {campaignId, name, defaultBid, state,
     * bidOptimization?, creativeType?}.
     *
     * @param  list<array<string, mixed>>  $adGroups
     * @return list<array<string, mixed>>
     */
    public function createAdGroups(string $profileId, array $adGroups): array
    {
        return $this->request('POST', '/sd/adGroups', $profileId, body: $adGroups);
    }

    /**
     * @param  list<array<string, mixed>>  $adGroups  cada um com adGroupId
     * @return list<array<string, mixed>>
     */
    public function updateAdGroups(string $profileId, array $adGroups): array
    {
        return $this->request('PUT', '/sd/adGroups', $profileId, body: $adGroups);
    }

    /** @return array<string, mixed> */
    public function getAdGroup(string $profileId, string $adGroupId): array
    {
        return $this->request('GET', '/sd/adGroups/'.rawurlencode($adGroupId), $profileId);
    }

    /** @return array<string, mixed> */
    public function getAdGroupResponseEx(string $profileId, string $adGroupId): array
    {
        return $this->request('GET', '/sd/adGroups/extended/'.rawurlencode($adGroupId), $profileId);
    }

    /** @return array<string, mixed> */
    public function archiveAdGroup(string $profileId, string $adGroupId): array
    {
        return $this->request('DELETE', '/sd/adGroups/'.rawurlencode($adGroupId), $profileId);
    }

    // ------------------------------------------------------------------ product ads

    /**
     * Lista product ads. Query: startIndex, count, stateFilter, adIdFilter,
     * adGroupIdFilter, campaignIdFilter.
     *
     * @param  array<string, mixed>  $query
     * @return list<array<string, mixed>>
     */
    public function listProductAds(string $profileId, array $query = []): array
    {
        return $this->request('GET', '/sd/productAds', $profileId, $query);
    }

    /**
     * @param  array<string, mixed>  $query  mesmos filtros de listProductAds()
     * @return list<array<string, mixed>>
     */
    public function listProductAdsEx(string $profileId, array $query = []): array
    {
        return $this->request('GET', '/sd/productAds/extended', $profileId, $query);
    }

    /**
     * Cria product ads. Body: lista de {adGroupId, sku|asin, state}.
     *
     * @param  list<array<string, mixed>>  $productAds
     * @return list<array<string, mixed>>
     */
    public function createProductAds(string $profileId, array $productAds): array
    {
        return $this->request('POST', '/sd/productAds', $profileId, body: $productAds);
    }

    /**
     * @param  list<array<string, mixed>>  $productAds  cada um com adId
     * @return list<array<string, mixed>>
     */
    public function updateProductAds(string $profileId, array $productAds): array
    {
        return $this->request('PUT', '/sd/productAds', $profileId, body: $productAds);
    }

    /** @return array<string, mixed> */
    public function getProductAd(string $profileId, string $adId): array
    {
        return $this->request('GET', '/sd/productAds/'.rawurlencode($adId), $profileId);
    }

    /** @return array<string, mixed> */
    public function getProductAdResponseEx(string $profileId, string $adId): array
    {
        return $this->request('GET', '/sd/productAds/extended/'.rawurlencode($adId), $profileId);
    }

    /** @return array<string, mixed> */
    public function archiveProductAd(string $profileId, string $adId): array
    {
        return $this->request('DELETE', '/sd/productAds/'.rawurlencode($adId), $profileId);
    }

    // ------------------------------------------------------------------ targets

    /**
     * Lista targeting clauses. Query: startIndex, count, stateFilter,
     * targetIdFilter, adGroupIdFilter, campaignIdFilter.
     *
     * @param  array<string, mixed>  $query
     * @return list<array<string, mixed>>
     */
    public function listTargetingClauses(string $profileId, array $query = []): array
    {
        return $this->request('GET', '/sd/targets', $profileId, $query);
    }

    /**
     * @param  array<string, mixed>  $query  mesmos filtros de listTargetingClauses()
     * @return list<array<string, mixed>>
     */
    public function listTargetingClausesEx(string $profileId, array $query = []): array
    {
        return $this->request('GET', '/sd/targets/extended', $profileId, $query);
    }

    /**
     * Cria targets. Body: lista de {adGroupId, state, expressionType, expression[], bid?}.
     *
     * @param  list<array<string, mixed>>  $targets
     * @return list<array<string, mixed>>
     */
    public function createTargetingClauses(string $profileId, array $targets): array
    {
        return $this->request('POST', '/sd/targets', $profileId, body: $targets);
    }

    /**
     * @param  list<array<string, mixed>>  $targets  cada um com targetId
     * @return list<array<string, mixed>>
     */
    public function updateTargetingClauses(string $profileId, array $targets): array
    {
        return $this->request('PUT', '/sd/targets', $profileId, body: $targets);
    }

    /**
     * Um target pelo id (operationId da Amazon é `getTargets`, mas é singular).
     *
     * @return array<string, mixed>
     */
    public function getTargets(string $profileId, string $targetId): array
    {
        return $this->request('GET', '/sd/targets/'.rawurlencode($targetId), $profileId);
    }

    /** @return array<string, mixed> */
    public function getTargetsEx(string $profileId, string $targetId): array
    {
        return $this->request('GET', '/sd/targets/extended/'.rawurlencode($targetId), $profileId);
    }

    /** @return array<string, mixed> */
    public function archiveTargetingClause(string $profileId, string $targetId): array
    {
        return $this->request('DELETE', '/sd/targets/'.rawurlencode($targetId), $profileId);
    }

    // ------------------------------------------------------------------ negative targets

    /**
     * Lista negative targets. Query: startIndex, count, stateFilter,
     * adGroupIdFilter, campaignIdFilter.
     *
     * @param  array<string, mixed>  $query
     * @return list<array<string, mixed>>
     */
    public function listNegativeTargetingClauses(string $profileId, array $query = []): array
    {
        return $this->request('GET', '/sd/negativeTargets', $profileId, $query);
    }

    /**
     * @param  array<string, mixed>  $query  filtros de listNegativeTargetingClauses() + targetIdFilter
     * @return list<array<string, mixed>>
     */
    public function listNegativeTargetingClausesEx(string $profileId, array $query = []): array
    {
        return $this->request('GET', '/sd/negativeTargets/extended', $profileId, $query);
    }

    /**
     * Body: lista de {adGroupId, state, expressionType, expression[]}.
     *
     * @param  list<array<string, mixed>>  $targets
     * @return list<array<string, mixed>>
     */
    public function createNegativeTargetingClauses(string $profileId, array $targets): array
    {
        return $this->request('POST', '/sd/negativeTargets', $profileId, body: $targets);
    }

    /**
     * @param  list<array<string, mixed>>  $targets  cada um com targetId
     * @return list<array<string, mixed>>
     */
    public function updateNegativeTargetingClauses(string $profileId, array $targets): array
    {
        return $this->request('PUT', '/sd/negativeTargets', $profileId, body: $targets);
    }

    /** @return array<string, mixed> */
    public function getNegativeTargets(string $profileId, string $negativeTargetId): array
    {
        return $this->request('GET', '/sd/negativeTargets/'.rawurlencode($negativeTargetId), $profileId);
    }

    /** @return array<string, mixed> */
    public function getNegativeTargetsEx(string $profileId, string $negativeTargetId): array
    {
        return $this->request('GET', '/sd/negativeTargets/extended/'.rawurlencode($negativeTargetId), $profileId);
    }

    /** @return array<string, mixed> */
    public function archiveNegativeTargetingClause(string $profileId, string $negativeTargetId): array
    {
        return $this->request('DELETE', '/sd/negativeTargets/'.rawurlencode($negativeTargetId), $profileId);
    }

    // ------------------------------------------------------------------ creatives

    /**
     * Lista creatives. Query: startIndex, count, adGroupIdFilter, creativeIdFilter.
     *
     * @param  array<string, mixed>  $query
     * @return list<array<string, mixed>>
     */
    public function listCreatives(string $profileId, array $query = []): array
    {
        return $this->request('GET', '/sd/creatives', $profileId, $query);
    }

    /**
     * Cria creatives. Body: lista de {adGroupId, creativeType, properties{…}, consentToTranslate?}.
     *
     * @param  list<array<string, mixed>>  $creatives
     * @return list<array<string, mixed>>
     */
    public function createCreatives(string $profileId, array $creatives): array
    {
        return $this->request('POST', '/sd/creatives', $profileId, body: $creatives);
    }

    /**
     * @param  list<array<string, mixed>>  $creatives  cada um com creativeId, creativeType, properties
     * @return list<array<string, mixed>>
     */
    public function updateCreatives(string $profileId, array $creatives): array
    {
        return $this->request('PUT', '/sd/creatives', $profileId, body: $creatives);
    }

    /**
     * Gera o HTML de preview de um creative. Body: {creative{…}, previewConfiguration|previewConfigurations}.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function postCreativePreview(string $profileId, array $body): array
    {
        return $this->request('POST', '/sd/creatives/preview', $profileId, body: $body);
    }

    /**
     * Status de moderação dos creatives. `language` é obrigatório (ex.: pt_BR).
     * Query: startIndex, count, adGroupIdFilter, creativeIdFilter.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listCreativeModerations(string $profileId, string $language, array $query = []): array
    {
        return $this->request('GET', '/sd/moderation/creatives', $profileId, ['language' => $language] + $query);
    }

    // ------------------------------------------------------------------ optimization rules

    /**
     * Lista optimization rules. Query: startIndex, count, stateFilter, name,
     * optimizationRuleIdFilter, adGroupIdFilter.
     *
     * @param  array<string, mixed>  $query
     * @return list<array<string, mixed>>
     */
    public function listOptimizationRules(string $profileId, array $query = []): array
    {
        return $this->request('GET', '/sd/optimizationRules', $profileId, $query);
    }

    /**
     * Body: lista de {ruleName, ruleType, ruleConditions[], ruleDuration?…}.
     *
     * @param  list<array<string, mixed>>  $rules
     * @return list<array<string, mixed>>
     */
    public function createOptimizationRules(string $profileId, array $rules): array
    {
        return $this->request('POST', '/sd/optimizationRules', $profileId, body: $rules);
    }

    /**
     * @param  list<array<string, mixed>>  $rules  cada um com optimizationRuleId
     * @return list<array<string, mixed>>
     */
    public function updateOptimizationRules(string $profileId, array $rules): array
    {
        return $this->request('PUT', '/sd/optimizationRules', $profileId, body: $rules);
    }

    /**
     * Uma optimization rule pelo id (sem operationId na spec; derivado do path).
     *
     * @return array<string, mixed>
     */
    public function getOptimizationRule(string $profileId, string $optimizationRuleId): array
    {
        return $this->request('GET', '/sd/optimizationRules/'.rawurlencode($optimizationRuleId), $profileId);
    }

    /**
     * Rules associadas a um ad group (sem operationId na spec; derivado do path).
     *
     * @return array<string, mixed>
     */
    public function listAdGroupOptimizationRules(string $profileId, string $adGroupId): array
    {
        return $this->request('GET', '/sd/adGroups/'.rawurlencode($adGroupId).'/optimizationRules', $profileId);
    }

    /**
     * @param  list<string>  $optimizationRuleIds
     * @return array<string, mixed>
     */
    public function associateOptimizationRulesWithAdGroup(string $profileId, string $adGroupId, array $optimizationRuleIds): array
    {
        return $this->request(
            'POST',
            '/sd/adGroups/'.rawurlencode($adGroupId).'/optimizationRules',
            $profileId,
            body: ['optimizationRuleIds' => $optimizationRuleIds],
        );
    }

    /**
     * @param  list<string>  $optimizationRuleIds
     * @return array<string, mixed>
     */
    public function disassociateOptimizationRulesFromAdGroup(string $profileId, string $adGroupId, array $optimizationRuleIds): array
    {
        return $this->request(
            'POST',
            '/sd/adGroups/'.rawurlencode($adGroupId).'/optimizationRules/disassociate',
            $profileId,
            body: ['optimizationRuleIds' => $optimizationRuleIds],
        );
    }

    // ------------------------------------------------------------------ locations

    /**
     * Lista location expressions. Query: startIndex, count, stateFilter,
     * adGroupIdFilter, campaignIdFilter.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listLocations(string $profileId, array $query = []): array
    {
        return $this->request('GET', '/sd/locations', $profileId, $query);
    }

    /**
     * Body: lista de {adGroupId, expression[{type, value}]}.
     *
     * @param  list<array<string, mixed>>  $locations
     * @return array<string, mixed>
     */
    public function createLocations(string $profileId, array $locations): array
    {
        return $this->request('POST', '/sd/locations', $profileId, body: $locations);
    }

    /**
     * Arquiva locations por filtro de ids (é um POST em /sd/locations/delete).
     *
     * @param  list<string>  $locationExpressionIds
     * @return array<string, mixed>
     */
    public function archiveLocations(string $profileId, array $locationExpressionIds): array
    {
        return $this->request('POST', '/sd/locations/delete', $profileId, body: [
            'locationExpressionIdFilter' => ['include' => $locationExpressionIds],
        ]);
    }

    // ------------------------------------------------------------------ brand safety

    /**
     * Domínios da deny list do anunciante. Query: startIndex, count.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listDomains(string $profileId, array $query = []): array
    {
        return $this->request('GET', '/sd/brandSafety/deny', $profileId, $query);
    }

    /**
     * Adiciona domínios à deny list (assíncrono → devolve requestId).
     *
     * @param  list<string>  $domains
     * @return array<string, mixed>
     */
    public function createBrandSafetyDenyListDomains(string $profileId, array $domains): array
    {
        return $this->request('POST', '/sd/brandSafety/deny', $profileId, body: ['domains' => $domains]);
    }

    /**
     * Apaga TODA a deny list (assíncrono → requestId).
     *
     * @return array<string, mixed>
     */
    public function deleteBrandSafetyDenyList(string $profileId): array
    {
        return $this->request('DELETE', '/sd/brandSafety/deny', $profileId);
    }

    /** @return array<string, mixed> */
    public function listRequestStatus(string $profileId): array
    {
        return $this->request('GET', '/sd/brandSafety/status', $profileId);
    }

    /** @return array<string, mixed> */
    public function getRequestStatus(string $profileId, string $requestId): array
    {
        return $this->request('GET', '/sd/brandSafety/'.rawurlencode($requestId).'/status', $profileId);
    }

    /**
     * @param  array<string, mixed>  $query  startIndex, count
     * @return array<string, mixed>
     */
    public function getRequestResults(string $profileId, string $requestId, array $query = []): array
    {
        return $this->request('GET', '/sd/brandSafety/'.rawurlencode($requestId).'/results', $profileId, $query);
    }

    // ------------------------------------------------------------------ budget rules

    /**
     * Budget rules do anunciante (pagina por nextToken; pageSize obrigatório).
     *
     * @return array<string, mixed>
     */
    public function getBudgetRulesForAdvertiser(string $profileId, int $pageSize, ?string $nextToken = null): array
    {
        $query = ['pageSize' => $pageSize];

        if ($nextToken !== null) {
            $query['nextToken'] = $nextToken;
        }

        return $this->request('GET', '/sd/budgetRules', $profileId, $query);
    }

    /**
     * Body: lista de budgetRulesDetails {name, ruleType, ruleDetails{…}, ruleState}.
     *
     * @param  list<array<string, mixed>>  $budgetRulesDetails
     * @return array<string, mixed>
     */
    public function createBudgetRulesForCampaigns(string $profileId, array $budgetRulesDetails): array
    {
        return $this->request('POST', '/sd/budgetRules', $profileId, body: ['budgetRulesDetails' => $budgetRulesDetails]);
    }

    /**
     * @param  list<array<string, mixed>>  $budgetRulesDetails  cada um com ruleId
     * @return array<string, mixed>
     */
    public function updateBudgetRulesForCampaigns(string $profileId, array $budgetRulesDetails): array
    {
        return $this->request('PUT', '/sd/budgetRules', $profileId, body: ['budgetRulesDetails' => $budgetRulesDetails]);
    }

    /** @return array<string, mixed> */
    public function getBudgetRuleByRuleId(string $profileId, string $budgetRuleId): array
    {
        return $this->request('GET', '/sd/budgetRules/'.rawurlencode($budgetRuleId), $profileId);
    }

    /**
     * Campanhas associadas a uma budget rule (pagina por nextToken).
     *
     * @return array<string, mixed>
     */
    public function getCampaignsAssociatedWithBudgetRule(string $profileId, string $budgetRuleId, int $pageSize, ?string $nextToken = null): array
    {
        $query = ['pageSize' => $pageSize];

        if ($nextToken !== null) {
            $query['nextToken'] = $nextToken;
        }

        return $this->request('GET', '/sd/budgetRules/'.rawurlencode($budgetRuleId).'/campaigns', $profileId, $query);
    }

    /** @return array<string, mixed> */
    public function listAssociatedBudgetRulesForCampaign(string $profileId, string $campaignId): array
    {
        return $this->request('GET', '/sd/campaigns/'.rawurlencode($campaignId).'/budgetRules', $profileId);
    }

    /**
     * @param  list<string>  $budgetRuleIds
     * @return array<string, mixed>
     */
    public function createAssociatedBudgetRulesForCampaign(string $profileId, string $campaignId, array $budgetRuleIds): array
    {
        return $this->request(
            'POST',
            '/sd/campaigns/'.rawurlencode($campaignId).'/budgetRules',
            $profileId,
            body: ['budgetRuleIds' => $budgetRuleIds],
        );
    }

    /** @return array<string, mixed> */
    public function disassociateAssociatedBudgetRuleForCampaign(string $profileId, string $campaignId, string $budgetRuleId): array
    {
        return $this->request(
            'DELETE',
            '/sd/campaigns/'.rawurlencode($campaignId).'/budgetRules/'.rawurlencode($budgetRuleId),
            $profileId,
        );
    }

    /**
     * Uso do orçamento por campanha (media type `sdcampaignbudgetusage.v1`; resposta 207).
     *
     * @param  list<string>  $campaignIds
     * @return array<string, mixed>
     */
    public function campaignsBudgetUsage(string $profileId, array $campaignIds): array
    {
        return $this->request(
            'POST',
            '/sd/campaigns/budget/usage',
            $profileId,
            body: ['campaignIds' => $campaignIds],
            contentType: 'application/vnd.sdcampaignbudgetusage.v1+json',
        );
    }

    // ------------------------------------------------------------------ recommendations

    /**
     * Recomendação de orçamento por campanha (media type `sdbudgetrecommendations.v3`).
     *
     * @param  list<string>  $campaignIds
     * @return array<string, mixed>
     */
    public function getBudgetRecommendations(string $profileId, array $campaignIds): array
    {
        return $this->request(
            'POST',
            '/sd/campaigns/budgetRecommendations',
            $profileId,
            body: ['campaignIds' => $campaignIds],
            contentType: 'application/vnd.sdbudgetrecommendations.v3+json',
        );
    }

    /**
     * Recomendação de targets (media type `sdtargetingrecommendations.v3.5`).
     * Body: {products[{asin}], tactic (T00020|T00030), typeFilter[]?}.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function getTargetRecommendations(string $profileId, array $body, ?string $locale = null): array
    {
        return $this->request(
            'POST',
            '/sd/targets/recommendations',
            $profileId,
            $locale !== null ? ['locale' => $locale] : [],
            $body,
            contentType: 'application/vnd.sdtargetingrecommendations.v3.5+json',
        );
    }

    /**
     * Recomendação de lance por target (media type `sdtargetingrecommendations.v3.3`; resposta 207).
     * Body: {products[{asin}], targetingClauses[{targetingClause{…}}]}.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function getTargetBidRecommendations(string $profileId, array $body): array
    {
        return $this->request(
            'POST',
            '/sd/targets/bid/recommendations',
            $profileId,
            body: $body,
            contentType: 'application/vnd.sdtargetingrecommendations.v3.3+json',
        );
    }

    /**
     * Headlines sugeridos pra creative (request `sdheadlinerecommendationrequest.v4.0`,
     * Accept `sdheadlinerecommendationresponse.v4.0`).
     * Body: {adFormat, asins[], maxNumRecommendations?}.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function getHeadlineRecommendations(string $profileId, array $body): array
    {
        return $this->request(
            'POST',
            '/sd/recommendations/creative/headline',
            $profileId,
            body: $body,
            contentType: 'application/vnd.sdheadlinerecommendationrequest.v4.0+json',
            accept: 'application/vnd.sdheadlinerecommendationresponse.v4.0+json',
        );
    }

    // ------------------------------------------------------------------ forecasts

    /**
     * Forecast de alcance/impressões pra uma configuração hipotética
     * (media type `sdforecasts.v3.1`). Body: {campaign, adGroup, productAds[],
     * targetingClauses[], negativeTargetingClauses[]?, locationExpressions[]?, optimizationRules[]?}.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createForecast(string $profileId, array $body): array
    {
        return $this->request(
            'POST',
            '/sd/forecasts',
            $profileId,
            body: $body,
            contentType: 'application/vnd.sdforecasts.v3.1+json',
        );
    }

    // ------------------------------------------------------------------ snapshots

    /**
     * Pede um snapshot de `campaigns|adGroups|productAds|targets|negativeTargets`.
     * Body: {stateFilter?, tacticFilter?}. Devolve snapshotId (assíncrono).
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createSnapshot(string $profileId, string $recordType, array $body = []): array
    {
        return $this->request('POST', '/sd/'.rawurlencode($recordType).'/snapshot', $profileId, body: $body);
    }

    /** @return array<string, mixed> status IN_PROGRESS|SUCCESS|FAILURE + location */
    public function getSnapshotById(string $profileId, string $snapshotId): array
    {
        return $this->request('GET', '/sd/snapshots/'.rawurlencode($snapshotId), $profileId);
    }

    /**
     * Conteúdo do snapshot pronto (JSON com a lista de registros).
     *
     * @return array<string, mixed>|list<mixed>
     */
    public function downloadSnapshotById(string $profileId, string $snapshotId): array
    {
        return $this->request('GET', '/sd/snapshots/'.rawurlencode($snapshotId).'/download', $profileId);
    }

    // ------------------------------------------------------------------ reports legado (v2)

    /**
     * Pede um relatório legado de `campaigns|adGroups|productAds|targets|asins`.
     * Body: {reportDate (YYYYMMDD), tactic, metrics (csv), segment?}.
     * Devolve reportId; acompanhe em getReportStatus() e baixe em downloadReport().
     *
     * @deprecated use o Reporting v3 (`Endpoints/Reporting/ReportingMethods`)
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function requestReport(string $profileId, string $recordType, array $body): array
    {
        return $this->request('POST', '/sd/'.rawurlencode($recordType).'/report', $profileId, body: $body);
    }

    /**
     * Status do relatório v2: {reportId, status IN_PROGRESS|SUCCESS|FAILURE, location?}.
     *
     * @deprecated use o Reporting v3
     *
     * @return array<string, mixed>
     */
    public function getReportStatus(string $profileId, string $reportId): array
    {
        return $this->request('GET', '/v2/reports/'.rawurlencode($reportId), $profileId);
    }

    /**
     * Baixa o relatório v2 (307 → S3, gzip JSON) e devolve as linhas.
     *
     * @deprecated use o Reporting v3
     *
     * @return list<array<string, mixed>>
     *
     * @throws AmazonAdsRequestException
     */
    public function downloadReport(string $profileId, string $reportId): array
    {
        $response = $this->http($profileId)
            ->withOptions(['decode_content' => false, 'allow_redirects' => true])
            ->get($this->baseUrl().'/v2/reports/'.rawurlencode($reportId).'/download');

        if (! $response->successful()) {
            throw new AmazonAdsRequestException("Amazon Ads GET /v2/reports/{$reportId}/download: ".$response->body(), $response->status());
        }

        $body = (string) $response->body();
        $json = @gzdecode($body);

        if ($json === false) {
            $json = $body;
        }

        $rows = json_decode($json, true);

        if (! is_array($rows)) {
            throw new AmazonAdsRequestException('relatório v2 baixado não é JSON válido');
        }

        return $rows;
    }
}

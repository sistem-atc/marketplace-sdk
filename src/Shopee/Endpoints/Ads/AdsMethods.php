<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Ads;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Módulo `/api/v2/ads` — publicidade CPC da Shopee.
 *
 * Usa o MESMO shop-token das rotas v2 que já consumimos (escrow, wallet,
 * pedidos): nenhuma permissão nova precisou ser habilitada no Open Platform.
 *
 * ⚠️ As datas aqui são **DD-MM-YYYY** (o resto da API Shopee usa unix timestamp).
 * `2026-08-05` devolve `error_param`.
 *
 * ⚠️ Erro vem com HTTP 200 e o campo `error` preenchido — o BaseMethods da
 * Shopee já trata isso (checa `error` no corpo, não só o status).
 */
class AdsMethods extends BaseMethods
{
    /**
     * Performance DIÁRIA agregada da LOJA inteira. Campo de gasto = `expense`.
     *
     * Limites da API: janela máxima de **1 mês** por chamada
     * (`ads.performance.error_date_range_too_long`) e retenção de **6 meses**
     * (`ads.performance.error_date_too_old`).
     *
     * @param  string  $startDate  DD-MM-YYYY
     * @param  string  $endDate    DD-MM-YYYY
     * @return list<array<string, mixed>>  1 item por dia: date, expense, impression,
     *   clicks, ctr, broad_gmv, broad_roas, direct_order, ...
     */
    public function getAllCpcAdsDailyPerformance(string $startDate, string $endDate): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/get_all_cpc_ads_daily_performance', [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Saldo da conta de ads — cross-check com a "Recarga de Ads" da wallet.
     *
     * @return float
     */
    public function getTotalBalance(): float
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/get_total_balance');

        return (float) ($response['response']['total_balance'] ?? 0.0);
    }

    // ---------------------------------------------------------------------
    // Loja / recomendações
    // ---------------------------------------------------------------------

    /**
     * Toggles de loja do Ads: auto_top_up (recarga automática) e campaign_surge.
     *
     * @return array<string, mixed>  data_timestamp, auto_top_up, campaign_surge
     */
    public function getShopToggleInfo(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/get_shop_toggle_info');

        return $response['response'] ?? [];
    }

    /**
     * Palavras-chave sugeridas para um item (opcionalmente refinadas por uma
     * palavra digitada). Cada sugestão traz quality_score, search_volume e bid
     * sugerido.
     *
     * @return array<string, mixed>  item_id, input_keyword, suggested_keywords[]
     */
    public function getRecommendedKeywordList(int $itemId, ?string $inputKeyword = null): array
    {
        $query = ['item_id' => $itemId];
        if ($inputKeyword !== null) {
            $query['input_keyword'] = $inputKeyword;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/get_recommended_keyword_list', $query);

        return $response['response'] ?? [];
    }

    /**
     * SKUs recomendados para anunciar (nível loja) com as tags top search /
     * best selling / best ROI e os tipos de anúncio já ativos.
     *
     * A lista vem direto em `response`.
     *
     * @return list<array<string, mixed>>
     */
    public function getRecommendedItemList(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/get_recommended_item_list');

        return $response['response'] ?? [];
    }

    /**
     * Taxa (%) do programa Ads Fácil para a loja + `update_at`. O path real é
     * `get_ads_facil_shop_rate` (sem acento, apesar do nome no catálogo).
     *
     * @return array<string, mixed>  rate, update_at
     */
    public function getAdsFacilShopRate(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/get_ads_facil_shop_rate');

        return $response['response'] ?? [];
    }

    // ---------------------------------------------------------------------
    // Performance
    // ---------------------------------------------------------------------

    /**
     * Performance HORÁRIA da loja inteira num único dia (DD-MM-YYYY).
     *
     * @return list<array<string, mixed>>  1 item por hora: hour, date, impression, clicks, expense…
     */
    public function getAllCpcAdsHourlyPerformance(string $performanceDate): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/get_all_cpc_ads_hourly_performance', [
            'performance_date' => $performanceDate,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Performance DIÁRIA por campanha de produto (até 100 campaign_id, csv).
     * Datas DD-MM-YYYY.
     *
     * @param  list<int>  $campaignIdList
     * @return array<string, mixed>  shop_id, region, campaign_list[]
     */
    public function getProductCampaignDailyPerformance(string $startDate, string $endDate, array $campaignIdList): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/get_product_campaign_daily_performance', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'campaign_id_list' => implode(',', $campaignIdList),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Performance HORÁRIA por campanha de produto num único dia (DD-MM-YYYY),
     * até 100 campaign_id (csv).
     *
     * @param  list<int>  $campaignIdList
     * @return array<string, mixed>  shop_id, region, campaign_list[]
     */
    public function getProductCampaignHourlyPerformance(string $performanceDate, array $campaignIdList): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/get_product_campaign_hourly_performance', [
            'performance_date' => $performanceDate,
            'campaign_id_list' => implode(',', $campaignIdList),
        ]);

        return $response['response'] ?? [];
    }

    // ---------------------------------------------------------------------
    // Campanhas de produto (Product Ads)
    // ---------------------------------------------------------------------

    /**
     * IDs de campanhas de produto da loja. `adType`: "", "all", "auto", "manual".
     * Paginação offset/limit; `has_next_page` no retorno.
     *
     * @return array<string, mixed>  shop_id, region, has_next_page, campaign_list[]
     */
    public function getProductLevelCampaignIdList(string $adType = 'all', int $offset = 0, int $limit = 100): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/get_product_level_campaign_id_list', [
            'ad_type' => $adType,
            'offset' => $offset,
            'limit' => $limit,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Configuração das campanhas de produto. `infoTypeList`: 1 Common,
     * 2 Manual Bidding, 3 Auto Bidding, 4 Auto Product Ads (csv). Até 100
     * campaign_id por chamada.
     *
     * @param  list<int>  $campaignIdList
     * @param  list<int>  $infoTypeList
     * @return array<string, mixed>  shop_id, region, campaign_list[]
     */
    public function getProductLevelCampaignSettingInfo(array $campaignIdList, array $infoTypeList = [1, 2, 3, 4]): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/get_product_level_campaign_setting_info', [
            'info_type_list' => implode(',', $infoTypeList),
            'campaign_id_list' => implode(',', $campaignIdList),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Cria Auto Product Ads. `referenceId` = string aleatória anti-duplicidade
     * (reusar depois de sucesso falha). Datas DD-MM-YYYY; sem `endDate` = sem fim.
     *
     * @deprecated A Shopee marcou como "coming offline soon" — migrar para
     *   `createManualProductAds`/GMS.
     */
    public function createAutoProductAds(string $referenceId, float $budget, string $startDate, ?string $endDate = null): int
    {
        $body = [
            'reference_id' => $referenceId,
            'budget' => $budget,
            'start_date' => $startDate,
        ];
        if ($endDate !== null) {
            $body['end_date'] = $endDate;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ads/create_auto_product_ads', [], $body);

        return (int) ($response['response']['campaign_id'] ?? 0);
    }

    /**
     * Edita Auto Product Ads. `editAction`: start, pause, resume, stop,
     * change_budget, change_duration. `$fields` = budget / start_date / end_date
     * conforme a ação.
     *
     * @deprecated A Shopee marcou como "coming offline soon".
     *
     * @param  array<string, mixed>  $fields
     */
    public function editAutoProductAds(string $referenceId, int $campaignId, string $editAction, array $fields = []): int
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ads/edit_auto_product_ads', [], array_merge($fields, [
            'reference_id' => $referenceId,
            'campaign_id' => $campaignId,
            'edit_action' => $editAction,
        ]));

        return (int) ($response['response']['campaign_id'] ?? 0);
    }

    /**
     * Cria Manual Selection Product Ads para UM item. Obrigatórios:
     * reference_id, budget, start_date (DD-MM-YYYY), bidding_method (auto|manual),
     * item_id. Em `$options`: end_date, roas_target, selected_keywords[]
     * ({keyword, match_type, bid_price_per_click} — obrigatório em manual),
     * discovery_ads_locations[] ({location, bid_price}), enhanced_cpc,
     * smart_creative_setting ("", default, on, off).
     *
     * @param  array<string, mixed>  $options
     */
    public function createManualProductAds(
        string $referenceId,
        float $budget,
        string $startDate,
        string $biddingMethod,
        int $itemId,
        array $options = [],
    ): int {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ads/create_manual_product_ads', [], array_merge($options, [
            'reference_id' => $referenceId,
            'budget' => $budget,
            'start_date' => $startDate,
            'bidding_method' => $biddingMethod,
            'item_id' => $itemId,
        ]));

        return (int) ($response['response']['campaign_id'] ?? 0);
    }

    /**
     * Edita as PALAVRAS-CHAVE de uma campanha manual. Cada item de
     * `$selectedKeywords`: edit_action (add, delete, restore, change_bid_price,
     * change_match_type), keyword, match_type?, bid_price_per_click?.
     *
     * @param  list<array<string, mixed>>  $selectedKeywords
     * @return array<string, mixed>  campaign_id, failed_edits[]
     */
    public function editManualProductAdKeywords(string $referenceId, int $campaignId, array $selectedKeywords): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ads/edit_manual_product_ad_keywords', [], [
            'reference_id' => $referenceId,
            'campaign_id' => $campaignId,
            'selected_keywords' => array_values($selectedKeywords),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Edita uma campanha manual. `editAction`: start, pause, resume, stop, delete,
     * change_budget, change_duration, change_smart_creative, change_location,
     * change_roas_target, change_enhanced_cpc. `$fields`: budget, start_date,
     * end_date, roas_target, discovery_ads_locations[] ({location, status, bid_price}),
     * enhanced_cpc, smart_creative_setting.
     *
     * @param  array<string, mixed>  $fields
     */
    public function editManualProductAds(string $referenceId, int $campaignId, string $editAction, array $fields = []): int
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ads/edit_manual_product_ads', [], array_merge($fields, [
            'reference_id' => $referenceId,
            'campaign_id' => $campaignId,
            'edit_action' => $editAction,
        ]));

        return (int) ($response['response']['campaign_id'] ?? 0);
    }

    /**
     * Sugestão de orçamento para criar um product ad. Obrigatórios:
     * product_selection (auto|manual), campaign_placement (search|discovery|all),
     * bidding_method (auto|manual). Em `$options`: enhanced_cpc ("true"/"false",
     * obrigatório em manual+manual), discovery_ads_location_names (csv),
     * roas_target, item_id (obrigatório em manual).
     *
     * @param  array<string, mixed>  $options
     */
    public function getCreateProductAdBudgetSuggestion(
        string $referenceId,
        string $productSelection,
        string $campaignPlacement,
        string $biddingMethod,
        array $options = [],
    ): float {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/get_create_product_ad_budget_suggestion', array_merge($options, [
            'reference_id' => $referenceId,
            'product_selection' => $productSelection,
            'campaign_placement' => $campaignPlacement,
            'bidding_method' => $biddingMethod,
        ]));

        return (float) ($response['response']['budget'] ?? 0.0);
    }

    /**
     * Faixa de ROAS alvo recomendada para um item (lower_bound / exact / upper_bound).
     *
     * @return array<string, mixed>
     */
    public function getProductRecommendedRoiTarget(string $referenceId, int $itemId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/get_product_recommended_roi_target', [
            'reference_id' => $referenceId,
            'item_id' => $itemId,
        ]);

        return $response['response'] ?? [];
    }

    // ---------------------------------------------------------------------
    // GMS (GMV Max Shop campaign)
    // ---------------------------------------------------------------------

    /**
     * A loja pode criar campanha GMS (GMV Max de loja)? Devolve is_eligible + reason.
     *
     * @return array<string, mixed>
     */
    public function checkCreateGmsProductCampaignEligibility(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ads/check_create_gms_product_campaign_eligibility');

        return $response['response'] ?? [];
    }

    /**
     * Cria a campanha GMS. Datas DD-MM-YYYY (start não pode ser passado).
     * `roasTarget` null = GMV Max Auto Bidding; 0 idem; > 0 = Custom ROAS.
     * Devolve campaign_id.
     */
    public function createGmsProductCampaign(
        string $startDate,
        float $dailyBudget,
        ?string $endDate = null,
        ?float $roasTarget = null,
        ?string $referenceId = null,
    ): int {
        $body = ['start_date' => $startDate, 'daily_budget' => $dailyBudget];
        foreach (['end_date' => $endDate, 'roas_target' => $roasTarget, 'reference_id' => $referenceId] as $k => $v) {
            if ($v !== null) {
                $body[$k] = $v;
            }
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ads/create_gms_product_campaign', [], $body);

        return (int) ($response['response']['campaign_id'] ?? 0);
    }

    /**
     * Edita a campanha GMS. `editAction`: change_budget (daily_budget),
     * change_duration (start_date/end_date), change_roas_target (roas_target),
     * pause, resume, stop… `$fields` carrega os campos da ação; `campaignId`
     * opcional (a loja só tem uma GMS).
     *
     * @param  array<string, mixed>  $fields
     */
    public function editGmsProductCampaign(string $editAction, array $fields = [], ?int $campaignId = null, ?string $referenceId = null): int
    {
        $body = array_merge($fields, ['edit_action' => $editAction]);
        if ($campaignId !== null) {
            $body['campaign_id'] = $campaignId;
        }
        if ($referenceId !== null) {
            $body['reference_id'] = $referenceId;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ads/edit_gms_product_campaign', [], $body);

        return (int) ($response['response']['campaign_id'] ?? 0);
    }

    /**
     * Itens que o vendedor REMOVEU da campanha GMS. POST com offset/limit (máx 100).
     *
     * @return array<string, mixed>  campaign_id, item_id_list[], total, has_next_page
     */
    public function listGmsUserDeletedItem(int $offset = 0, int $limit = 50): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ads/list_gms_user_deleted_item', [], [
            'offset' => $offset,
            'limit' => $limit,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Adiciona/remove itens da campanha GMS. `editAction`: add | remove;
     * 1..30 item_id por chamada.
     *
     * @param  list<int>  $itemIdList
     */
    public function editGmsItemProductCampaign(string $editAction, array $itemIdList, ?int $campaignId = null): int
    {
        $body = ['edit_action' => $editAction, 'item_id_list' => array_values($itemIdList)];
        if ($campaignId !== null) {
            $body['campaign_id'] = $campaignId;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ads/edit_gms_item_product_campaign', [], $body);

        return (int) ($response['response']['campaign_id'] ?? 0);
    }

    /**
     * Performance da campanha GMS por dia. Datas DD-MM-YYYY; janela máxima de
     * 3 meses; start mais antigo = 6 meses atrás. POST.
     *
     * @return array<string, mixed>  campaign_id, report[]
     */
    public function getGmsCampaignPerformance(string $startDate, string $endDate, ?int $campaignId = null): array
    {
        $body = ['start_date' => $startDate, 'end_date' => $endDate];
        if ($campaignId !== null) {
            $body['campaign_id'] = $campaignId;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ads/get_gms_campaign_performance', [], $body);

        return $response['response'] ?? [];
    }

    /**
     * Performance por ITEM da campanha GMS (só itens com performance, ordenado
     * por item_id). Mesmas regras de data de `getGmsCampaignPerformance`;
     * offset/limit (máx 100). POST.
     *
     * @return array<string, mixed>  campaign_id, result_list[], total, has_next_page
     */
    public function getGmsItemPerformance(string $startDate, string $endDate, int $offset = 0, int $limit = 50, ?int $campaignId = null): array
    {
        $body = ['start_date' => $startDate, 'end_date' => $endDate, 'offset' => $offset, 'limit' => $limit];
        if ($campaignId !== null) {
            $body['campaign_id'] = $campaignId;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ads/get_gms_item_performance', [], $body);

        return $response['response'] ?? [];
    }
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Ams;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Módulo `/api/v2/ams` — Affiliate Marketing Solution (programa de afiliados
 * da Shopee): Open Campaign (comissão aberta a todo afiliado), Targeted
 * Campaign (afiliados escolhidos), performance e relatórios de conversão /
 * validação (fatura mensal de comissão).
 *
 * Convenções que se repetem:
 *  - comissão `commission_rate`/`rate` é percentual com 2 casas: 1.1 = 1,1%;
 *  - `period_end_time` = 32503651199 (2999-12-31) significa "sem limite";
 *  - relatórios de performance usam `period_type` Day|Week|Month|Last7d|Last30d e
 *    datas YYYY-MM-DD que PRECISAM alinhar com o tipo (Week: domingo→sábado;
 *    Month: dia 1→último dia; Day: start=end, até 3 meses atrás);
 *  - listas GET vão em csv (item_id_list, campaign_id_list…), POST em JSON.
 *
 * A comissão AMS é DESCONTADA do repasse (deduction_method OrderEscrow /
 * SellerWallet…) — para conciliar o custo de afiliado com o payment/wallet use
 * `getConversionReport` (por pedido) e `getValidationReport` (por fatura mensal).
 */
class AmsMethods extends BaseMethods
{
    // ---------------------------------------------------------------------
    // Open Campaign
    // ---------------------------------------------------------------------

    /**
     * Produtos elegíveis que ainda NÃO estão na Open Campaign. Cursor: `''` na
     * primeira chamada. `sortBy`: sales (default) …; busca por ITEM_ID (csv, ≤50)
     * ou ITEM_NAME.
     *
     * @return array<string, mixed>  item_list[], total_count, cursor, has_more
     */
    public function getOpenCampaignNotAddedProduct(
        int $pageSize = 50,
        string $cursor = '',
        ?string $sortBy = null,
        ?string $searchType = null,
        ?string $searchContent = null,
    ): array {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_open_campaign_not_added_product', $this->compact([
            'page_size' => $pageSize,
            'cursor' => $cursor,
            'sort_by' => $sortBy,
            'search_type' => $searchType,
            'search_content' => $searchContent,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Produtos que JÁ estão na Open Campaign, com status, comissão e período.
     * Mesma paginação/busca de `getOpenCampaignNotAddedProduct`.
     *
     * @return array<string, mixed>  item_list[], total_count, cursor, has_more
     */
    public function getOpenCampaignAddedProduct(
        int $pageSize = 50,
        string $cursor = '',
        ?string $sortBy = null,
        ?string $searchType = null,
        ?string $searchContent = null,
    ): array {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_open_campaign_added_product', $this->compact([
            'page_size' => $pageSize,
            'cursor' => $cursor,
            'sort_by' => $sortBy,
            'search_type' => $searchType,
            'search_content' => $searchContent,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Produtos com sugestão de otimização. `rcmdReasonFilter`:
     * product_opportunities | optimize_increase_commission_rate |
     * optimize_extend_promotion_period.
     *
     * @return array<string, mixed>  item_list[], total, has_more
     */
    public function getOptimizationSuggestionProduct(string $rcmdReasonFilter, int $pageNo = 1, int $pageSize = 50): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_optimization_suggestion_product', [
            'page_no' => $pageNo,
            'page_size' => $pageSize,
            'rcmd_reason_filter' => $rcmdReasonFilter,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Comissão sugerida por item (até 20 item_id, csv).
     *
     * @param  list<int>  $itemIdList
     * @return list<array<string, mixed>>  rates[]
     */
    public function batchGetProductsSuggestedRate(array $itemIdList): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/batch_get_products_suggested_rate', [
            'item_id_list' => implode(',', $itemIdList),
        ]);

        return $response['response']['rates'] ?? [];
    }

    /**
     * Faixa de comissão sugerida para a loja inteira (min_rate / max_rate).
     *
     * @return array<string, mixed>
     */
    public function getShopSuggestedRate(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_shop_suggested_rate');

        return $response['response'] ?? [];
    }

    /**
     * Toggle "adicionar produto novo automaticamente" à Open Campaign + comissão default.
     *
     * @return array<string, mixed>  is_open, commission_rate
     */
    public function getAutoAddNewProductToggleStatus(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_auto_add_new_product_toggle_status');

        return $response['response'] ?? [];
    }

    /**
     * Liga/desliga o auto-add e (opcional) a comissão default dos novos produtos.
     *
     * @return array<string, mixed>
     */
    public function updateAutoAddNewProductSetting(bool $open, ?float $commissionRate = null): array
    {
        $body = ['open' => $open];
        if ($commissionRate !== null) {
            $body['commission_rate'] = $commissionRate;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ams/update_auto_add_new_product_setting', [], $body);

        return $response['response'] ?? [];
    }

    /**
     * Adiciona até 50 itens à Open Campaign com a mesma comissão. Sem período:
     * começa em 10 min e não termina.
     *
     * @param  list<int>  $itemIdList
     * @return array<string, mixed>  failed_list[], success_list[]
     */
    public function batchAddProductsToOpenCampaign(
        array $itemIdList,
        float $commissionRate,
        ?int $periodStartTime = null,
        ?int $periodEndTime = null,
    ): array {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ams/batch_add_products_to_open_campaign', [], $this->compact([
            'item_id_list' => array_values($itemIdList),
            'commission_rate' => $commissionRate,
            'period_start_time' => $periodStartTime,
            'period_end_time' => $periodEndTime,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Adiciona TODOS os produtos elegíveis à Open Campaign — tarefa assíncrona:
     * devolve task_id para `getOpenCampaignBatchTaskResult` (só o erro geral;
     * erro por produto exige o batch).
     *
     * @return array<string, mixed>  task_type, task_id
     */
    public function addAllProductsToOpenCampaign(float $commissionRate, ?int $periodStartTime = null, ?int $periodEndTime = null): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ams/add_all_products_to_open_campaign', [], $this->compact([
            'commission_rate' => $commissionRate,
            'period_start_time' => $periodStartTime,
            'period_end_time' => $periodEndTime,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Progresso de uma tarefa em massa (add_all / edit_all / remove_all).
     *
     * @return array<string, mixed>  status, progress_rate, fail_reason
     */
    public function getOpenCampaignBatchTaskResult(string $taskId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_open_campaign_batch_task_result', [
            'task_id' => $taskId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Edita comissão/período de até 50 campanhas (campaign_id = id do item na
     * Open Campaign). period_start só muda em status UPCOMING.
     *
     * @param  list<int>  $campaignIds
     * @return array<string, mixed>  failed_list[], success_list[]
     */
    public function batchEditProductsOpenCampaignSetting(
        array $campaignIds,
        ?float $commissionRate = null,
        ?int $periodStartTime = null,
        ?int $periodEndTime = null,
    ): array {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ams/batch_edit_products_open_campaign_setting', [], $this->compact([
            'campaign_ids' => array_values($campaignIds),
            'commission_rate' => $commissionRate,
            'period_start_time' => $periodStartTime,
            'period_end_time' => $periodEndTime,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Edita TODOS os produtos da Open Campaign (assíncrono → task_id).
     *
     * @return array<string, mixed>  task_type, task_id
     */
    public function editAllProductsOpenCampaignSetting(?float $commissionRate = null, ?int $periodStartTime = null, ?int $periodEndTime = null): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ams/edit_all_products_open_campaign_setting', [], $this->compact([
            'commission_rate' => $commissionRate,
            'period_start_time' => $periodStartTime,
            'period_end_time' => $periodEndTime,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Remove até 50 produtos da Open Campaign.
     *
     * @param  list<int>  $campaignIds
     * @return array<string, mixed>  failed_list[], success_list[]
     */
    public function batchRemoveProductsOpenCampaignSetting(array $campaignIds): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ams/batch_remove_products_open_campaign_setting', [], [
            'campaign_ids' => array_values($campaignIds),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Remove TODOS os produtos da Open Campaign (assíncrono → task_id).
     *
     * @return array<string, mixed>  task_type, task_id
     */
    public function removeAllProductsOpenCampaignSetting(): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ams/remove_all_products_open_campaign_setting', [], []);

        return $response['response'] ?? [];
    }

    // ---------------------------------------------------------------------
    // Targeted Campaign
    // ---------------------------------------------------------------------

    /**
     * Campanhas direcionadas do vendedor. Filtros em `$filters`: campaign_id_list
     * (csv ≤50), campaign_name, campaign_status (Upcoming|Ongoing|Ended|Cancelled|
     * Draft|Terminating|Terminated|Paused…), period_start_time, period_end_time,
     * item_id, item_name.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>  total_count, campaign_list[]
     */
    public function getTargetedCampaignList(int $pageNo = 1, int $pageSize = 50, array $filters = []): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_targeted_campaign_list', array_merge($filters, [
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Configuração completa de uma campanha direcionada: básico, budget,
     * item_list (com rate) e affiliate_list. Campanhas ShopeeManaged NÃO podem
     * ser consultadas aqui.
     *
     * @return array<string, mixed>
     */
    public function getTargetedCampaignSettings(int $campaignId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_targeted_campaign_settings', [
            'campaign_id' => $campaignId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Produtos que podem entrar numa campanha direcionada (cursor; busca por
     * ITEM_ID csv ≤50 ou ITEM_NAME).
     *
     * @return array<string, mixed>  item_list[], total_count, cursor
     */
    public function getTargetedCampaignAddableProductList(
        int $pageSize = 50,
        string $cursor = '',
        ?string $sortBy = null,
        ?string $searchType = null,
        ?string $searchContent = null,
    ): array {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_targeted_campaign_addable_product_list', $this->compact([
            'page_size' => $pageSize,
            'cursor' => $cursor,
            'sort_by' => $sortBy,
            'search_type' => $searchType,
            'search_content' => $searchContent,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Top 200 afiliados recomendados para campanha.
     *
     * @return array<string, mixed>  total_count, affiliate_list[]
     */
    public function getRecommendedAffiliateList(int $pageSize = 50): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_recommended_affiliate_list', [
            'page_size' => $pageSize,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Afiliados salvos na lista gerenciada (máx 2000; page_size ≤ 100).
     *
     * @return array<string, mixed>  total_count, affiliate_list[]
     */
    public function getManagedAffiliateList(int $pageNo = 1, int $pageSize = 20): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_managed_affiliate_list', [
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Cria campanha direcionada. `itemList`: [{item_id, rate}], `affiliateList`:
     * [{affiliate_id}]. Budget (is_set_budget/budget) só para lojas whitelisted
     * (TH não suporta).
     *
     * @param  list<array{item_id: int, rate: float}>  $itemList
     * @param  list<array{affiliate_id: int}>  $affiliateList
     * @return array<string, mixed>  campaign_id, fail_item_list[], fail_affiliate_list[]
     */
    public function createNewTargetedCampaign(
        string $campaignName,
        int $periodStartTime,
        int $periodEndTime,
        string $sellerMessage,
        array $itemList,
        array $affiliateList,
        ?bool $isSetBudget = null,
        ?float $budget = null,
    ): array {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ams/create_new_targeted_campaign', [], $this->compact([
            'campaign_name' => $campaignName,
            'period_start_time' => $periodStartTime,
            'period_end_time' => $periodEndTime,
            'seller_message' => $sellerMessage,
            'item_list' => array_values($itemList),
            'affiliate_list' => array_values($affiliateList),
            'is_set_budget' => $isSetBudget,
            'budget' => $budget,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Altera os produtos de uma campanha direcionada. `editType`: add | delete |
     * update; `itemList`: [{item_id, rate?}].
     *
     * @param  list<array<string, mixed>>  $itemList
     * @return array<string, mixed>  fail_item_list[]
     */
    public function editProductListOfTargetedCampaign(int $campaignId, string $editType, array $itemList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ams/edit_product_list_of_targeted_campaign', [], [
            'campaign_id' => $campaignId,
            'edit_type' => $editType,
            'item_list' => array_values($itemList),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Altera os afiliados de uma campanha direcionada. `editType`: add | delete;
     * `affiliateList`: [{affiliate_id}].
     *
     * @param  list<array<string, mixed>>  $affiliateList
     * @return array<string, mixed>  fail_affiliate_list[]
     */
    public function editAffiliateListOfTargetedCampaign(int $campaignId, string $editType, array $affiliateList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ams/edit_affiliate_list_of_targeted_campaign', [], [
            'campaign_id' => $campaignId,
            'edit_type' => $editType,
            'affiliate_list' => array_values($affiliateList),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Atualiza nome, período, mensagem e budget da campanha direcionada. Só os
     * campos informados vão no body.
     *
     * @param  array<string, mixed>  $fields  campaign_name, period_start_time, period_end_time, is_set_budget, budget
     * @return array<string, mixed>
     */
    public function updateBasicInfoOfTargetedCampaign(int $campaignId, array $fields): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ams/update_basic_info_of_targeted_campaign', [], array_merge(
            $this->compact($fields),
            ['campaign_id' => $campaignId],
        ));

        return $response['response'] ?? [];
    }

    /**
     * Encerra a campanha direcionada (status terminated — para toda promoção).
     *
     * @return array<string, mixed>
     */
    public function terminateTargetedCampaign(int $campaignId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/ams/terminate_targeted_campaign', [], [
            'campaign_id' => $campaignId,
        ]);

        return $response['response'] ?? [];
    }

    // ---------------------------------------------------------------------
    // Performance
    // ---------------------------------------------------------------------

    /**
     * Métricas gerais de afiliados (sales, orders, clicks, est_commission, roi,
     * buyers). `orderType`: PlacedOrder | ConfirmedOrder; `channel`: AllChannel |
     * SocialMedia | ShopeeVideo | LiveStreaming. Datas YYYY-MM-DD alinhadas ao
     * `periodType`.
     *
     * @return array<string, mixed>
     */
    public function getShopPerformance(
        string $periodType,
        string $startDate,
        string $endDate,
        string $orderType = 'PlacedOrder',
        string $channel = 'AllChannel',
    ): array {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_shop_performance', [
            'period_type' => $periodType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'order_type' => $orderType,
            'channel' => $channel,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Performance por PRODUTO via afiliados (paginado; `itemId` filtra um).
     *
     * @return array<string, mixed>  list[], total_count, has_more, fetched_date_range
     */
    public function getProductPerformance(
        string $periodType,
        string $startDate,
        string $endDate,
        int $pageNo = 1,
        int $pageSize = 50,
        string $orderType = 'PlacedOrder',
        string $channel = 'AllChannel',
        ?int $itemId = null,
    ): array {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_product_performance', $this->compact([
            'period_type' => $periodType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
            'order_type' => $orderType,
            'channel' => $channel,
            'item_id' => $itemId,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Performance por AFILIADO (paginado; `affiliateId` filtra um).
     *
     * @return array<string, mixed>  list[], total_count, has_more, fetched_date_range
     */
    public function getAffiliatePerformance(
        string $periodType,
        string $startDate,
        string $endDate,
        int $pageNo = 1,
        int $pageSize = 50,
        string $orderType = 'PlacedOrder',
        string $channel = 'AllChannel',
        ?int $affiliateId = null,
    ): array {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_affiliate_performance', $this->compact([
            'period_type' => $periodType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
            'order_type' => $orderType,
            'channel' => $channel,
            'affiliate_id' => $affiliateId,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Afiliados por id (queryType 1, csv ≤200) ou por nome fuzzy (queryType 2 —
     * devolve só id e nome).
     *
     * @param  list<int>  $affiliateIdList
     * @return array<string, mixed>  total_count, affiliate_list[]
     */
    public function queryAffiliateList(int $queryType, array $affiliateIdList = [], ?string $name = null): array
    {
        $query = ['query_type' => $queryType];
        if ($affiliateIdList !== []) {
            $query['affiliate_id_list'] = implode(',', $affiliateIdList);
        }
        if ($name !== null) {
            $query['name'] = $name;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/query_affiliate_list', $query);

        return $response['response'] ?? [];
    }

    /**
     * Performance por CONTEÚDO (vídeo/live). `channel`: ShopeeVideo |
     * LiveStreaming (AllChannel não vale aqui).
     *
     * @return array<string, mixed>  list[], total_count, has_more, fetched_date_range
     */
    public function getContentPerformance(
        string $periodType,
        string $startDate,
        string $endDate,
        string $channel,
        int $pageNo = 1,
        int $pageSize = 50,
        string $orderType = 'PlacedOrder',
        ?int $affiliateId = null,
        ?int $itemId = null,
    ): array {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_content_performance', $this->compact([
            'period_type' => $periodType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
            'order_type' => $orderType,
            'channel' => $channel,
            'affiliate_id' => $affiliateId,
            'item_id' => $itemId,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Métricas-chave de Open + Targeted campaigns no período.
     *
     * @return array<string, mixed>  open_campaign_key_metircs, targeted_campaign_key_metircs (sic), fetched_date_range
     */
    public function getCampaignKeyMetricsPerformance(string $periodType, string $startDate, string $endDate): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_campaign_key_metrics_performance', [
            'period_type' => $periodType,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Produtos da Open Campaign com performance no período (paginado).
     *
     * @return array<string, mixed>  list[], total_count, has_more, fetched_date_range
     */
    public function getOpenCampaignPerformance(
        string $periodType,
        string $startDate,
        string $endDate,
        int $pageNo = 1,
        int $pageSize = 50,
        ?int $itemId = null,
    ): array {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_open_campaign_performance', $this->compact([
            'period_type' => $periodType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
            'item_id' => $itemId,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Campanhas direcionadas com performance no período (paginado).
     *
     * @return array<string, mixed>  list[], total_count, has_more, fetched_date_range
     */
    public function getTargetedCampaignPerformance(
        string $periodType,
        string $startDate,
        string $endDate,
        int $pageNo = 1,
        int $pageSize = 50,
        ?int $campaignId = null,
    ): array {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_targeted_campaign_performance', $this->compact([
            'period_type' => $periodType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
            'campaign_id' => $campaignId,
        ]));

        return $response['response'] ?? [];
    }

    // ---------------------------------------------------------------------
    // Relatórios (conversão / validação) — base da conciliação de comissão
    // ---------------------------------------------------------------------

    /**
     * Relatório de CONVERSÃO: uma linha por pedido×item×afiliado×campanha, com
     * comissão, status de verificação e status/método de dedução (OrderEscrow,
     * SellerWallet…). É a ponte entre a comissão de afiliado e o repasse.
     *
     * Filtros em `$filters`: order_sn, affiliate_id, item_id, item_name,
     * l1/l2/l3_category_id, order_status (Unpaid|Pending|Completed|Cancelled),
     * verified_status (Unverified|Valid|Invalid), buyer_status (New|Existing),
     * attr_campaign_id, campaign_partner, seller_campaign_type (TargetCampaign|
     * OpenCampaign|MCNCampaign), deduction_status (PendingDeduction|Deducted),
     * deduction_method, e janelas place_order_time_*, order_completed_time_*,
     * conversion_completed_time_*, ams_deduction_time_* (unix; interseção).
     *
     * ⚠️ page_size ≤ 500 e page_no × page_size ≤ 10000 — para volumes maiores,
     * fatie por janela de tempo.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>  list[], total_count, has_more
     */
    public function getConversionReport(int $pageNo = 1, int $pageSize = 100, array $filters = []): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_conversion_report', array_merge($this->compact($filters), [
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Faturas mensais de validação AMS (validation bill) do vendedor — cada
     * item tem validation_id + mês, entrada para `getValidationReport`.
     *
     * @return list<array<string, mixed>>  validation_list[]
     */
    public function getValidationList(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_validation_list');

        return $response['response']['validation_list'] ?? [];
    }

    /**
     * Detalhe de uma fatura de validação: pedidos validados/invalidados e
     * comissão cobrada. `validationMonth` YYYYMM; `campaignSource`:
     * ShopeeManaged | Seller; janela obrigatória de place_order_time (unix).
     * Filtros opcionais: order_sn, l1/l2/l3_category_id, item_id, item_name,
     * verified_status (Valid|Invalid), attr_campaign_id.
     * page_size ≤ 500 e page_no × page_size ≤ 10000.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>  list[], total_count, has_more
     */
    public function getValidationReport(
        string $validationId,
        int $validationMonth,
        string $campaignSource,
        int $placeOrderTimeStart,
        int $placeOrderTimeEnd,
        int $pageNo = 1,
        int $pageSize = 100,
        array $filters = [],
    ): array {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_validation_report', array_merge($this->compact($filters), [
            'validation_id' => $validationId,
            'validation_month' => $validationMonth,
            'campaign_source' => $campaignSource,
            'place_order_time_start' => $placeOrderTimeStart,
            'place_order_time_end' => $placeOrderTimeEnd,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Data da última atualização do dashboard AMS (`markerType` = AmsMarker) —
     * consultar antes de puxar performance para não ler dia incompleto.
     *
     * @return array<string, mixed>  last_report_date
     */
    public function getPerformanceDataUpdateTime(string $markerType = 'AmsMarker'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/ams/get_performance_data_update_time', [
            'marker_type' => $markerType,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Remove chaves com valor null (parâmetros opcionais não informados).
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private function compact(array $params): array
    {
        return array_filter($params, static fn ($v) => $v !== null);
    }
}

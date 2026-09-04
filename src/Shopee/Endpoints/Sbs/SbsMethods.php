<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Sbs;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Módulo `/api/v2/sbs` — Supported by Shopee / Shopee Fulfilled (estoque no
 * armazém da Shopee): armazéns vinculados, inventário atual, validade, aging
 * (envelhecimento), movimentação e mapeamento de fulfillment (bundle/parent).
 *
 * ⚠️ `whs_region` é OBRIGATÓRIO nas telas de inventário/validade/aging/
 * movimentação: sem ele a Shopee responde "block by gateway due to invalid cid".
 * Só uma região por chamada (BR para nós). Listas (whs_ids, expiry_status) vão
 * como string separada por vírgula. Paginação page_no (1-based) + page_size.
 */
class SbsMethods extends BaseMethods
{
    /**
     * Armazéns Shopee vinculados à loja (whs_id, região, nome).
     *
     * @return list<array<string, mixed>>
     */
    public function getBoundWhsInfo(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/sbs/get_bound_whs_info');

        return $response['response']['list'] ?? [];
    }

    /**
     * "Current Inventory" do Seller Center: estoque por SKU no armazém Shopee
     * (sellable, reservado, inbound pendente, tags de baixo giro/excesso).
     *
     * Filtros opcionais em `$filters`: search_type (0 all, 1 nome, 2 sku_id,
     * 3 variação, 4 item_id) + keyword, whs_ids (csv), not_moving_tag,
     * inbound_pending_approval, products_with_inventory, category_id (L1),
     * stock_levels (1 low+no sellable, 2 low+to replenish, 3 low+replenished, 4 excess).
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>  item_list[]
     */
    public function getCurrentInventory(string $whsRegion = 'BR', int $pageNo = 1, int $pageSize = 50, array $filters = []): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/sbs/get_current_inventory', array_merge($filters, [
            'whs_region' => $whsRegion,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * "Expiry Report": lotes por status de validade. `expiry_status` (csv):
     * 0 expired, 2 expiring, 4 expiry_blocked, 5 damaged, 6 normal.
     * Outros filtros: whs_ids, category_id_l1, sku_id, item_id, variation, item_name.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>  item_list[]
     */
    public function getExpiryReport(string $whsRegion = 'BR', int $pageNo = 1, int $pageSize = 50, array $filters = []): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/sbs/get_expiry_report', array_merge($filters, [
            'whs_region' => $whsRegion,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * "Stock Aging": dias em estoque por SKU + tags de armazenagem prolongada /
     * excesso (base da taxa de armazenagem). Filtros: search_type (1 nome,
     * 2 sku_id, 3 variação, 4 item_id) + keyword, whs_ids, aging_storage_tag,
     * excess_storage_tag, category_id (L1).
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>  item_list[]
     */
    public function getStockAging(string $whsRegion = 'BR', int $pageNo = 1, int $pageSize = 50, array $filters = []): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/sbs/get_stock_aging', array_merge($filters, [
            'whs_region' => $whsRegion,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * "Stock Movement": entradas/saídas/ajustes por SKU no período.
     *
     * ⚠️ Datas em **YYYY-MM-DD**; janela máxima de **90 dias** e só o último
     * 1 ano é consultável. Filtros: whs_ids, category_id_l1, sku_id, item_id,
     * item_name, variation.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>  total, start_time, end_time, query_end_time, item_list[]
     */
    public function getStockMovement(
        string $startTime,
        string $endTime,
        string $whsRegion = 'BR',
        int $pageNo = 1,
        int $pageSize = 50,
        array $filters = [],
    ): array {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/sbs/get_stock_movement', array_merge($filters, [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'whs_region' => $whsRegion,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]));

        return $response['response'] ?? [];
    }

    /**
     * Fulfillment Mapping: para MTSKUs de bundle/parent devolve o mapeamento e o
     * inventário correspondente. Até 100 MTSKU IDs (csv); page_size 1..100
     * (default 100); paginação por `next_cursor`.
     *
     * @param  list<string|int>  $mtskuIds
     * @return array<string, mixed>  list[], total, next_cursor
     */
    public function getFulfillmentMappingInventoryList(array $mtskuIds = [], int $pageSize = 100, ?string $nextCursor = null): array
    {
        $query = ['page_size' => $pageSize];
        if ($mtskuIds !== []) {
            $query['mtsku_ids'] = implode(',', $mtskuIds);
        }
        if ($nextCursor !== null && $nextCursor !== '') {
            $query['next_cursor'] = $nextCursor;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/sbs/get_fulfillment_mapping_inventory_list', $query);

        return $response['response'] ?? [];
    }
}

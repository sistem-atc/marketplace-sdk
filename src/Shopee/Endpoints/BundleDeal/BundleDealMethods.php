<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\BundleDeal;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Modulo `/api/v2/bundle_deal` — "Leve N por X" (combo de itens).
 *
 * rule_type: 1 = FIX_PRICE (fix_price), 2 = DISCOUNT_PERCENTAGE
 * (discount_percentage), 3 = DISCOUNT_VALUE (discount_value). So o campo do
 * rule_type escolhido e' lido; `additional_tiers` (max 2) cria escalonamento.
 * Status de item: 1 = enable, 0 = disable (diferente do add_on_deal, que usa 2).
 * Restricoes: https://open.shopee.com/faq/254
 *
 * Todos os metodos devolvem o bloco `response` cru (array).
 */
class BundleDealMethods extends BaseMethods
{
    /**
     * Lista os bundle deals — /api/v2/bundle_deal/get_bundle_deal_list.
     * `timeStatus`: 1 all, 2 upcoming, 3 ongoing, 4 expired. page_size max 1000.
     *
     * @return array<string,mixed>
     */
    public function getBundleDealList(int $timeStatus = 1, int $pageNo = 1, int $pageSize = 20): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/bundle_deal/get_bundle_deal_list', [
            'time_status' => $timeStatus,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Detalhe de um bundle deal — /api/v2/bundle_deal/get_bundle_deal.
     *
     * @return array<string,mixed>
     */
    public function getBundleDeal(int $bundleDealId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/bundle_deal/get_bundle_deal', [
            'bundle_deal_id' => $bundleDealId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Itens de um bundle deal — /api/v2/bundle_deal/get_bundle_deal_item.
     *
     * @return array<string,mixed>
     */
    public function getBundleDealItem(int $bundleDealId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/bundle_deal/get_bundle_deal_item', [
            'bundle_deal_id' => $bundleDealId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Cria um bundle deal — /api/v2/bundle_deal/add_bundle_deal.
     * Obrigatorios: rule_type, min_amount, start_time (> agora), end_time
     * (>= start + 1h), name, purchase_limit, e o campo de valor do rule_type
     * (fix_price | discount_percentage | discount_value). Opcional:
     * additional_tiers[{min_amount, fix_price|discount_percentage|discount_value}].
     * Devolve `bundle_deal_id`.
     *
     * @param  array<string,mixed>  $payload
     * @return array<string,mixed>
     */
    public function addBundleDeal(array $payload): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/bundle_deal/add_bundle_deal', [], $payload);

        return $response['response'] ?? [];
    }

    /**
     * Atualiza um bundle deal — /api/v2/bundle_deal/update_bundle_deal.
     * `$fields`: mesmos campos do add (todos opcionais).
     *
     * @param  array<string,mixed>  $fields
     * @return array<string,mixed>
     */
    public function updateBundleDeal(int $bundleDealId, array $fields): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/bundle_deal/update_bundle_deal', [], array_merge(
            ['bundle_deal_id' => $bundleDealId],
            $fields,
        ));

        return $response['response'] ?? [];
    }

    /**
     * Apaga um bundle deal (so upcoming) — /api/v2/bundle_deal/delete_bundle_deal.
     *
     * @return array<string,mixed>
     */
    public function deleteBundleDeal(int $bundleDealId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/bundle_deal/delete_bundle_deal', [], [
            'bundle_deal_id' => $bundleDealId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Encerra um bundle deal em andamento — /api/v2/bundle_deal/end_bundle_deal.
     *
     * @return array<string,mixed>
     */
    public function endBundleDeal(int $bundleDealId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/bundle_deal/end_bundle_deal', [], [
            'bundle_deal_id' => $bundleDealId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Adiciona itens — /api/v2/bundle_deal/add_bundle_deal_item.
     * Cada item: {item_id, status (1 enable, 0 disable)}. Resposta traz
     * `failed_list` por item.
     *
     * @param  list<array<string,mixed>>  $itemList
     * @return array<string,mixed>
     */
    public function addBundleDealItem(int $bundleDealId, array $itemList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/bundle_deal/add_bundle_deal_item', [], [
            'bundle_deal_id' => $bundleDealId,
            'item_list' => $itemList,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Atualiza status dos itens — /api/v2/bundle_deal/update_bundle_deal_item.
     * Cada item: {item_id, status}.
     *
     * @param  list<array<string,mixed>>  $itemList
     * @return array<string,mixed>
     */
    public function updateBundleDealItem(int $bundleDealId, array $itemList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/bundle_deal/update_bundle_deal_item', [], [
            'bundle_deal_id' => $bundleDealId,
            'item_list' => $itemList,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Remove itens — /api/v2/bundle_deal/delete_bundle_deal_item.
     * Recebe item_ids; monta `item_list[{item_id}]`.
     *
     * @param  list<int>  $itemIds
     * @return array<string,mixed>
     */
    public function deleteBundleDealItem(int $bundleDealId, array $itemIds): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/bundle_deal/delete_bundle_deal_item', [], [
            'bundle_deal_id' => $bundleDealId,
            'item_list' => array_map(static fn (int $id) => ['item_id' => $id], array_values($itemIds)),
        ]);

        return $response['response'] ?? [];
    }
}

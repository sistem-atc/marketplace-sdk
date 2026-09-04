<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\AddOnDeal;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Modulo `/api/v2/add_on_deal` — "Compre junto" / brinde com gasto minimo.
 *
 * Um add-on deal tem itens PRINCIPAIS (main_item: o que o comprador precisa
 * levar) e itens SECUNDARIOS (sub_item: o que entra com desconto ou como
 * brinde). promotion_type: 0 = add-on discount, 1 = gift with min spend.
 * Status de item: 1 = enable, 2 = disable.
 *
 * Todos os metodos devolvem o bloco `response` cru (array). Erro de negocio
 * vem com HTTP 200 + `error` — o BaseMethods ja lanca ShopeeRequestException.
 */
class AddOnDealMethods extends BaseMethods
{
    /**
     * Lista os add-on deals — /api/v2/add_on_deal/get_add_on_deal_list.
     * `promotionStatus`: all/upcoming/ongoing/expired. Paginacao page_no
     * 1-based + flag `more`.
     *
     * @return array<string,mixed>
     */
    public function getAddOnDealList(string $promotionStatus = 'all', int $pageNo = 1, int $pageSize = 100): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/add_on_deal/get_add_on_deal_list', [
            'promotion_status' => $promotionStatus,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Detalhe de um add-on deal — /api/v2/add_on_deal/get_add_on_deal.
     *
     * @return array<string,mixed>
     */
    public function getAddOnDeal(int $addOnDealId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/add_on_deal/get_add_on_deal', [
            'add_on_deal_id' => $addOnDealId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Itens principais do deal — /api/v2/add_on_deal/get_add_on_deal_main_item.
     *
     * @return array<string,mixed>
     */
    public function getAddOnDealMainItem(int $addOnDealId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/add_on_deal/get_add_on_deal_main_item', [
            'add_on_deal_id' => $addOnDealId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Itens secundarios do deal — /api/v2/add_on_deal/get_add_on_deal_sub_item.
     *
     * @return array<string,mixed>
     */
    public function getAddOnDealSubItem(int $addOnDealId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/add_on_deal/get_add_on_deal_sub_item', [
            'add_on_deal_id' => $addOnDealId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Cria um add-on deal — /api/v2/add_on_deal/add_add_on_deal.
     * Obrigatorios: add_on_deal_name, start_time, end_time, promotion_type
     * (0 add-on discount, 1 gift with min spend). Opcionais:
     * purchase_min_spend, per_gift_num, promotion_purchase_limit.
     * Devolve `add_on_deal_id`.
     *
     * @param  array<string,mixed>  $payload
     * @return array<string,mixed>
     */
    public function addAddOnDeal(array $payload): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/add_on_deal/add_add_on_deal', [], $payload);

        return $response['response'] ?? [];
    }

    /**
     * Atualiza um add-on deal — /api/v2/add_on_deal/update_add_on_deal.
     * `$fields`: add_on_deal_name, start_time, end_time, purchase_min_spend,
     * per_gift_num, promotion_purchase_limit, sub_item_priority[].
     *
     * @param  array<string,mixed>  $fields
     * @return array<string,mixed>
     */
    public function updateAddOnDeal(int $addOnDealId, array $fields): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/add_on_deal/update_add_on_deal', [], array_merge(
            ['add_on_deal_id' => $addOnDealId],
            $fields,
        ));

        return $response['response'] ?? [];
    }

    /**
     * Apaga um add-on deal (so upcoming) — /api/v2/add_on_deal/delete_add_on_deal.
     *
     * @return array<string,mixed>
     */
    public function deleteAddOnDeal(int $addOnDealId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/add_on_deal/delete_add_on_deal', [], [
            'add_on_deal_id' => $addOnDealId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Encerra um add-on deal em andamento — /api/v2/add_on_deal/end_add_on_deal.
     *
     * @return array<string,mixed>
     */
    public function endAddOnDeal(int $addOnDealId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/add_on_deal/end_add_on_deal', [], [
            'add_on_deal_id' => $addOnDealId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Adiciona itens principais — /api/v2/add_on_deal/add_add_on_deal_main_item.
     * Cada item: {item_id, status (1 enable, 2 disable)}.
     *
     * @param  list<array<string,mixed>>  $mainItemList
     * @return array<string,mixed>
     */
    public function addAddOnDealMainItem(int $addOnDealId, array $mainItemList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/add_on_deal/add_add_on_deal_main_item', [], [
            'add_on_deal_id' => $addOnDealId,
            'main_item_list' => $mainItemList,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Atualiza status dos itens principais —
     * /api/v2/add_on_deal/update_add_on_deal_main_item. Cada item:
     * {item_id, status}.
     *
     * @param  list<array<string,mixed>>  $mainItemList
     * @return array<string,mixed>
     */
    public function updateAddOnDealMainItem(int $addOnDealId, array $mainItemList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/add_on_deal/update_add_on_deal_main_item', [], [
            'add_on_deal_id' => $addOnDealId,
            'main_item_list' => $mainItemList,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Remove itens principais — /api/v2/add_on_deal/delete_add_on_deal_main_item.
     * Recebe a lista de item_id (int[]), nao objetos.
     *
     * @param  list<int>  $mainItemIds
     * @return array<string,mixed>
     */
    public function deleteAddOnDealMainItem(int $addOnDealId, array $mainItemIds): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/add_on_deal/delete_add_on_deal_main_item', [], [
            'add_on_deal_id' => $addOnDealId,
            'main_item_list' => array_values($mainItemIds),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Adiciona itens secundarios — /api/v2/add_on_deal/add_add_on_deal_sub_item.
     * Cada item: {item_id, model_id, status, sub_item_input_price (preco
     * add-on sem imposto), sub_item_limit}.
     *
     * @param  list<array<string,mixed>>  $subItemList
     * @return array<string,mixed>
     */
    public function addAddOnDealSubItem(int $addOnDealId, array $subItemList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/add_on_deal/add_add_on_deal_sub_item', [], [
            'add_on_deal_id' => $addOnDealId,
            'sub_item_list' => $subItemList,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Atualiza itens secundarios — /api/v2/add_on_deal/update_add_on_deal_sub_item.
     * Mesmo formato do add. sub_item_limit so vale pro tipo add-on discount.
     *
     * @param  list<array<string,mixed>>  $subItemList
     * @return array<string,mixed>
     */
    public function updateAddOnDealSubItem(int $addOnDealId, array $subItemList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/add_on_deal/update_add_on_deal_sub_item', [], [
            'add_on_deal_id' => $addOnDealId,
            'sub_item_list' => $subItemList,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Remove itens secundarios — /api/v2/add_on_deal/delete_add_on_deal_sub_item.
     * Cada item: {item_id, model_id}.
     *
     * @param  list<array<string,mixed>>  $subItemList
     * @return array<string,mixed>
     */
    public function deleteAddOnDealSubItem(int $addOnDealId, array $subItemList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/add_on_deal/delete_add_on_deal_sub_item', [], [
            'add_on_deal_id' => $addOnDealId,
            'sub_item_list' => $subItemList,
        ]);

        return $response['response'] ?? [];
    }
}

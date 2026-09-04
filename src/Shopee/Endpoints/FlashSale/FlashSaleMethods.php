<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\FlashSale;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Modulo `/api/v2/shop_flash_sale` — Oferta Relampago da loja.
 *
 * Fluxo: getTimeSlotId (slots disponiveis) -> createShopFlashSale(timeslot_id)
 * -> addShopFlashSaleItems. Precos sao SEM imposto (`input_promo_price`).
 * Estoque de campanha (`stock`) min 1. Nao da pra apagar flash sale ongoing
 * ou expirada, nem editar em status `system_rejected`.
 *
 * Paginacao aqui e' offset/limit (offset max 1000, limit 1..100), nao page_no.
 * Todos os metodos devolvem o bloco `response` cru (array).
 */
class FlashSaleMethods extends BaseMethods
{
    /**
     * Slots de horario disponiveis — /api/v2/shop_flash_sale/get_time_slot_id.
     * start_time min = agora; end_time > start_time.
     *
     * @return array<string,mixed>
     */
    public function getTimeSlotId(int $startTime, int $endTime): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/shop_flash_sale/get_time_slot_id', [
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Criterios de elegibilidade de item (preco/estoque/desconto minimo por
     * categoria) — /api/v2/shop_flash_sale/get_item_criteria. Sem parametros.
     *
     * @return array<string,mixed>
     */
    public function getItemCriteria(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/shop_flash_sale/get_item_criteria');

        return $response['response'] ?? [];
    }

    /**
     * Lista flash sales da loja — /api/v2/shop_flash_sale/get_shop_flash_sale_list.
     * `type`: 0 all, 1 upcoming, 2 ongoing, 3 expired. start_time/end_time
     * andam juntos (start < end). offset 0..1000, limit 1..100.
     *
     * @return array<string,mixed>
     */
    public function getShopFlashSaleList(
        int $type = 0,
        int $offset = 0,
        int $limit = 100,
        ?int $startTime = null,
        ?int $endTime = null,
    ): array {
        $query = array_filter([
            'type' => $type,
            'offset' => $offset,
            'limit' => $limit,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ], static fn ($v) => $v !== null);

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/shop_flash_sale/get_shop_flash_sale_list', $query);

        return $response['response'] ?? [];
    }

    /**
     * Detalhe de uma flash sale — /api/v2/shop_flash_sale/get_shop_flash_sale.
     *
     * @return array<string,mixed>
     */
    public function getShopFlashSale(int $flashSaleId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/shop_flash_sale/get_shop_flash_sale', [
            'flash_sale_id' => $flashSaleId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Itens de uma flash sale (com detalhe de modelo/preco/estoque) —
     * /api/v2/shop_flash_sale/get_shop_flash_sale_items. offset 0..1000,
     * limit 1..100.
     *
     * @return array<string,mixed>
     */
    public function getShopFlashSaleItems(int $flashSaleId, int $offset = 0, int $limit = 100): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/shop_flash_sale/get_shop_flash_sale_items', [
            'flash_sale_id' => $flashSaleId,
            'offset' => $offset,
            'limit' => $limit,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Cria uma flash sale num slot — /api/v2/shop_flash_sale/create_shop_flash_sale.
     * So slots com start_time > agora. Devolve `flash_sale_id`.
     *
     * @return array<string,mixed>
     */
    public function createShopFlashSale(int $timeslotId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/shop_flash_sale/create_shop_flash_sale', [], [
            'timeslot_id' => $timeslotId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Habilita/desabilita uma flash sale — /api/v2/shop_flash_sale/update_shop_flash_sale.
     * `status`: 1 enable, 2 disable.
     *
     * @return array<string,mixed>
     */
    public function updateShopFlashSale(int $flashSaleId, int $status): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/shop_flash_sale/update_shop_flash_sale', [], [
            'flash_sale_id' => $flashSaleId,
            'status' => $status,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Apaga uma flash sale (so upcoming) — /api/v2/shop_flash_sale/delete_shop_flash_sale.
     *
     * @return array<string,mixed>
     */
    public function deleteShopFlashSale(int $flashSaleId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/shop_flash_sale/delete_shop_flash_sale', [], [
            'flash_sale_id' => $flashSaleId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Adiciona itens — /api/v2/shop_flash_sale/add_shop_flash_sale_items.
     * Cada item: {item_id, purchase_limit (0 = sem limite), e OU
     * item_input_promo_price + item_stock (sem variacao) OU
     * models[{model_id, input_promo_price, stock}] (com variacao)}.
     *
     * @param  list<array<string,mixed>>  $items
     * @return array<string,mixed>
     */
    public function addShopFlashSaleItems(int $flashSaleId, array $items): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/shop_flash_sale/add_shop_flash_sale_items', [], [
            'flash_sale_id' => $flashSaleId,
            'items' => $items,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Edita itens — /api/v2/shop_flash_sale/update_shop_flash_sale_items.
     * Cada item: {item_id, purchase_limit, item_status, item_input_promo_price,
     * item_stock, models[{model_id, status (0 disable, 1 enable),
     * input_promo_price, stock}]}. Preco/estoque so podem ser alterados com o
     * modelo desabilitado.
     *
     * @param  list<array<string,mixed>>  $items
     * @return array<string,mixed>
     */
    public function updateShopFlashSaleItems(int $flashSaleId, array $items): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/shop_flash_sale/update_shop_flash_sale_items', [], [
            'flash_sale_id' => $flashSaleId,
            'items' => $items,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Remove itens — /api/v2/shop_flash_sale/delete_shop_flash_sale_items.
     * Remover um item apaga todos os modelos dele.
     *
     * @param  list<int>  $itemIds
     * @return array<string,mixed>
     */
    public function deleteShopFlashSaleItems(int $flashSaleId, array $itemIds): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/shop_flash_sale/delete_shop_flash_sale_items', [], [
            'flash_sale_id' => $flashSaleId,
            'item_ids' => array_values($itemIds),
        ]);

        return $response['response'] ?? [];
    }
}

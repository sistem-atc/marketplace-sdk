<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\MerchantOrders;

use Generator;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders\MerchantOrderResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders\MerchantOrderSearchResponseDTO;

/**
 * Merchant Orders — a "ordem comercial" que agrupa os pagamentos de uma
 * compra (Checkout Pro/Preferences). E' o `order.id` que aparece dentro
 * de cada payment; um pedido pode ter N payments (tentativas recusadas +
 * a aprovada) todos apontando pra mesma merchant_order.
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference/merchant_orders/_merchant_orders_id/get
 */
class MerchantOrdersMethods extends BaseMethods
{
    /**
     * @param  array<string, mixed>  $payload  items, payer, external_reference, notification_url...
     */
    public function create(array $payload): MerchantOrderResponseDTO
    {
        return MerchantOrderResponseDTO::fromArray($this->makeRequest(HttpMethod::POST, '/merchant_orders', body: $payload));
    }

    public function get(int|string $merchantOrderId): MerchantOrderResponseDTO
    {
        return MerchantOrderResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, "/merchant_orders/{$merchantOrderId}"));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(int|string $merchantOrderId, array $payload): MerchantOrderResponseDTO
    {
        return MerchantOrderResponseDTO::fromArray($this->makeRequest(HttpMethod::PUT, "/merchant_orders/{$merchantOrderId}", body: $payload));
    }

    /**
     * Filtros: external_reference, preference_id, payer_id, status,
     * date_created_from/to, limit, offset.
     *
     * @param  array<string, mixed>  $filters
     */
    public function search(array $filters = []): MerchantOrderSearchResponseDTO
    {
        return MerchantOrderSearchResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/merchant_orders/search', $filters));
    }

    /**
     * Resposta do search vem em `elements` (nao `results`) — o paginador trata.
     *
     * @param  array<string, mixed>  $filters
     * @return Generator<int, MerchantOrderResponseDTO>
     */
    public function searchAll(array $filters = [], int $limit = 100): Generator
    {
        return $this->paginate('/merchant_orders/search', $filters, $limit, map: MerchantOrderResponseDTO::fromArray(...));
    }
}

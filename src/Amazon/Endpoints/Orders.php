<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Endpoint Orders v0 da SP-API (busca por AmazonOrderId individual).
 *
 * Rate limits Amazon:
 *   - GET /orders/{id}            : 2 req/s + burst 30
 *   - GET /orders/{id}/orderItems : 0.5 req/s + burst 30 (o gargalo)
 */
class Orders
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Header do pedido (GET /orders/v0/orders/{orderId}). Retorna `payload`
     * ou [] em 404.
     *
     * @return array<string, mixed>
     */
    public function getOrder(string $amazonOrderId): array
    {
        $resp = $this->client->get('/orders/v0/orders/'.rawurlencode($amazonOrderId));

        return data_get($resp, 'payload', []);
    }

    /**
     * Itens do pedido (GET /orders/v0/orders/{orderId}/orderItems). Retorna
     * `payload.OrderItems` ou [] em 404 / sem itens. NAO pagina NextToken
     * (pedidos BR raramente passam de 1 pagina).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getOrderItems(string $amazonOrderId): array
    {
        $resp = $this->client->get('/orders/v0/orders/'.rawurlencode($amazonOrderId).'/orderItems');

        return data_get($resp, 'payload.OrderItems', []);
    }
}

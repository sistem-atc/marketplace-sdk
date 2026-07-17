<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Order\OrderItem;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Order\OrderResponseDTO;

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
     * Header do pedido (GET /orders/v0/orders/{orderId}). Pedido inexistente
     * (404) vem com AmazonOrderId null.
     */
    public function getOrder(string $amazonOrderId): OrderResponseDTO
    {
        $resp = $this->client->get('/orders/v0/orders/'.rawurlencode($amazonOrderId));

        return OrderResponseDTO::fromArray((array) data_get($resp, 'payload', []));
    }

    /**
     * Itens do pedido (GET /orders/v0/orders/{orderId}/orderItems). Vazio em
     * 404 / sem itens. NAO pagina NextToken (pedidos BR raramente passam de 1
     * pagina).
     *
     * @return list<OrderItem>
     */
    public function getOrderItems(string $amazonOrderId): array
    {
        $resp = $this->client->get('/orders/v0/orders/'.rawurlencode($amazonOrderId).'/orderItems');

        return array_map(
            static fn (array $i): OrderItem => OrderItem::fromArray($i),
            data_get($resp, 'payload.OrderItems', []),
        );
    }
}

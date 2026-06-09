<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Fulfillment;

use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class FulfillmentMethods extends BaseMethods
{
    /**
     * Lista fulfillments de um pedido.
     */
    public function list(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/fulfillments");
    }

    /**
     * Cria um fulfillment para um pedido.
     * Nota: Nas versoes recentes da API, recomenda-se usar fulfillment_orders.
     */
    public function create(int|string $orderId, array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, "/orders/{$orderId}/fulfillments", [], ['fulfillment' => $data]);
    }

    /**
     * Recupera as Fulfillment Orders de um pedido (API >= 2022-07).
     */
    public function getFulfillmentOrders(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/fulfillment_orders");
    }

    /**
     * Cria um fulfillment a partir de uma fulfillment order.
     */
    public function createFromFulfillmentOrder(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, '/fulfillments', [], ['fulfillment' => $data]);
    }
}

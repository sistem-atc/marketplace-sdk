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

    /**
     * Recupera um fulfillment de um pedido.
     */
    public function get(int|string $orderId, int|string $fulfillmentId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/fulfillments/{$fulfillmentId}");
    }

    /**
     * Conta os fulfillments de um pedido (created_at_min/max, updated_at_min/max).
     *
     * @param  array<string, mixed>  $params
     */
    public function count(int|string $orderId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/fulfillments/count", $params);
    }

    /**
     * Lista os fulfillments de uma fulfillment order.
     *
     * @param  array<string, mixed>  $params
     */
    public function listForFulfillmentOrder(int|string $fulfillmentOrderId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/fulfillment_orders/{$fulfillmentOrderId}/fulfillments", $params);
    }

    /**
     * Cancela um fulfillment (POST /fulfillments/{id}/cancel).
     */
    public function cancel(int|string $fulfillmentId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/fulfillments/{$fulfillmentId}/cancel");
    }

    /**
     * Atualiza o rastreio de um fulfillment (POST /fulfillments/{id}/update_tracking).
     * Embrulha em `fulfillment` — ex.: ['notify_customer' => true,
     * 'tracking_info' => ['number' => ..., 'url' => ..., 'company' => ...]].
     *
     * @param  array<string, mixed>  $fulfillment
     */
    public function updateTracking(int|string $fulfillmentId, array $fulfillment): array
    {
        return $this->makeRequest(HttpMethod::POST, "/fulfillments/{$fulfillmentId}/update_tracking", [], ['fulfillment' => $fulfillment]);
    }

    /**
     * Lista os eventos (tracking) de um fulfillment.
     */
    public function listEvents(int|string $orderId, int|string $fulfillmentId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/fulfillments/{$fulfillmentId}/events");
    }

    /**
     * Recupera um evento de fulfillment.
     */
    public function getEvent(int|string $orderId, int|string $fulfillmentId, int|string $eventId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/fulfillments/{$fulfillmentId}/events/{$eventId}");
    }

    /**
     * Cria um evento de fulfillment. Embrulha em `event` — ex.: ['status' => 'in_transit'].
     *
     * @param  array<string, mixed>  $event
     */
    public function createEvent(int|string $orderId, int|string $fulfillmentId, array $event): array
    {
        return $this->makeRequest(HttpMethod::POST, "/orders/{$orderId}/fulfillments/{$fulfillmentId}/events", [], ['event' => $event]);
    }

    /**
     * Exclui um evento de fulfillment.
     */
    public function deleteEvent(int|string $orderId, int|string $fulfillmentId, int|string $eventId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/orders/{$orderId}/fulfillments/{$fulfillmentId}/events/{$eventId}");
    }
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Fulfillment;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Fulfillment Orders (`fulfillment_orders/{id}`) + Fulfillment Requests
 * (`fulfillment_orders/{id}/fulfillment_request`). API >= 2022-07.
 */
class FulfillmentOrderMethods extends BaseMethods
{
    /**
     * Recupera uma fulfillment order.
     */
    public function get(int|string $fulfillmentOrderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/fulfillment_orders/{$fulfillmentOrderId}");
    }

    /**
     * Lista as fulfillment orders de um pedido
     * (include_financial_summaries, include_order_reference_fields).
     *
     * @param  array<string, mixed>  $params
     */
    public function listForOrder(int|string $orderId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/fulfillment_orders", $params);
    }

    /**
     * Cancela uma fulfillment order.
     */
    public function cancel(int|string $fulfillmentOrderId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/fulfillment_orders/{$fulfillmentOrderId}/cancel");
    }

    /**
     * Marca uma fulfillment order em progresso como incompleta (fecha).
     */
    public function close(int|string $fulfillmentOrderId, ?string $message = null): array
    {
        $body = $message !== null ? ['fulfillment_order' => ['message' => $message]] : [];

        return $this->makeRequest(HttpMethod::POST, "/fulfillment_orders/{$fulfillmentOrderId}/close", [], $body);
    }

    /**
     * Aplica um hold. Embrulha em `fulfillment_hold` — ex.:
     * ['reason' => 'inventory_out_of_stock', 'reason_notes' => '...', 'notify_merchant' => true].
     *
     * @param  array<string, mixed>  $fulfillmentHold
     */
    public function hold(int|string $fulfillmentOrderId, array $fulfillmentHold): array
    {
        return $this->makeRequest(HttpMethod::POST, "/fulfillment_orders/{$fulfillmentOrderId}/hold", [], ['fulfillment_hold' => $fulfillmentHold]);
    }

    /**
     * Move a fulfillment order para outra location. `$extra` entra no mesmo
     * embrulho `fulfillment_order` (ex.: fulfillment_order_line_items).
     *
     * @param  array<string, mixed>  $extra
     */
    public function move(int|string $fulfillmentOrderId, int|string $newLocationId, array $extra = []): array
    {
        $body = ['fulfillment_order' => array_merge(['new_location_id' => $newLocationId], $extra)];

        return $this->makeRequest(HttpMethod::POST, "/fulfillment_orders/{$fulfillmentOrderId}/move", [], $body);
    }

    /**
     * Reabre uma fulfillment order (scheduled -> open).
     */
    public function open(int|string $fulfillmentOrderId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/fulfillment_orders/{$fulfillmentOrderId}/open");
    }

    /**
     * Libera o hold.
     */
    public function releaseHold(int|string $fulfillmentOrderId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/fulfillment_orders/{$fulfillmentOrderId}/release_hold");
    }

    /**
     * Reagenda o `fulfill_at` (ISO 8601). Embrulha em `fulfillment_order`.
     */
    public function reschedule(int|string $fulfillmentOrderId, string $newFulfillAt): array
    {
        return $this->makeRequest(HttpMethod::POST, "/fulfillment_orders/{$fulfillmentOrderId}/reschedule", [], [
            'fulfillment_order' => ['new_fulfill_at' => $newFulfillAt],
        ]);
    }

    /**
     * Define o deadline de varias fulfillment orders de uma vez.
     *
     * @param  array<int, int|string>  $fulfillmentOrderIds
     */
    public function setDeadline(array $fulfillmentOrderIds, string $fulfillmentDeadline): array
    {
        return $this->makeRequest(HttpMethod::POST, '/fulfillment_orders/set_fulfillment_orders_deadline', [], [
            'fulfillment_order_ids' => array_values($fulfillmentOrderIds),
            'fulfillment_deadline' => $fulfillmentDeadline,
        ]);
    }

    /**
     * Envia um fulfillment request pro servico de fulfillment. Embrulha em
     * `fulfillment_request` — ex.: ['message' => '...', 'fulfillment_order_line_items' => [...]].
     *
     * @param  array<string, mixed>  $fulfillmentRequest
     */
    public function sendRequest(int|string $fulfillmentOrderId, array $fulfillmentRequest = []): array
    {
        return $this->makeRequest(HttpMethod::POST, "/fulfillment_orders/{$fulfillmentOrderId}/fulfillment_request", [], [
            'fulfillment_request' => $fulfillmentRequest,
        ]);
    }

    /**
     * Aceita o fulfillment request (lado do servico de fulfillment).
     */
    public function acceptRequest(int|string $fulfillmentOrderId, ?string $message = null): array
    {
        return $this->makeRequest(HttpMethod::POST, "/fulfillment_orders/{$fulfillmentOrderId}/fulfillment_request/accept", [], [
            'fulfillment_request' => array_filter(['message' => $message], fn ($v) => $v !== null),
        ]);
    }

    /**
     * Rejeita o fulfillment request. `$extra` entra no embrulho
     * `fulfillment_request` (ex.: reason, line_items).
     *
     * @param  array<string, mixed>  $extra
     */
    public function rejectRequest(int|string $fulfillmentOrderId, ?string $message = null, array $extra = []): array
    {
        $request = array_merge(array_filter(['message' => $message], fn ($v) => $v !== null), $extra);

        return $this->makeRequest(HttpMethod::POST, "/fulfillment_orders/{$fulfillmentOrderId}/fulfillment_request/reject", [], [
            'fulfillment_request' => $request,
        ]);
    }
}

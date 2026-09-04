<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Fulfillment;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Lado do servico de fulfillment: fulfillment orders atribuidas ao app e
 * pedidos de cancelamento.
 *
 * Recursos REST: `assigned_fulfillment_order`, `cancellation_request`.
 */
class FulfillmentOrderRequestMethods extends BaseMethods
{
    /**
     * Lista as fulfillment orders atribuidas aos locais do app.
     *
     * @param  array<string, mixed>  $params  assignment_status (cancellation_requested|fulfillment_requested|fulfillment_accepted), location_ids[]
     */
    public function listAssigned(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/assigned_fulfillment_orders', $params);
    }

    /**
     * Envia um pedido de cancelamento ao servico de fulfillment. Embrulha em `cancellation_request`.
     *
     * @param  array<string, mixed>  $request  ex.: ['message' => '...']
     */
    public function createCancellationRequest(int|string $fulfillmentOrderId, array $request = []): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment_orders/{$fulfillmentOrderId}/cancellation_request",
            [],
            ['cancellation_request' => $request],
        );
    }

    /**
     * Aceita o pedido de cancelamento. Embrulha em `cancellation_request`.
     *
     * @param  array<string, mixed>  $request  ex.: ['message' => '...']
     */
    public function acceptCancellationRequest(int|string $fulfillmentOrderId, array $request = []): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment_orders/{$fulfillmentOrderId}/cancellation_request/accept",
            [],
            ['cancellation_request' => $request],
        );
    }

    /**
     * Rejeita o pedido de cancelamento. Embrulha em `cancellation_request`.
     *
     * @param  array<string, mixed>  $request  ex.: ['message' => '...']
     */
    public function rejectCancellationRequest(int|string $fulfillmentOrderId, array $request = []): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/fulfillment_orders/{$fulfillmentOrderId}/cancellation_request/reject",
            [],
            ['cancellation_request' => $request],
        );
    }
}

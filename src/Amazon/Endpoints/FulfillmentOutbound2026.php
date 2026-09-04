<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Fulfillment Outbound API v2026-07-04 (versão nova, path /fulfillment/outbound/2026-07-04).
 *
 * Respostas sem envelope `payload` (dado direto na raiz). Rate limit padrão do
 * modelo: 2 req/s, burst 30 (getOffers: 5 req/s, burst 30).
 *
 * O modelo aceita o header opcional `x-amzn-fulfillment-service-id`
 * (multi-serviço): use {@see withFulfillmentServiceId()} — devolve um CLONE
 * imutável que envia o header em todas as chamadas; sem ele, o serviço default
 * da Amazon é usado e o header não é enviado.
 * Versão anterior: {@see FulfillmentOutbound} (2020-07-01).
 */
class FulfillmentOutbound2026
{
    private const BASE = '/fulfillment/outbound/2026-07-04';

    private ?string $fulfillmentServiceId = null;

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Clone imutável que envia `x-amzn-fulfillment-service-id: $id` em todas
     * as chamadas. A instância original não é alterada.
     */
    public function withFulfillmentServiceId(string $id): static
    {
        $clone = clone $this;
        $clone->fulfillmentServiceId = $id;

        return $clone;
    }

    /** @return array<string, string> */
    private function headers(): array
    {
        return $this->fulfillmentServiceId !== null
            ? ['x-amzn-fulfillment-service-id' => $this->fulfillmentServiceId]
            : [];
    }

    /**
     * Preview de pedido (custos/prazos por opção de envio). POST /previews — 2 req/s, burst 30.
     *
     * @param  array<string, mixed>  $body  GetOrderPreviewRequest
     */
    public function getOrderPreview(array $body): array
    {
        return $this->client->post(self::BASE.'/previews', $body, $this->headers());
    }

    /**
     * Ofertas de entrega. POST /offers — 5 req/s, burst 30.
     *
     * @param  array<string, mixed>  $body  GetOffersRequest
     */
    public function getOffers(array $body): array
    {
        return $this->client->post(self::BASE.'/offers', $body, $this->headers());
    }

    /**
     * Cancela um pedido. PUT /orders/{orderId}/cancel — 2 req/s, burst 30.
     */
    public function cancelOrder(string $orderId): array
    {
        return $this->client->put(self::BASE.'/orders/'.rawurlencode($orderId).'/cancel', [], $this->headers());
    }

    /**
     * Atualiza status do pedido. PUT /orders/{orderId}/status (rate limit não documentado).
     *
     * @param  array<string, mixed>  $body  UpdateOrderStatusRequest
     */
    public function updateOrderStatus(string $orderId, array $body): array
    {
        return $this->client->put(self::BASE.'/orders/'.rawurlencode($orderId).'/status', $body, $this->headers());
    }

    /**
     * Atualiza um pacote do pedido. PUT /orders/{orderId}/packages/{packageId} (rate limit não documentado).
     *
     * @param  array<string, mixed>  $body  UpdatePackageRequest
     */
    public function updatePackage(string $orderId, string $packageId, array $body): array
    {
        return $this->client->put(
            self::BASE.'/orders/'.rawurlencode($orderId).'/packages/'.rawurlencode($packageId),
            $body,
            $this->headers(),
        );
    }

    /**
     * Atualiza um pedido. PUT /orders/{orderId} — 2 req/s, burst 30.
     *
     * @param  array<string, mixed>  $body  UpdateOrderRequest
     */
    public function updateOrder(string $orderId, array $body): array
    {
        return $this->client->put(self::BASE.'/orders/'.rawurlencode($orderId), $body, $this->headers());
    }

    /**
     * Detalhe do pedido. GET /orders/{orderId} — 2 req/s, burst 30.
     *
     * @param  array<string, mixed>  $query  shipments (bool: incluir shipments)
     */
    public function getOrder(string $orderId, array $query = []): array
    {
        return $this->client->get(self::BASE.'/orders/'.rawurlencode($orderId), $query, $this->headers());
    }

    /**
     * Lista pedidos. GET /orders — 2 req/s, burst 30. Paginação via `pagination.nextToken` → `$query['pageToken']`.
     *
     * @param  array<string, mixed>  $query  updatedAfter, pageToken, shipments
     */
    public function listOrders(array $query = []): array
    {
        return $this->client->get(self::BASE.'/orders', $query, $this->headers());
    }

    /**
     * Cria um pedido. POST /orders — 2 req/s, burst 30.
     *
     * @param  array<string, mixed>  $body  CreateOrderRequest
     */
    public function createOrder(array $body): array
    {
        return $this->client->post(self::BASE.'/orders', $body, $this->headers());
    }
}

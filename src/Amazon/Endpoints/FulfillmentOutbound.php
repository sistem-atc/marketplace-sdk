<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Fulfillment Outbound API v2020-07-01 (Multi-Channel Fulfillment / FBA outbound).
 *
 * Path base: /fba/outbound/2020-07-01. Todas as respostas vêm embrulhadas em
 * `payload` (devolvemos o array inteiro). Rate limit padrão do modelo: 2 req/s,
 * burst 30 (deliveryOffers: 5 req/s, burst 30).
 *
 * Versão nova do mesmo recurso: {@see FulfillmentOutbound2026} (2026-07-04).
 */
class FulfillmentOutbound
{
    private const BASE = '/fba/outbound/2020-07-01';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Preview de fulfillment (custos/prazos) pra um endereço + itens.
     * POST /fulfillmentOrders/preview — 2 req/s, burst 30. Dado em `payload.fulfillmentPreviews`.
     *
     * @param  array<string, mixed>  $body  GetFulfillmentPreviewRequest (address, items, shippingSpeedCategories...)
     */
    public function getFulfillmentPreview(array $body): array
    {
        return $this->client->post(self::BASE.'/fulfillmentOrders/preview', $body);
    }

    /**
     * Ofertas de entrega (janelas/prazos) pra um produto + destino.
     * POST /deliveryOffers — 5 req/s, burst 30. Dado em `payload.deliveryOffers`.
     *
     * @param  array<string, mixed>  $body  GetDeliveryOffersRequest (product, terms)
     */
    public function deliveryOffers(array $body): array
    {
        return $this->client->post(self::BASE.'/deliveryOffers', $body);
    }

    /**
     * Lista ordens de fulfillment. GET /fulfillmentOrders — 2 req/s, burst 30.
     * Paginação via `payload.nextToken` → `$query['nextToken']`.
     *
     * @param  array<string, mixed>  $query  queryStartDate (ISO 8601), nextToken
     */
    public function listAllFulfillmentOrders(array $query = []): array
    {
        return $this->client->get(self::BASE.'/fulfillmentOrders', $query);
    }

    /**
     * Cria uma ordem de fulfillment. POST /fulfillmentOrders — 2 req/s, burst 30.
     *
     * @param  array<string, mixed>  $body  CreateFulfillmentOrderRequest
     */
    public function createFulfillmentOrder(array $body): array
    {
        return $this->client->post(self::BASE.'/fulfillmentOrders', $body);
    }

    /**
     * Rastreio de um pacote. GET /tracking?packageNumber= — 2 req/s, burst 30.
     * Dado em `payload` (trackingNumber, currentStatus, trackingEvents...).
     */
    public function getPackageTrackingDetails(int $packageNumber): array
    {
        return $this->client->get(self::BASE.'/tracking', ['packageNumber' => $packageNumber]);
    }

    /**
     * Motivos de devolução válidos pra um SKU. GET /returnReasonCodes — 2 req/s, burst 30.
     * Dado em `payload.reasonCodeDetails`.
     *
     * @param  array<string, mixed>  $query  marketplaceId, sellerFulfillmentOrderId, language
     */
    public function listReturnReasonCodes(string $sellerSku, array $query = []): array
    {
        return $this->client->get(self::BASE.'/returnReasonCodes', array_merge(['sellerSku' => $sellerSku], $query));
    }

    /**
     * Cria devolução pra uma ordem. PUT /fulfillmentOrders/{id}/return — 2 req/s, burst 30.
     *
     * @param  array<string, mixed>  $body  CreateFulfillmentReturnRequest (items)
     */
    public function createFulfillmentReturn(string $sellerFulfillmentOrderId, array $body): array
    {
        return $this->client->put(self::BASE.'/fulfillmentOrders/'.rawurlencode($sellerFulfillmentOrderId).'/return', $body);
    }

    /**
     * Detalhe da ordem (header, itens, shipments, returns). GET /fulfillmentOrders/{id} — 2 req/s, burst 30.
     * Dado em `payload.fulfillmentOrder` / `payload.fulfillmentOrderItems` / `payload.fulfillmentShipments`.
     */
    public function getFulfillmentOrder(string $sellerFulfillmentOrderId): array
    {
        return $this->client->get(self::BASE.'/fulfillmentOrders/'.rawurlencode($sellerFulfillmentOrderId));
    }

    /**
     * Atualiza uma ordem. PUT /fulfillmentOrders/{id} — 2 req/s, burst 30.
     *
     * @param  array<string, mixed>  $body  UpdateFulfillmentOrderRequest
     */
    public function updateFulfillmentOrder(string $sellerFulfillmentOrderId, array $body): array
    {
        return $this->client->put(self::BASE.'/fulfillmentOrders/'.rawurlencode($sellerFulfillmentOrderId), $body);
    }

    /**
     * Cancela uma ordem. PUT /fulfillmentOrders/{id}/cancel — 2 req/s, burst 30.
     */
    public function cancelFulfillmentOrder(string $sellerFulfillmentOrderId): array
    {
        return $this->client->put(self::BASE.'/fulfillmentOrders/'.rawurlencode($sellerFulfillmentOrderId).'/cancel');
    }

    /**
     * Atualiza status de uma ordem (fluxo de seller-fulfilled). PUT /fulfillmentOrders/{id}/status.
     * Rate limit não documentado no modelo.
     *
     * @param  array<string, mixed>  $body  SubmitFulfillmentOrderStatusUpdateRequest
     */
    public function submitFulfillmentOrderStatusUpdate(string $sellerFulfillmentOrderId, array $body): array
    {
        return $this->client->put(self::BASE.'/fulfillmentOrders/'.rawurlencode($sellerFulfillmentOrderId).'/status', $body);
    }

    /**
     * Features de fulfillment disponíveis no marketplace. GET /features — 2 req/s, burst 30.
     * Dado em `payload.features`.
     */
    public function getFeatures(string $marketplaceId): array
    {
        return $this->client->get(self::BASE.'/features', ['marketplaceId' => $marketplaceId]);
    }

    /**
     * Inventário elegível a uma feature. GET /features/inventory/{featureName} — 2 req/s, burst 30.
     * Paginação via `payload.nextToken`.
     *
     * @param  array<string, mixed>  $query  nextToken, queryStartDate
     */
    public function getFeatureInventory(string $featureName, string $marketplaceId, array $query = []): array
    {
        return $this->client->get(
            self::BASE.'/features/inventory/'.rawurlencode($featureName),
            array_merge(['marketplaceId' => $marketplaceId], $query),
        );
    }

    /**
     * Elegibilidade de um SKU a uma feature. GET /features/inventory/{featureName}/{sellerSku} — 2 req/s, burst 30.
     * Dado em `payload` (isEligible, ineligibleReasons, skuInfo).
     */
    public function getFeatureSKU(string $featureName, string $sellerSku, string $marketplaceId): array
    {
        return $this->client->get(
            self::BASE.'/features/inventory/'.rawurlencode($featureName).'/'.rawurlencode($sellerSku),
            ['marketplaceId' => $marketplaceId],
        );
    }
}

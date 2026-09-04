<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Amazon Warehousing and Distribution (AWD) API v2024-05-09.
 *
 * Path base: /awd/2024-05-09. Respostas sem envelope `payload`. Paginação das
 * listas via `nextToken` na resposta → `$query['nextToken']`. Rate limits do
 * modelo: mutations 1 req/s (burst 1); leituras 2 req/s (burst 2) salvo indicado.
 */
class Awd
{
    private const BASE = '/awd/2024-05-09';

    public function __construct(
        private readonly Client $client,
    ) {}

    // ---- Inbound orders --------------------------------------------------

    /**
     * Cria ordem de inbound (rascunho). POST /inboundOrders — 1 req/s.
     *
     * @param  array<string, mixed>  $body  InboundOrderCreationData
     */
    public function createInbound(array $body): array
    {
        return $this->client->post(self::BASE.'/inboundOrders', $body);
    }

    /** Detalhe da ordem de inbound. GET /inboundOrders/{orderId} — 2 req/s. */
    public function getInbound(string $orderId): array
    {
        return $this->client->get(self::BASE.'/inboundOrders/'.rawurlencode($orderId));
    }

    /**
     * Atualiza ordem de inbound (só em rascunho). PUT /inboundOrders/{orderId} — 1 req/s.
     *
     * @param  array<string, mixed>  $body  InboundOrderCreationData
     */
    public function updateInbound(string $orderId, array $body): array
    {
        return $this->client->put(self::BASE.'/inboundOrders/'.rawurlencode($orderId), $body);
    }

    /** Cancela ordem de inbound. POST /inboundOrders/{orderId}/cancellation — 1 req/s. */
    public function cancelInbound(string $orderId): array
    {
        return $this->client->post(self::BASE.'/inboundOrders/'.rawurlencode($orderId).'/cancellation');
    }

    /** Confirma ordem de inbound (rascunho → confirmada). POST /inboundOrders/{orderId}/confirmation — 1 req/s. */
    public function confirmInbound(string $orderId): array
    {
        return $this->client->post(self::BASE.'/inboundOrders/'.rawurlencode($orderId).'/confirmation');
    }

    // ---- Inbound shipments -----------------------------------------------

    /**
     * Detalhe do shipment de inbound. GET /inboundShipments/{shipmentId} — 2 req/s.
     *
     * @param  array<string, mixed>  $query  skuQuantities (SHOW|HIDE)
     */
    public function getInboundShipment(string $shipmentId, array $query = []): array
    {
        return $this->client->get(self::BASE.'/inboundShipments/'.rawurlencode($shipmentId), $query);
    }

    /**
     * Etiquetas do shipment (PDF/ZPL base64 no retorno). GET /inboundShipments/{shipmentId}/labels — 1 req/s, burst 2.
     *
     * @param  array<string, mixed>  $query  pageType, formatType
     */
    public function getInboundShipmentLabels(string $shipmentId, array $query = []): array
    {
        return $this->client->get(self::BASE.'/inboundShipments/'.rawurlencode($shipmentId).'/labels', $query);
    }

    /** Tipos de página de etiqueta suportados. GET /inboundShipments/{shipmentId}/labelPageTypes — 1 req/s, burst 2. */
    public function getLabelPageTypes(string $shipmentId): array
    {
        return $this->client->get(self::BASE.'/inboundShipments/'.rawurlencode($shipmentId).'/labelPageTypes');
    }

    /**
     * Atualiza dados de transporte (tracking) do shipment. PUT /inboundShipments/{shipmentId}/transport — 1 req/s.
     *
     * @param  array<string, mixed>  $body  TransportationDetails
     */
    public function updateInboundShipmentTransportDetails(string $shipmentId, array $body): array
    {
        return $this->client->put(self::BASE.'/inboundShipments/'.rawurlencode($shipmentId).'/transport', $body);
    }

    /**
     * Checa elegibilidade de inbound dos SKUs/pacotes. POST /inboundEligibility — 1 req/s.
     *
     * @param  array<string, mixed>  $body  InboundEligibilityRequest
     */
    public function checkInboundEligibility(array $body): array
    {
        return $this->client->post(self::BASE.'/inboundEligibility', $body);
    }

    /**
     * Lista shipments de inbound. GET /inboundShipments — 1 req/s. Paginação `nextToken`.
     *
     * @param  array<string, mixed>  $query  sortBy, sortOrder, shipmentStatus, updatedAfter, updatedBefore, maxResults, nextToken
     */
    public function listInboundShipments(array $query = []): array
    {
        return $this->client->get(self::BASE.'/inboundShipments', $query);
    }

    // ---- Inventory --------------------------------------------------------

    /**
     * Inventário AWD por SKU. GET /inventory — 2 req/s. Paginação `nextToken`.
     *
     * @param  array<string, mixed>  $query  sku, sortOrder, details (SHOW|HIDE), nextToken, maxResults
     */
    public function listInventory(array $query = []): array
    {
        return $this->client->get(self::BASE.'/inventory', $query);
    }

    // ---- Outbound orders --------------------------------------------------

    /**
     * Lista ordens de outbound. GET /outboundOrders — 1 req/s. Paginação `nextToken`.
     *
     * @param  array<string, mixed>  $query  updatedAfter, updatedBefore, sortOrder, maxResults, nextToken
     */
    public function listOutbounds(array $query = []): array
    {
        return $this->client->get(self::BASE.'/outboundOrders', $query);
    }

    /**
     * Cria ordem de outbound (rascunho). POST /outboundOrders — 1 req/s.
     *
     * @param  array<string, mixed>  $body  OutboundOrderCreationData
     */
    public function createOutbound(array $body): array
    {
        return $this->client->post(self::BASE.'/outboundOrders', $body);
    }

    /** Detalhe da ordem de outbound. GET /outboundOrders/{orderId} — 1 req/s. */
    public function getOutbound(string $orderId): array
    {
        return $this->client->get(self::BASE.'/outboundOrders/'.rawurlencode($orderId));
    }

    /**
     * Atualiza ordem de outbound (só em rascunho). PUT /outboundOrders/{orderId} — 1 req/s.
     *
     * @param  array<string, mixed>  $body  OutboundOrderCreationData
     */
    public function updateOutbound(string $orderId, array $body): array
    {
        return $this->client->put(self::BASE.'/outboundOrders/'.rawurlencode($orderId), $body);
    }

    /** Confirma ordem de outbound. POST /outboundOrders/{orderId}/confirmation — 1 req/s. */
    public function confirmOutbound(string $orderId): array
    {
        return $this->client->post(self::BASE.'/outboundOrders/'.rawurlencode($orderId).'/confirmation');
    }

    // ---- Replenishment orders (AWD → FBA) ---------------------------------

    /**
     * Lista ordens de reabastecimento AWD→FBA. GET /replenishmentOrders. Paginação `nextToken`.
     * Rate limit não documentado no modelo.
     *
     * @param  array<string, mixed>  $query  updatedAfter, updatedBefore, sortOrder, maxResults, nextToken
     */
    public function listReplenishmentOrders(array $query = []): array
    {
        return $this->client->get(self::BASE.'/replenishmentOrders', $query);
    }

    /**
     * Cria ordem de reabastecimento AWD→FBA. POST /replenishmentOrders (rate limit não documentado).
     *
     * @param  array<string, mixed>  $body  ReplenishmentOrderCreationData
     */
    public function createReplenishmentOrder(array $body): array
    {
        return $this->client->post(self::BASE.'/replenishmentOrders', $body);
    }

    /** Detalhe da ordem de reabastecimento. GET /replenishmentOrders/{orderId} (rate limit não documentado). */
    public function getReplenishmentOrder(string $orderId): array
    {
        return $this->client->get(self::BASE.'/replenishmentOrders/'.rawurlencode($orderId));
    }

    /** Confirma ordem de reabastecimento. POST /replenishmentOrders/{orderId}/confirmation (rate limit não documentado). */
    public function confirmReplenishmentOrder(string $orderId): array
    {
        return $this->client->post(self::BASE.'/replenishmentOrders/'.rawurlencode($orderId).'/confirmation');
    }
}

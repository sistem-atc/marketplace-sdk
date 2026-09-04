<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Vendor Shipments API v1 — confirmações de embarque (ASN) e pedidos de
 * transporte do fornecedor pra Amazon.
 *
 * ⚠️ API de **Vendor Central** (conta de fornecedor 1P); NÃO se aplica a
 * conta de seller (3P).
 *
 * Path base: /vendor/shipping/v1. Respostas embrulhadas em `payload`. Rate
 * limit do modelo: 10 req/s, burst 10. Os operationIds do modelo vêm em
 * PascalCase (SubmitShipments…); aqui em camelCase.
 */
class VendorShipments
{
    private const BASE = '/vendor/shipping/v1';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Envia confirmações de embarque (ASN). POST /shipmentConfirmations.
     * Resposta: `payload.transactionId`.
     *
     * @param  array<string, mixed>  $body  SubmitShipmentConfirmationsRequest {shipmentConfirmations: [...]}
     * @return array<string, mixed>
     */
    public function submitShipmentConfirmations(array $body): array
    {
        return $this->client->post(self::BASE.'/shipmentConfirmations', $body);
    }

    /**
     * Envia pedidos de transporte (collect/WePay). POST /shipments.
     * Resposta: `payload.transactionId`.
     *
     * @param  array<string, mixed>  $body  SubmitShipments {shipments: [...]}
     * @return array<string, mixed>
     */
    public function submitShipments(array $body): array
    {
        return $this->client->post(self::BASE.'/shipments', $body);
    }

    /**
     * Detalhes de embarques. GET /shipments. Paginação `payload.pagination.nextToken`.
     * Dado em `payload.shipments`.
     *
     * @param  array<string, mixed>  $query  limit, sortOrder, nextToken, createdAfter/Before,
     *   shipmentConfirmedAfter/Before, packageLabelCreatedAfter/Before, shippedAfter/Before,
     *   estimatedDeliveryAfter/Before, shipmentDeliveryAfter/Before, requestedPickUpAfter/Before,
     *   scheduledPickUpAfter/Before, currentShipmentStatus, vendorShipmentIdentifier,
     *   buyerReferenceNumber, buyerWarehouseCode, sellerWarehouseCode
     * @return array<string, mixed>
     */
    public function getShipmentDetails(array $query = []): array
    {
        return $this->client->get(self::BASE.'/shipments', $query);
    }

    /**
     * Etiquetas de transporte. GET /transportLabels. Paginação `payload.pagination.nextToken`.
     * Dado em `payload.transportLabels`.
     *
     * @param  array<string, mixed>  $query  limit, sortOrder, nextToken, labelCreatedAfter/Before,
     *   buyerReferenceNumber, vendorShipmentIdentifier, sellerWarehouseCode
     * @return array<string, mixed>
     */
    public function getShipmentLabels(array $query = []): array
    {
        return $this->client->get(self::BASE.'/transportLabels', $query);
    }
}

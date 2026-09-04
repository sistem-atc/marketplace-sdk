<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Vendor Direct Fulfillment Shipping API v1 — etiquetas de envio, confirmações
 * de embarque, atualizações de status, invoices ao consumidor e packing slips
 * dos pedidos de Direct Fulfillment.
 *
 * ⚠️ API de **Vendor Central** (conta de fornecedor 1P, programa Direct
 * Fulfillment); NÃO se aplica a conta de seller (3P).
 *
 * Path base: /vendor/directFulfillment/shipping/v1. Respostas embrulhadas em
 * `payload`; listas paginam por `payload.pagination.nextToken`. Rate limit do
 * modelo: 10 req/s, burst 10.
 *
 * @deprecated Use VendorDirectFulfillmentShipping (2021-12-28).
 */
class VendorDirectFulfillmentShippingV1
{
    private const BASE = '/vendor/directFulfillment/shipping/v1';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Lista etiquetas de envio. GET /shippingLabels. createdAfter/createdBefore
     * obrigatórios (ISO 8601).
     *
     * @param  array<string, mixed>  $query  shipFromPartyId, limit, sortOrder, nextToken
     * @return array<string, mixed>
     */
    public function getShippingLabels(string $createdAfter, string $createdBefore, array $query = []): array
    {
        return $this->client->get(self::BASE.'/shippingLabels', [
            'createdAfter' => $createdAfter,
            'createdBefore' => $createdBefore,
        ] + $query);
    }

    /**
     * Solicita etiquetas de envio. POST /shippingLabels. Resposta: `payload.transactionId`.
     *
     * @param  array<string, mixed>  $body  SubmitShippingLabelsRequest {shippingLabelRequests: [...]}
     * @return array<string, mixed>
     */
    public function submitShippingLabelRequest(array $body): array
    {
        return $this->client->post(self::BASE.'/shippingLabels', $body);
    }

    /**
     * Etiqueta de uma PO. GET /shippingLabels/{purchaseOrderNumber}.
     *
     * @return array<string, mixed>
     */
    public function getShippingLabel(string $purchaseOrderNumber): array
    {
        return $this->client->get(self::BASE.'/shippingLabels/'.rawurlencode($purchaseOrderNumber));
    }

    /**
     * Confirmações de embarque. POST /shipmentConfirmations. Resposta: `payload.transactionId`.
     *
     * @param  array<string, mixed>  $body  SubmitShipmentConfirmationsRequest {shipmentConfirmations: [...]}
     * @return array<string, mixed>
     */
    public function submitShipmentConfirmations(array $body): array
    {
        return $this->client->post(self::BASE.'/shipmentConfirmations', $body);
    }

    /**
     * Atualizações de status de embarque. POST /shipmentStatusUpdates. Resposta: `payload.transactionId`.
     *
     * @param  array<string, mixed>  $body  SubmitShipmentStatusUpdatesRequest {shipmentStatusUpdates: [...]}
     * @return array<string, mixed>
     */
    public function submitShipmentStatusUpdates(array $body): array
    {
        return $this->client->post(self::BASE.'/shipmentStatusUpdates', $body);
    }

    /**
     * Lista invoices ao consumidor. GET /customerInvoices. createdAfter/createdBefore obrigatórios.
     *
     * @param  array<string, mixed>  $query  shipFromPartyId, limit, sortOrder, nextToken
     * @return array<string, mixed>
     */
    public function getCustomerInvoices(string $createdAfter, string $createdBefore, array $query = []): array
    {
        return $this->client->get(self::BASE.'/customerInvoices', [
            'createdAfter' => $createdAfter,
            'createdBefore' => $createdBefore,
        ] + $query);
    }

    /**
     * Invoice ao consumidor de uma PO. GET /customerInvoices/{purchaseOrderNumber}.
     *
     * @return array<string, mixed>
     */
    public function getCustomerInvoice(string $purchaseOrderNumber): array
    {
        return $this->client->get(self::BASE.'/customerInvoices/'.rawurlencode($purchaseOrderNumber));
    }

    /**
     * Lista packing slips. GET /packingSlips. createdAfter/createdBefore obrigatórios.
     *
     * @param  array<string, mixed>  $query  shipFromPartyId, limit, sortOrder, nextToken
     * @return array<string, mixed>
     */
    public function getPackingSlips(string $createdAfter, string $createdBefore, array $query = []): array
    {
        return $this->client->get(self::BASE.'/packingSlips', [
            'createdAfter' => $createdAfter,
            'createdBefore' => $createdBefore,
        ] + $query);
    }

    /**
     * Packing slip de uma PO. GET /packingSlips/{purchaseOrderNumber}.
     *
     * @return array<string, mixed>
     */
    public function getPackingSlip(string $purchaseOrderNumber): array
    {
        return $this->client->get(self::BASE.'/packingSlips/'.rawurlencode($purchaseOrderNumber));
    }
}

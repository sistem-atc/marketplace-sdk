<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Vendor Direct Fulfillment Shipping API 2021-12-28 — etiquetas de envio, confirmações
 * de embarque, atualizações de status, invoices ao consumidor e packing slips
 * dos pedidos de Direct Fulfillment.
 *
 * ⚠️ API de **Vendor Central** (conta de fornecedor 1P, programa Direct
 * Fulfillment); NÃO se aplica a conta de seller (3P).
 *
 * Path base: /vendor/directFulfillment/shipping/2021-12-28. Respostas SEM envelope
 * `payload` (dado e `pagination.nextToken` no root). Rate limit do
 * modelo: 10 req/s, burst 10.
 */
class VendorDirectFulfillmentShipping
{
    private const BASE = '/vendor/directFulfillment/shipping/2021-12-28';

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
     * Solicita etiquetas de envio. POST /shippingLabels. Resposta: `transactionId`.
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
     * Cria etiquetas de envio de uma PO (síncrono). POST /shippingLabels/{purchaseOrderNumber}.
     * Resposta: ShippingLabel (labelData) no root.
     *
     * @param  array<string, mixed>  $body  CreateShippingLabelsRequest {sellingParty, shipFromParty, containers}
     * @return array<string, mixed>
     */
    public function createShippingLabels(string $purchaseOrderNumber, array $body): array
    {
        return $this->client->post(self::BASE.'/shippingLabels/'.rawurlencode($purchaseOrderNumber), $body);
    }

    /**
     * Confirmações de embarque. POST /shipmentConfirmations. Resposta: `transactionId`.
     *
     * @param  array<string, mixed>  $body  SubmitShipmentConfirmationsRequest {shipmentConfirmations: [...]}
     * @return array<string, mixed>
     */
    public function submitShipmentConfirmations(array $body): array
    {
        return $this->client->post(self::BASE.'/shipmentConfirmations', $body);
    }

    /**
     * Atualizações de status de embarque. POST /shipmentStatusUpdates. Resposta: `transactionId`.
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

    /**
     * Cria etiqueta de contêiner (consolidação). POST /containerLabel.
     * Resposta: `containerLabel` no root.
     *
     * @param  array<string, mixed>  $body  CreateContainerLabelRequest {sellingParty, vendorContainerId, packages, ...}
     * @return array<string, mixed>
     */
    public function createContainerLabel(array $body): array
    {
        return $this->client->post(self::BASE.'/containerLabel', $body);
    }
}

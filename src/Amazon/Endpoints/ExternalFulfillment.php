<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * External Fulfillment API 2024-09-11 — 3 modelos num só endpoint:
 *   - Shipments (/externalFulfillment/2024-09-11/shipments...) — pedidos
 *     roteados pra fulfillment externo (MCF/3P): confirmar/rejeitar, pacotes,
 *     opções de envio, etiquetas e nota fiscal;
 *   - Returns (/externalFulfillment/2024-09-11/returns...) — devoluções;
 *   - Inventory (/externalFulfillment/inventory/2024-09-11/inventories) —
 *     atualização de estoque em lote.
 *
 * O modelo não publica rate limit. Respostas no nível raiz (sem `payload`).
 */
class ExternalFulfillment
{
    private const BASE = '/externalFulfillment/2024-09-11';

    public function __construct(
        private readonly Client $client,
    ) {}

    // ------------------------------------------------------------------ Shipments

    /**
     * Lista remessas por status (GET /shipments). Paginação por
     * `paginationToken`. Retorno em `shipments[]` + `paginationToken`.
     *
     * @param  string  $status  ACCEPTED, CONFIRMED, PACKAGE_CREATED, PICKUP_SLOT_RETRIEVED, INVOICE_GENERATED, SHIPLABEL_GENERATED, SHIPPED, DELIVERED, CANCELLED, REJECTED
     * @param  array<string, mixed>  $query  locationId, marketplaceId, channelName, lastUpdatedAfter, lastUpdatedBefore, maxResults, paginationToken
     */
    public function getShipments(string $status, array $query = []): array
    {
        return $this->client->get(self::BASE.'/shipments', array_merge(['status' => $status], $query));
    }

    /**
     * Detalhe de uma remessa (GET /shipments/{shipmentId}).
     */
    public function getShipment(string $shipmentId): array
    {
        return $this->client->get(self::BASE.'/shipments/'.rawurlencode($shipmentId));
    }

    /**
     * Confirma ou rejeita uma remessa (POST /shipments/{shipmentId}?operation=).
     *
     * @param  string  $operation  CONFIRM | REJECT
     * @param  array<string, mixed>  $body  ProcessShipmentRequest (lineItems com rejeição parcial, quando REJECT)
     */
    public function processShipment(string $shipmentId, string $operation, array $body = []): array
    {
        return $this->client->post(
            self::BASE.'/shipments/'.rawurlencode($shipmentId).'?'.http_build_query(['operation' => $operation]),
            $body,
        );
    }

    /**
     * Cria os pacotes de uma remessa (POST /shipments/{shipmentId}/packages).
     *
     * @param  array<string, mixed>  $body  CreatePackagesRequest (packages[])
     */
    public function createPackages(string $shipmentId, array $body): array
    {
        return $this->client->post(self::BASE.'/shipments/'.rawurlencode($shipmentId).'/packages', $body);
    }

    /**
     * Substitui um pacote (PUT /shipments/{shipmentId}/packages/{packageId}).
     *
     * @param  array<string, mixed>  $body  Package (dimensions, weight, packageLineItems, ...)
     */
    public function updatePackage(string $shipmentId, string $packageId, array $body): array
    {
        return $this->client->put(
            self::BASE.'/shipments/'.rawurlencode($shipmentId).'/packages/'.rawurlencode($packageId),
            $body,
        );
    }

    /**
     * Marca o pacote como enviado
     * (PATCH /shipments/{shipmentId}/packages/{packageId}?status=SHIPPED).
     *
     * @param  array<string, mixed>  $query  status (SHIPPED)
     * @param  array<string, mixed>  $body  UpdatePackageStatusRequest (trackingDetails, shipDate, ...)
     */
    public function updatePackageStatus(string $shipmentId, string $packageId, array $query = [], array $body = []): array
    {
        $path = self::BASE.'/shipments/'.rawurlencode($shipmentId).'/packages/'.rawurlencode($packageId);

        if ($query !== []) {
            $path .= '?'.http_build_query($query);
        }

        return $this->client->patch($path, $body);
    }

    /**
     * Opções de envio disponíveis pra um pacote
     * (GET /shipments/{shipmentId}/shippingOptions?packageId=). Retorno em
     * `shippingOptions[]`.
     */
    public function retrieveShippingOptions(string $shipmentId, string $packageId): array
    {
        return $this->client->get(
            self::BASE.'/shipments/'.rawurlencode($shipmentId).'/shippingOptions',
            ['packageId' => $packageId],
        );
    }

    /**
     * Solicita geração da nota fiscal da remessa (POST /shipments/{shipmentId}/invoice).
     */
    public function generateInvoice(string $shipmentId): array
    {
        return $this->client->post(self::BASE.'/shipments/'.rawurlencode($shipmentId).'/invoice');
    }

    /**
     * Obtém a nota fiscal gerada (GET /shipments/{shipmentId}/invoice).
     * Retorno = Document (format, content/url).
     */
    public function retrieveInvoice(string $shipmentId): array
    {
        return $this->client->get(self::BASE.'/shipments/'.rawurlencode($shipmentId).'/invoice');
    }

    /**
     * Gera/regenera etiquetas de envio
     * (PUT /shipments/{shipmentId}/shipLabels?operation=&shippingOptionId=).
     *
     * @param  string  $operation  GENERATE | REGENERATE
     * @param  array<string, mixed>  $query  shippingOptionId
     * @param  array<string, mixed>  $body  GenerateShipLabelsRequest (packageIds)
     */
    public function generateShipLabels(string $shipmentId, string $operation, array $query = [], array $body = []): array
    {
        return $this->client->put(
            self::BASE.'/shipments/'.rawurlencode($shipmentId).'/shipLabels?'.http_build_query(array_merge(['operation' => $operation], $query)),
            $body,
        );
    }

    // ------------------------------------------------------------------ Returns

    /**
     * Lista devoluções (GET /returns). Paginação por `nextToken`. Retorno em
     * `returns[]` + `nextToken`.
     *
     * @param  array<string, mixed>  $query  returnLocationId, rmaId, status, reverseTrackingId, createdSince, createdUntil, lastUpdatedSince, lastUpdatedUntil, lastUpdatedAfter, lastUpdatedBefore, maxResults, nextToken
     */
    public function listReturns(array $query = []): array
    {
        return $this->client->get(self::BASE.'/returns', $query);
    }

    /**
     * Detalhe de uma devolução (GET /returns/{returnId}).
     */
    public function getReturn(string $returnId): array
    {
        return $this->client->get(self::BASE.'/returns/'.rawurlencode($returnId));
    }

    // ------------------------------------------------------------------ Inventory

    /**
     * Atualiza estoque em lote
     * (POST /externalFulfillment/inventory/2024-09-11/inventories). Retorno em
     * `responses[]` (um por item, com status/erro).
     *
     * @param  array<string, mixed>  $body  BatchInventoryRequest (requests[] com locationId, skuId, quantity, ...)
     */
    public function batchInventory(array $body): array
    {
        return $this->client->post('/externalFulfillment/inventory/2024-09-11/inventories', $body);
    }
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Fulfillment Inbound API v0 da SP-API (`/fba/inbound/v0`).
 *
 * Amazon descontinuou a maior parte do v0 em favor da versao 2024-03-20
 * (ver {@see FulfillmentInbound}); sobraram no modelo apenas as operacoes
 * de consulta abaixo (shipments, itens, etiquetas, BOL e prep). Todas
 * embrulham o dado em `payload`.
 *
 * Rate limit (modelo): 2 req/s + burst 30 em todas as operacoes.
 *
 * @deprecated Use {@see FulfillmentInbound} (2024-03-20) para fluxos novos.
 */
class FulfillmentInboundV0
{
    private const BASE = '/fba/inbound/v0';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Instrucoes de preparo de itens (GET /fba/inbound/v0/prepInstructions).
     * Retorno em `payload.SKUPrepInstructionsList` / `payload.ASINPrepInstructionsList`.
     *
     * @param  array<string, mixed>  $query  SellerSKUList, ASINList (max 50 cada)
     */
    public function getPrepInstructions(string $shipToCountryCode, array $query = []): array
    {
        return $this->client->get(self::BASE.'/prepInstructions', ['ShipToCountryCode' => $shipToCountryCode] + $query);
    }

    /**
     * Etiquetas de caixa/palete do envio (GET /fba/inbound/v0/shipments/{shipmentId}/labels).
     * Retorno em `payload.DownloadURL`.
     *
     * @param  array<string, mixed>  $query  NumberOfPackages, PackageLabelsToPrint, NumberOfPallets, PageSize, PageStartIndex
     */
    public function getLabels(string $shipmentId, string $pageType, string $labelType, array $query = []): array
    {
        return $this->client->get(
            self::BASE.'/shipments/'.rawurlencode($shipmentId).'/labels',
            ['PageType' => $pageType, 'LabelType' => $labelType] + $query,
        );
    }

    /**
     * Bill of Lading do envio LTL (GET /fba/inbound/v0/shipments/{shipmentId}/billOfLading).
     * Retorno em `payload.DownloadURL`.
     */
    public function getBillOfLading(string $shipmentId): array
    {
        return $this->client->get(self::BASE.'/shipments/'.rawurlencode($shipmentId).'/billOfLading');
    }

    /**
     * Lista de envios inbound (GET /fba/inbound/v0/shipments). Paginacao por
     * `NextToken` (QueryType=NEXT_TOKEN). Retorno em `payload.ShipmentData`.
     *
     * @param  array<string, mixed>  $query  ShipmentStatusList, ShipmentIdList, LastUpdatedAfter, LastUpdatedBefore, NextToken
     */
    public function getShipments(string $queryType, string $marketplaceId, array $query = []): array
    {
        return $this->client->get(self::BASE.'/shipments', ['QueryType' => $queryType, 'MarketplaceId' => $marketplaceId] + $query);
    }

    /**
     * Itens de um envio (GET /fba/inbound/v0/shipments/{shipmentId}/items).
     * Retorno em `payload.ItemData`.
     *
     * @param  array<string, mixed>  $query  MarketplaceId
     */
    public function getShipmentItemsByShipmentId(string $shipmentId, array $query = []): array
    {
        return $this->client->get(self::BASE.'/shipments/'.rawurlencode($shipmentId).'/items', $query);
    }

    /**
     * Itens de envios por janela de atualizacao (GET /fba/inbound/v0/shipmentItems).
     * Paginacao por `NextToken`. Retorno em `payload.ItemData`.
     *
     * @param  array<string, mixed>  $query  LastUpdatedAfter, LastUpdatedBefore, NextToken
     */
    public function getShipmentItems(string $queryType, string $marketplaceId, array $query = []): array
    {
        return $this->client->get(self::BASE.'/shipmentItems', ['QueryType' => $queryType, 'MarketplaceId' => $marketplaceId] + $query);
    }
}

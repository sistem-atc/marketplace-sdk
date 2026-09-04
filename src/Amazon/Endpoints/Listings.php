<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Listings Items API (2021-08-01).
 * Permite criar, atualizar e excluir listagens.
 */
class Listings
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Recupera detalhes de uma listagem pelo SKU.
     */
    public function getListingsItem(string $sellerId, string $sku, array $query = []): array
    {
        $path = "/listings/2021-08-01/items/{$sellerId}/" . rawurlencode($sku);
        return $this->client->get($path, $query);
    }

    /**
     * Atualiza ou cria uma listagem (PUT).
     */
    public function putListingsItem(string $sellerId, string $sku, array $body): array
    {
        $path = "/listings/2021-08-01/items/{$sellerId}/" . rawurlencode($sku);
        return $this->client->put($path, $body);
    }

    /**
     * Remove uma listagem (DELETE).
     */
    public function deleteListingsItem(string $sellerId, string $sku): array
    {
        $path = "/listings/2021-08-01/items/{$sellerId}/" . rawurlencode($sku);
        return $this->client->delete($path);
    }

    /**
     * Atualiza parcialmente uma listagem (PATCH JSON-Patch em `patches[]`).
     * Path 2021-08-01. Rate limit 5 req/s, burst 5. `marketplaceIds` vai na
     * query (CSV) junto com `includedData`, `mode` (VALIDATION_PREVIEW) e
     * `issueLocale`. Resposta: `sku`, `status`, `submissionId`, `issues[]`.
     *
     * @param  array<int,string>|string  $marketplaceIds
     * @param  array<string,mixed>  $body  ListingsItemPatchRequest (productType, patches[])
     * @param  array<string,mixed>  $query  includedData, mode, issueLocale
     */
    public function patchListingsItem(string $sellerId, string $sku, array|string $marketplaceIds, array $body, array $query = []): array
    {
        $path = '/listings/2021-08-01/items/'.rawurlencode($sellerId).'/'.rawurlencode($sku)
            .'?'.http_build_query($this->withMarketplaceIds($marketplaceIds, $query));

        return $this->client->patch($path, $body);
    }

    /**
     * Busca listagens do seller (GET /listings/2021-08-01/items/{sellerId}).
     * Rate limit 5 req/s, burst 5. Paginacao por `pageToken` (resposta traz
     * `pagination.nextToken`); `items[]` com summaries/attributes/issues/offers
     * conforme `includedData`. Filtros: identifiers (+identifiersType),
     * variationParentSku, packageHierarchySku, createdAfter/Before,
     * lastUpdatedAfter/Before, withIssueSeverity, withStatus, withoutStatus,
     * sortBy, sortOrder, pageSize. Arrays sao serializados em CSV.
     *
     * @param  array<int,string>|string  $marketplaceIds
     * @param  array<string,mixed>  $query
     */
    public function searchListingsItems(string $sellerId, array|string $marketplaceIds, array $query = []): array
    {
        return $this->client->get(
            '/listings/2021-08-01/items/'.rawurlencode($sellerId),
            $this->withMarketplaceIds($marketplaceIds, $query),
        );
    }

    /**
     * Cria/substitui uma listagem na versao LEGADA 2020-09-01
     * (PUT /listings/2020-09-01/items/{sellerId}/{sku}). Rate limit 5 req/s,
     * burst 10. Prefira {@see putListingsItem} (2021-08-01).
     *
     * @deprecated Versao 2020-09-01 — use putListingsItem (2021-08-01).
     *
     * @param  array<int,string>|string  $marketplaceIds
     * @param  array<string,mixed>  $body  ListingsItemPutRequest (productType, requirements, attributes)
     * @param  array<string,mixed>  $query  issueLocale
     */
    public function putListingsItemLegacy(string $sellerId, string $sku, array|string $marketplaceIds, array $body, array $query = []): array
    {
        $path = '/listings/2020-09-01/items/'.rawurlencode($sellerId).'/'.rawurlencode($sku)
            .'?'.http_build_query($this->withMarketplaceIds($marketplaceIds, $query));

        return $this->client->put($path, $body);
    }

    /**
     * Atualiza parcialmente uma listagem na versao LEGADA 2020-09-01
     * (PATCH /listings/2020-09-01/items/{sellerId}/{sku}). Rate limit 5 req/s,
     * burst 10.
     *
     * @deprecated Versao 2020-09-01 — use patchListingsItem (2021-08-01).
     *
     * @param  array<int,string>|string  $marketplaceIds
     * @param  array<string,mixed>  $body  ListingsItemPatchRequest (productType, patches[])
     * @param  array<string,mixed>  $query  issueLocale
     */
    public function patchListingsItemLegacy(string $sellerId, string $sku, array|string $marketplaceIds, array $body, array $query = []): array
    {
        $path = '/listings/2020-09-01/items/'.rawurlencode($sellerId).'/'.rawurlencode($sku)
            .'?'.http_build_query($this->withMarketplaceIds($marketplaceIds, $query));

        return $this->client->patch($path, $body);
    }

    /**
     * Remove uma listagem na versao LEGADA 2020-09-01
     * (DELETE /listings/2020-09-01/items/{sellerId}/{sku}). Rate limit 5 req/s,
     * burst 10.
     *
     * @deprecated Versao 2020-09-01 — use deleteListingsItem (2021-08-01).
     *
     * @param  array<int,string>|string  $marketplaceIds
     * @param  array<string,mixed>  $query  issueLocale
     */
    public function deleteListingsItemLegacy(string $sellerId, string $sku, array|string $marketplaceIds, array $query = []): array
    {
        $path = '/listings/2020-09-01/items/'.rawurlencode($sellerId).'/'.rawurlencode($sku)
            .'?'.http_build_query($this->withMarketplaceIds($marketplaceIds, $query));

        return $this->client->delete($path);
    }

    /**
     * Normaliza `marketplaceIds` + arrays da query em CSV (formato exigido pela
     * SP-API; http_build_query geraria `k[0]=`).
     *
     * @param  array<int,string>|string  $marketplaceIds
     * @param  array<string,mixed>  $query
     * @return array<string,mixed>
     */
    private function withMarketplaceIds(array|string $marketplaceIds, array $query): array
    {
        $query = ['marketplaceIds' => $marketplaceIds] + $query;

        foreach ($query as $k => $v) {
            if (is_array($v)) {
                $query[$k] = implode(',', $v);
            }
        }

        return $query;
    }
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Catalog Items API (2022-04-01) — versao atual. Busca por identificadores
 * (ASIN/EAN/GTIN/SKU…) ou keywords e detalhe de ASIN com `includedData`
 * (summaries, attributes, dimensions, identifiers, images, productTypes,
 * relationships, salesRanks, vendorDetails…).
 */
class CatalogItems
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Busca no catalogo (GET /catalog/2022-04-01/items). Rate limit 2 req/s,
     * burst 2. Ou `identifiers` (+`identifiersType`, ate 20) ou `keywords`
     * (nunca os dois). `sellerId` obrigatorio quando identifiersType=SKU.
     * Paginacao por `pageToken` (resposta traz `pagination.nextToken`);
     * `pageSize` ate 20. Resposta: `numberOfResults`, `items[]`, `refinements`.
     * Arrays sao serializados em CSV.
     *
     * @param  array<int,string>|string  $marketplaceIds
     * @param  array<string,mixed>  $query  identifiers, identifiersType, keywords, includedData, locale, sellerId, brandNames, classificationIds, pageSize, pageToken, keywordsLocale
     */
    public function searchCatalogItems(array|string $marketplaceIds, array $query = []): array
    {
        return $this->client->get('/catalog/2022-04-01/items', $this->csvQuery(['marketplaceIds' => $marketplaceIds] + $query));
    }

    /**
     * Detalhe de um ASIN (GET /catalog/2022-04-01/items/{asin}). Rate limit
     * 2 req/s, burst 2. `includedData` default = summaries. Resposta: `asin`
     * + blocos pedidos.
     *
     * @param  array<int,string>|string  $marketplaceIds
     * @param  array<string,mixed>  $query  includedData, locale
     */
    public function getCatalogItem(string $asin, array|string $marketplaceIds, array $query = []): array
    {
        return $this->client->get(
            '/catalog/2022-04-01/items/'.rawurlencode($asin),
            $this->csvQuery(['marketplaceIds' => $marketplaceIds] + $query),
        );
    }

    /**
     * @param  array<string,mixed>  $query
     * @return array<string,mixed>
     */
    private function csvQuery(array $query): array
    {
        foreach ($query as $k => $v) {
            if (is_array($v)) {
                $query[$k] = implode(',', $v);
            }
        }

        return $query;
    }
}

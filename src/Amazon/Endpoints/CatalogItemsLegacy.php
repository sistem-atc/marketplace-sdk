<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Catalog Items API — versoes LEGADAS (2020-12-01 e v0). Mantidas so' pra
 * compatibilidade; prefira {@see CatalogItems} (2022-04-01).
 *
 * @deprecated Use CatalogItems (2022-04-01).
 */
class CatalogItemsLegacy
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Busca por keywords (GET /catalog/2020-12-01/items). Rate limit 2 req/s,
     * burst 2. `keywords` obrigatorio (CSV). Paginacao por `pageToken`
     * (`pagination.nextToken`). Resposta: `numberOfResults`, `items[]`.
     *
     * @deprecated Use CatalogItems::searchCatalogItems (2022-04-01).
     *
     * @param  array<int,string>|string  $keywords
     * @param  array<int,string>|string  $marketplaceIds
     * @param  array<string,mixed>  $query  includedData, brandNames, classificationIds, pageSize, pageToken, keywordsLocale, locale
     */
    public function searchCatalogItems(array|string $keywords, array|string $marketplaceIds, array $query = []): array
    {
        return $this->client->get('/catalog/2020-12-01/items', $this->csvQuery([
            'keywords' => $keywords,
            'marketplaceIds' => $marketplaceIds,
        ] + $query));
    }

    /**
     * Detalhe de um ASIN (GET /catalog/2020-12-01/items/{asin}). Rate limit
     * 2 req/s, burst 2.
     *
     * @deprecated Use CatalogItems::getCatalogItem (2022-04-01).
     *
     * @param  array<int,string>|string  $marketplaceIds
     * @param  array<string,mixed>  $query  includedData, locale
     */
    public function getCatalogItem(string $asin, array|string $marketplaceIds, array $query = []): array
    {
        return $this->client->get(
            '/catalog/2020-12-01/items/'.rawurlencode($asin),
            $this->csvQuery(['marketplaceIds' => $marketplaceIds] + $query),
        );
    }

    /**
     * Categorias (browse nodes) de um ASIN ou SKU (GET /catalog/v0/categories).
     * Rate limit 1 req/s, burst 2. Informe `ASIN` ou `SellerSKU` na query.
     * Resposta em `payload[]` (ProductCategoryId, ProductCategoryName, parent).
     *
     * @deprecated Endpoint v0 sem substituto direto; relationships/classifications vem de CatalogItems (2022-04-01).
     *
     * @param  array<string,mixed>  $query  ASIN, SellerSKU
     */
    public function listCatalogCategories(string $marketplaceId, array $query = []): array
    {
        return $this->client->get('/catalog/v0/categories', ['MarketplaceId' => $marketplaceId] + $query);
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

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Collection;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Colecoes: leitura generica, colecoes manuais (custom), vinculo
 * produto<->colecao (collect) e publicacao em canais (collection listing).
 *
 * Recursos REST: `collection`, `custom_collection`, `collect`, `collection_listing`.
 * Smart collections ficam fora deste lote (`smart_collection`).
 */
class CollectionMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    // ----------------------------------------------------------- collection

    /**
     * Recupera uma colecao (custom ou smart) pelo ID.
     *
     * @param  array<string, mixed>  $params  fields
     */
    public function get(int|string $collectionId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/collections/{$collectionId}", $params);
    }

    /**
     * Lista os produtos de uma colecao.
     *
     * @param  array<string, mixed>  $params  limit
     */
    public function products(int|string $collectionId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/collections/{$collectionId}/products", $params);
    }

    /**
     * Itera todos os produtos de uma colecao (paginacao por cursor).
     *
     * @return \Generator<int, array<string, mixed>>
     */
    public function eachProduct(int|string $collectionId, int $limit = 250): \Generator
    {
        return $this->eachPage("/collections/{$collectionId}/products.json", 'products', [], $limit);
    }

    // ---------------------------------------------------- custom_collection

    /**
     * Lista as colecoes manuais.
     *
     * @param  array<string, mixed>  $params  ids, since_id, title, product_id, handle, updated_at_min/max, published_status, limit, fields
     */
    public function listCustom(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/custom_collections', $params);
    }

    /**
     * Itera todas as colecoes manuais (paginacao por cursor).
     *
     * @param  array<string, mixed>  $params
     * @return \Generator<int, array<string, mixed>>
     */
    public function eachCustom(array $params = [], int $limit = 250): \Generator
    {
        return $this->eachPage('/custom_collections.json', 'custom_collections', $params, $limit);
    }

    /**
     * Total de colecoes manuais.
     *
     * @param  array<string, mixed>  $params
     */
    public function countCustom(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/custom_collections/count', $params);
    }

    /**
     * Recupera uma colecao manual.
     */
    public function getCustom(int|string $collectionId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/custom_collections/{$collectionId}", $params);
    }

    /**
     * Cria uma colecao manual. Embrulha em `custom_collection`.
     *
     * @param  array<string, mixed>  $collection
     */
    public function createCustom(array $collection): array
    {
        return $this->makeRequest(HttpMethod::POST, '/custom_collections', [], ['custom_collection' => $collection]);
    }

    /**
     * Atualiza uma colecao manual. Embrulha em `custom_collection`.
     *
     * @param  array<string, mixed>  $collection
     */
    public function updateCustom(int|string $collectionId, array $collection): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/custom_collections/{$collectionId}", [], ['custom_collection' => $collection]);
    }

    /**
     * Remove uma colecao manual.
     */
    public function deleteCustom(int|string $collectionId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/custom_collections/{$collectionId}");
    }

    // -------------------------------------------------------------- collect

    /**
     * Lista os collects (vinculos produto<->colecao manual).
     *
     * @param  array<string, mixed>  $params  product_id, collection_id, since_id, limit, fields
     */
    public function listCollects(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/collects', $params);
    }

    /**
     * Total de collects.
     *
     * @param  array<string, mixed>  $params  product_id, collection_id
     */
    public function countCollects(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/collects/count', $params);
    }

    /**
     * Recupera um collect.
     */
    public function getCollect(int|string $collectId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/collects/{$collectId}", $params);
    }

    /**
     * Adiciona um produto a uma colecao manual. Embrulha em `collect`.
     */
    public function createCollect(int|string $productId, int|string $collectionId, array $extra = []): array
    {
        $collect = array_merge(['product_id' => $productId, 'collection_id' => $collectionId], $extra);

        return $this->makeRequest(HttpMethod::POST, '/collects', [], ['collect' => $collect]);
    }

    /**
     * Remove um produto de uma colecao manual (apaga o collect).
     */
    public function deleteCollect(int|string $collectId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/collects/{$collectId}");
    }

    // --------------------------------------------------- collection_listing

    /**
     * Lista as colecoes publicadas no canal do app (Sales Channel SDK).
     *
     * @param  array<string, mixed>  $params  limit
     */
    public function listListings(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/collection_listings', $params);
    }

    /**
     * Recupera a publicacao de uma colecao no canal.
     */
    public function getListing(int|string $collectionId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/collection_listings/{$collectionId}");
    }

    /**
     * IDs dos produtos publicados via uma collection listing.
     *
     * @param  array<string, mixed>  $params  limit
     */
    public function listingProductIds(int|string $collectionId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/collection_listings/{$collectionId}/product_ids", $params);
    }

    /**
     * Publica (cria/atualiza) uma colecao no canal. Embrulha em `collection_listing`.
     *
     * @param  array<string, mixed>  $listing
     */
    public function putListing(int|string $collectionId, array $listing = []): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/collection_listings/{$collectionId}", [], ['collection_listing' => $listing]);
    }

    /**
     * Despublica a colecao do canal.
     */
    public function deleteListing(int|string $collectionId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/collection_listings/{$collectionId}");
    }
}

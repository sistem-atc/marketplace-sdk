<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Product;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Colecoes inteligentes / automaticas (Admin API REST — `smart_collection`).
 */
class SmartCollectionMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    /**
     * Lista colecoes inteligentes (1 pagina, max 250).
     *
     * @param  array<string, mixed>  $params  ex.: limit, ids, since_id, title, product_id, handle, published_status, fields
     */
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/smart_collections', $params);
    }

    /**
     * Itera TODAS as colecoes inteligentes (paginacao por cursor).
     *
     * @param  array<string, mixed>  $params
     * @return \Generator<int, array<string, mixed>>
     */
    public function each(array $params = [], int $limit = 250): \Generator
    {
        return $this->eachPage('/smart_collections', 'smart_collections', $params, $limit);
    }

    /**
     * Conta colecoes inteligentes.
     *
     * @param  array<string, mixed>  $params  ex.: title, product_id, published_status
     */
    public function count(array $params = []): int
    {
        $response = $this->makeRequest(HttpMethod::GET, '/smart_collections/count', $params);

        return $response['count'] ?? 0;
    }

    /**
     * Recupera uma colecao inteligente.
     *
     * @param  array<string, mixed>  $params  ex.: fields
     */
    public function get(int|string $collectionId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/smart_collections/{$collectionId}", $params);
    }

    /**
     * Cria uma colecao inteligente (title + rules obrigatorios).
     *
     * @param  array<string, mixed>  $collection
     */
    public function create(array $collection): array
    {
        return $this->makeRequest(HttpMethod::POST, '/smart_collections', [], ['smart_collection' => $collection]);
    }

    /**
     * Atualiza uma colecao inteligente.
     *
     * @param  array<string, mixed>  $collection
     */
    public function update(int|string $collectionId, array $collection): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/smart_collections/{$collectionId}", [], ['smart_collection' => $collection]);
    }

    /**
     * Remove uma colecao inteligente.
     */
    public function delete(int|string $collectionId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/smart_collections/{$collectionId}");
    }

    /**
     * Reordena os produtos da colecao (PUT .../order). `$productIds` vira
     * query `products[]=...` (ordem manual); `$sortOrder` troca o criterio
     * (ex.: manual, alpha-asc, best-selling, created-desc).
     *
     * @param  array<int, int|string>  $productIds
     */
    public function order(int|string $collectionId, array $productIds = [], ?string $sortOrder = null): array
    {
        $query = [];
        if ($productIds !== []) {
            $query['products'] = array_values($productIds);
        }
        if ($sortOrder !== null) {
            $query['sort_order'] = $sortOrder;
        }

        return $this->makeRequest(HttpMethod::PUT, "/smart_collections/{$collectionId}/order", $query);
    }
}

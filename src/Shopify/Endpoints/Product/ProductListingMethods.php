<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Product;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Publicacao de produtos no canal de vendas do app (Admin API REST — `product_listing`).
 * A chave do recurso e' o product_id (nao existe id proprio).
 */
class ProductListingMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    /**
     * Lista produtos publicados no canal (1 pagina, max 250).
     *
     * @param  array<string, mixed>  $params  ex.: product_ids, limit, collection_id, updated_at_min, handle
     */
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/product_listings', $params);
    }

    /**
     * Itera TODOS os produtos publicados (paginacao por cursor).
     *
     * @param  array<string, mixed>  $params
     * @return \Generator<int, array<string, mixed>>
     */
    public function each(array $params = [], int $limit = 250): \Generator
    {
        return $this->eachPage('/product_listings', 'product_listings', $params, $limit);
    }

    /**
     * Conta produtos publicados no canal.
     */
    public function count(): int
    {
        $response = $this->makeRequest(HttpMethod::GET, '/product_listings/count');

        return $response['count'] ?? 0;
    }

    /**
     * Ids de todos os produtos publicados no canal (`product_ids`).
     *
     * @param  array<string, mixed>  $params  ex.: limit
     */
    public function productIds(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/product_listings/product_ids', $params);
    }

    /**
     * Recupera a publicacao de um produto.
     */
    public function get(int|string $productId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/product_listings/{$productId}");
    }

    /**
     * Publica (ou atualiza a publicacao de) um produto no canal.
     *
     * @param  array<string, mixed>  $listing  normalmente ['product_id' => $productId]
     */
    public function update(int|string $productId, array $listing = []): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/product_listings/{$productId}", [], [
            'product_listing' => $listing + ['product_id' => $productId],
        ]);
    }

    /**
     * Despublica um produto do canal.
     */
    public function delete(int|string $productId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/product_listings/{$productId}");
    }
}

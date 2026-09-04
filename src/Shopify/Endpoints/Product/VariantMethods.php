<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Product;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Variantes de produto (Admin API REST — `variant`).
 */
class VariantMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    /**
     * Lista variantes de um produto (1 pagina, max 250).
     *
     * @param  array<string, mixed>  $params  ex.: limit, since_id, fields, presentment_currencies
     */
    public function list(int|string $productId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/products/{$productId}/variants", $params);
    }

    /**
     * Itera TODAS as variantes de um produto (paginacao por cursor).
     *
     * @param  array<string, mixed>  $params
     * @return \Generator<int, array<string, mixed>>
     */
    public function each(int|string $productId, array $params = [], int $limit = 250): \Generator
    {
        return $this->eachPage("/products/{$productId}/variants", 'variants', $params, $limit);
    }

    /**
     * Conta variantes de um produto.
     */
    public function count(int|string $productId): int
    {
        $response = $this->makeRequest(HttpMethod::GET, "/products/{$productId}/variants/count");

        return $response['count'] ?? 0;
    }

    /**
     * Recupera uma variante pelo id.
     *
     * @param  array<string, mixed>  $params  ex.: fields
     */
    public function get(int|string $variantId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/variants/{$variantId}", $params);
    }

    /**
     * Cria uma variante no produto.
     *
     * @param  array<string, mixed>  $variant  ex.: option1, price, sku
     */
    public function create(int|string $productId, array $variant): array
    {
        return $this->makeRequest(HttpMethod::POST, "/products/{$productId}/variants", [], ['variant' => $variant]);
    }

    /**
     * Atualiza uma variante.
     *
     * @param  array<string, mixed>  $variant
     */
    public function update(int|string $variantId, array $variant): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/variants/{$variantId}", [], ['variant' => $variant]);
    }

    /**
     * Remove uma variante do produto.
     */
    public function delete(int|string $productId, int|string $variantId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/products/{$productId}/variants/{$variantId}");
    }
}

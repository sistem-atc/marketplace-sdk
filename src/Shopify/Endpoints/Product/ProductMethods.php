<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Product;

use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class ProductMethods extends BaseMethods
{
    /**
     * Recupera detalhes de um produto.
     */
    public function get(int|string $productId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/products/{$productId}");
    }

    /**
     * Cria um novo produto.
     */
    public function create(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, '/products', [], ['product' => $data]);
    }

    /**
     * Atualiza um produto existente.
     */
    public function update(int|string $productId, array $data): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/products/{$productId}", [], ['product' => $data]);
    }

    /**
     * Remove um produto.
     */
    public function delete(int|string $productId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/products/{$productId}");
    }

    /**
     * Lista produtos (paginado).
     */
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/products', $params);
    }

    /**
     * Conta produtos.
     */
    public function count(array $params = []): int
    {
        $response = $this->makeRequest(HttpMethod::GET, '/products/count', $params);
        return $response['count'] ?? 0;
    }
}

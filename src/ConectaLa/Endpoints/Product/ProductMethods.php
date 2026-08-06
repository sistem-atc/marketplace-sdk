<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Endpoints\Product;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\ConectaLa\Bases\BaseMethods;

/**
 * Produtos (Conecta Lá / Shophub). Mesma lógica de "fila": produtos alterados
 * entram em `/Products/modified`; você reflete e remove da fila. Preço e estoque
 * são PUT no mesmo recurso `/Products/{sku}` com payload específico.
 */
class ProductMethods extends BaseMethods
{
    /** Fila de produtos alterados (GET /Products/modified). */
    public function modifiedQueue(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/Products/modified', $filters);
    }

    /** Lista paginada de produtos (GET /Products/list). */
    public function list(int $page = 1, int $perPage = 50, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/Products/list', array_merge([
            'page' => $page,
            'per_page' => $perPage,
        ], $filters));
    }

    /** Detalhe de um produto por SKU (GET /Products/{sku}). */
    public function get(string $sku): array
    {
        return $this->makeRequest(HttpMethod::GET, "/Products/{$sku}");
    }

    /** Atributos preenchidos do produto (GET /Products/attributes/{sku}). */
    public function attributes(string $sku): array
    {
        return $this->makeRequest(HttpMethod::GET, "/Products/attributes/{$sku}");
    }

    /** Remove o produto da fila de alterados (DELETE /Products/modified/{sku}). */
    public function removeFromQueue(string $sku): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/Products/modified/{$sku}");
    }

    /** Cria um produto (POST /Products). */
    public function create(array $product): array
    {
        return $this->makeRequest(HttpMethod::POST, '/Products', body: $product);
    }

    /** Atualiza um produto (PUT /Products/{sku}). */
    public function update(string $sku, array $product): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/Products/{$sku}", body: $product);
    }

    /** Atualiza SÓ o preço (PUT /Products/{sku} com payload de preço). */
    public function updatePrice(string $sku, array $price): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/Products/{$sku}", body: $price);
    }

    /** Atualiza SÓ o estoque (PUT /Products/{sku} com payload de estoque). */
    public function updateStock(string $sku, array $stock): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/Products/{$sku}", body: $stock);
    }
}

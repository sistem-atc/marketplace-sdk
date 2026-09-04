<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Metafield;

use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class MetafieldMethods extends BaseMethods
{
    /**
     * Lista metafields de um recurso (order, product, etc).
     * Ex: $path = "orders/123/metafields"
     */
    public function list(string $resourcePath): array
    {
        return $this->makeRequest(HttpMethod::GET, "/{$resourcePath}/metafields");
    }

    /**
     * Cria ou atualiza um metafield.
     */
    public function create(string $resourcePath, array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, "/{$resourcePath}/metafields", [], ['metafield' => $data]);
    }

    /**
     * Remove um metafield.
     */
    public function delete(string $resourcePath, int|string $metafieldId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/{$resourcePath}/metafields/{$metafieldId}");
    }

    /**
     * Recupera um metafield de um recurso. Ex: $resourcePath = "products/123".
     */
    public function get(string $resourcePath, int|string $metafieldId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/{$resourcePath}/metafields/{$metafieldId}");
    }

    /**
     * Atualiza um metafield de um recurso. Embrulha em `metafield`.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(string $resourcePath, int|string $metafieldId, array $data): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/{$resourcePath}/metafields/{$metafieldId}", [], ['metafield' => $data]);
    }

    /**
     * Conta os metafields de um recurso.
     */
    public function count(string $resourcePath): array
    {
        return $this->makeRequest(HttpMethod::GET, "/{$resourcePath}/metafields/count");
    }

    /**
     * Lista os metafields da LOJA (GET /metafields). Filtros: namespace, key,
     * type, limit, since_id, created_at_min/max, updated_at_min/max, fields,
     * `metafield[owner_id]` + `metafield[owner_resource]` pra outro dono.
     *
     * @param  array<string, mixed>  $params
     */
    public function listShop(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/metafields', $params);
    }

    /**
     * Conta os metafields da loja.
     *
     * @param  array<string, mixed>  $params
     */
    public function countShop(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/metafields/count', $params);
    }

    /**
     * Recupera um metafield pelo ID (GET /metafields/{id}).
     */
    public function getShop(int|string $metafieldId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/metafields/{$metafieldId}");
    }

    /**
     * Cria um metafield da loja (POST /metafields). Embrulha em `metafield`.
     *
     * @param  array<string, mixed>  $data
     */
    public function createShop(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, '/metafields', [], ['metafield' => $data]);
    }

    /**
     * Atualiza um metafield pelo ID (PUT /metafields/{id}). Embrulha em `metafield`.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateShop(int|string $metafieldId, array $data): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/metafields/{$metafieldId}", [], ['metafield' => $data]);
    }

    /**
     * Remove um metafield pelo ID (DELETE /metafields/{id}).
     */
    public function deleteShop(int|string $metafieldId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/metafields/{$metafieldId}");
    }
}

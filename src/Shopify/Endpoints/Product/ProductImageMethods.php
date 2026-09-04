<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Product;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Imagens de produto (`products/{product_id}/images`).
 * Deprecado no REST desde 2024-04 (usar `productCreateMedia` no GraphQL); ainda responde.
 */
class ProductImageMethods extends BaseMethods
{
    /**
     * Lista as imagens de um produto (since_id, fields).
     *
     * @param  array<string, mixed>  $params
     */
    public function list(int|string $productId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/products/{$productId}/images", $params);
    }

    /**
     * Conta as imagens de um produto (since_id).
     *
     * @param  array<string, mixed>  $params
     */
    public function count(int|string $productId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/products/{$productId}/images/count", $params);
    }

    /**
     * Recupera uma imagem.
     */
    public function get(int|string $productId, int|string $imageId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/products/{$productId}/images/{$imageId}", $params);
    }

    /**
     * Cria uma imagem (src URL ou attachment base64). Embrulha em `image`.
     *
     * @param  array<string, mixed>  $image
     */
    public function create(int|string $productId, array $image): array
    {
        return $this->makeRequest(HttpMethod::POST, "/products/{$productId}/images", [], ['image' => $image]);
    }

    /**
     * Atualiza uma imagem (position, alt, variant_ids...). Embrulha em `image`.
     *
     * @param  array<string, mixed>  $image
     */
    public function update(int|string $productId, int|string $imageId, array $image): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/products/{$productId}/images/{$imageId}", [], ['image' => $image]);
    }

    /**
     * Exclui uma imagem.
     */
    public function delete(int|string $productId, int|string $imageId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/products/{$productId}/images/{$imageId}");
    }
}

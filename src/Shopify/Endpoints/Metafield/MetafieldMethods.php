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
}

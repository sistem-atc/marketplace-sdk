<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Product;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class ProductMethods extends BaseMethods
{
    public function search(array $filters = [], int $pageSize = 20): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/products/v202309/products/search', [], array_merge([
            'page_size' => $pageSize,
        ], $filters));
    }

    public function get(string $productId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/api/products/v202309/products/{$productId}");
    }

    public function updatePrice(string $productId, array $skus): array
    {
        return $this->makeRequest(HttpMethod::POST, "/api/products/v202309/products/{$productId}/prices/update", [], [
            'skus' => $skus,
        ]);
    }

    public function updateStock(string $productId, array $skus): array
    {
        return $this->makeRequest(HttpMethod::POST, "/api/products/v202309/products/{$productId}/stocks/update", [], [
            'skus' => $skus,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Product;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class ProductMethods extends BaseMethods
{
    /** Versao da API de produto (mesma convencao de /order/{version}/...). */
    private const VERSION = '202309';

    /**
     * Busca produtos do seller. page_size e page_token vao na QUERY (a API
     * recusa page_size no body); os filtros (status, etc) vao no body.
     */
    public function search(array $filters = [], int $pageSize = 20, ?string $pageToken = null): array
    {
        $query = ['page_size' => $pageSize];
        if ($pageToken !== null && $pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        return $this->makeRequest(HttpMethod::POST, '/product/'.self::VERSION.'/products/search', $query, $filters);
    }

    public function get(string $productId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/product/'.self::VERSION."/products/{$productId}");
    }

    public function updatePrice(string $productId, array $skus): array
    {
        return $this->makeRequest(HttpMethod::POST, '/product/'.self::VERSION."/products/{$productId}/prices/update", [], [
            'skus' => $skus,
        ]);
    }

    public function updateStock(string $productId, array $skus): array
    {
        return $this->makeRequest(HttpMethod::POST, '/product/'.self::VERSION."/products/{$productId}/stocks/update", [], [
            'skus' => $skus,
        ]);
    }
}

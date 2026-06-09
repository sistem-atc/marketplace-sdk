<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Product;

use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class ProductMethods extends BaseMethods
{
    public function list(int $limit = 50, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/items', [
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    public function get(string $sku): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v1/items/{$sku}");
    }

    public function updatePrice(string $sku, float $price): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/seller/v1/items/{$sku}/price", [], [
            'price' => $price,
        ]);
    }

    public function updateStock(string $sku, int $quantity): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/seller/v1/items/{$sku}/stock", [], [
            'quantity' => $quantity,
        ]);
    }
}

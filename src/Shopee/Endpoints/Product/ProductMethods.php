<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Product;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class ProductMethods extends BaseMethods
{
    public function getItemList(int $offset = 0, int $pageSize = 20, array $filters = []): array
    {
        $query = array_merge([
            'offset' => $offset,
            'page_size' => $pageSize,
            'item_status' => 'NORMAL',
        ], $filters);

        return $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_item_list', $query);
    }

    public function getItemBaseInfo(array $itemIds): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_item_base_info', [
            'item_id_list' => implode(',', $itemIds),
        ]);
    }

    public function updateStock(int $itemId, int $stock, ?int $variationId = null): array
    {
        $body = [
            'item_id' => $itemId,
            'stock_list' => [
                ['normal_stock' => $stock]
            ]
        ];
        if ($variationId) {
            $body['stock_list'][0]['model_id'] = $variationId;
        }

        return $this->makeRequest(HttpMethod::POST, '/api/v2/product/update_stock', [], $body);
    }

    public function updatePrice(int $itemId, float $price, ?int $variationId = null): array
    {
        $body = [
            'item_id' => $itemId,
            'price_list' => [
                ['original_price' => $price]
            ]
        ];
        if ($variationId) {
            $body['price_list'][0]['model_id'] = $variationId;
        }

        return $this->makeRequest(HttpMethod::POST, '/api/v2/product/update_price', [], $body);
    }
}

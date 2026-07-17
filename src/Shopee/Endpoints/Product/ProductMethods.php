<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Product;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Product\ItemBaseInfoResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Product\ItemListResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Product\ModelListResponseDTO;

class ProductMethods extends BaseMethods
{
    /** Paginacao por OFFSET (nao cursor): pagine enquanto ->hasNextPage. */
    public function getItemList(int $offset = 0, int $pageSize = 20, array $filters = []): ItemListResponseDTO
    {
        $query = array_merge([
            'offset' => $offset,
            'page_size' => $pageSize,
            'item_status' => 'NORMAL',
        ], $filters);

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_item_list', $query);

        return ItemListResponseDTO::fromArray($response['response'] ?? []);
    }

    public function getItemBaseInfo(array $itemIds): ItemBaseInfoResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_item_base_info', [
            'item_id_list' => implode(',', $itemIds),
        ]);

        return ItemBaseInfoResponseDTO::fromArray($response['response'] ?? []);
    }

    /**
     * Variacoes (models) de um anuncio com `has_model: true`. O preco de itens com
     * variacao NAO vem no item (price_info null) — vem por model aqui.
     *
     * GET /api/v2/product/get_model_list?item_id={id}
     *
     * Retorna response.model[] cada um com model_id, model_sku, tier_index[] e
     * price_info[{current_price, original_price}]; + response.tier_variation[] (os
     * nomes/opcoes das variacoes, ex.: "Peso" => 250g/500g/1Kg).
     */
    public function getModelList(int $itemId): ModelListResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/product/get_model_list', [
            'item_id' => $itemId,
        ]);

        return ModelListResponseDTO::fromArray($response['response'] ?? []);
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

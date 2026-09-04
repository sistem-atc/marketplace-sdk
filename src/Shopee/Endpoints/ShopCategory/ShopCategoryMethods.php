<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\ShopCategory;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Categorias DA LOJA (vitrine/colecoes do seller) — nao confundir com a
 * arvore de categorias da Shopee (CategoryMethods). Modulo `shop_category`.
 *
 * Paginacao por page_no (1-based) + page_size. Listas de item vao no body
 * como int[] (max 100 por chamada).
 */
class ShopCategoryMethods extends BaseMethods
{
    /**
     * Lista categorias da loja. page_size 1..100, page_no >= 1.
     *
     * GET /api/v2/shop_category/get_shop_category_list
     *
     * @return array<string, mixed> response{shop_categorys[], more, total_count}
     */
    public function getShopCategoryList(int $pageNo = 1, int $pageSize = 100): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/shop_category/get_shop_category_list', [
            'page_size' => $pageSize,
            'page_no' => $pageNo,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Cria categoria da loja. sort_weight define a ordem na vitrine (max 2147483546).
     *
     * POST /api/v2/shop_category/add_shop_category
     *
     * @return array<string, mixed> response{shop_category_id}
     */
    public function addShopCategory(string $name, ?int $sortWeight = null): array
    {
        $body = ['name' => $name];
        if ($sortWeight !== null) {
            $body['sort_weight'] = $sortWeight;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/shop_category/add_shop_category', [], $body);

        return $response['response'] ?? [];
    }

    /**
     * Altera nome/ordem/status (NORMAL, INACTIVE, DELETED) de uma categoria da loja.
     *
     * POST /api/v2/shop_category/update_shop_category
     *
     * @return array<string, mixed> response{shop_category_id, name, sort_weight, status}
     */
    public function updateShopCategory(int $shopCategoryId, ?string $name = null, ?int $sortWeight = null, ?string $status = null): array
    {
        $body = ['shop_category_id' => $shopCategoryId];
        if ($name !== null) {
            $body['name'] = $name;
        }
        if ($sortWeight !== null) {
            $body['sort_weight'] = $sortWeight;
        }
        if ($status !== null) {
            $body['status'] = $status;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/shop_category/update_shop_category', [], $body);

        return $response['response'] ?? [];
    }

    /**
     * Exclui categoria da loja (os itens continuam existindo).
     *
     * POST /api/v2/shop_category/delete_shop_category
     *
     * @return array<string, mixed> response{shop_category_id}
     */
    public function deleteShopCategory(int $shopCategoryId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/shop_category/delete_shop_category', [], [
            'shop_category_id' => $shopCategoryId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Itens de uma categoria da loja. page_size ate 1000 (default da Shopee).
     *
     * GET /api/v2/shop_category/get_item_list
     *
     * @return array<string, mixed> response{item_list[], more, total_count}
     */
    public function getItemList(int $shopCategoryId, int $pageNo = 1, int $pageSize = 1000): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/shop_category/get_item_list', [
            'shop_category_id' => $shopCategoryId,
            'page_size' => $pageSize,
            'page_no' => $pageNo,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Adiciona ate 100 itens a categoria da loja.
     *
     * POST /api/v2/shop_category/add_item_list
     *
     * @param  array<int, int>  $itemIds
     * @return array<string, mixed> response{shop_category_id, current_count, invalid_item_id_list?}
     */
    public function addItemList(int $shopCategoryId, array $itemIds): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/shop_category/add_item_list', [], [
            'shop_category_id' => $shopCategoryId,
            'item_list' => array_values($itemIds),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Remove ate 100 itens da categoria da loja (ids invalidos voltam em
     * invalid_item_id_list, os demais sao removidos mesmo assim).
     *
     * POST /api/v2/shop_category/delete_item_list
     *
     * @param  array<int, int>  $itemIds
     * @return array<string, mixed> response{shop_category_id, current_count, invalid_item_id_list}
     */
    public function deleteItemList(int $shopCategoryId, array $itemIds): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/shop_category/delete_item_list', [], [
            'shop_category_id' => $shopCategoryId,
            'item_list' => array_values($itemIds),
        ]);

        return $response['response'] ?? [];
    }
}

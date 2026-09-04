<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Netshoes\Endpoints\Product;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Netshoes\Bases\BaseMethods;

/**
 * Produtos, status, precos e estoque por SKU — Swagger V2 (/api/v2).
 *
 *   GET/POST      /api/v2/products            (lista page/size/expands | cria)
 *   GET/PUT       /api/v2/products/{sku}      (detalhe | atualiza)
 *   GET/PUT       /api/v2/products/{sku}/status   (PUT = so' sandbox)
 *   GET/POST/PUT  /api/v2/products/{sku}/prices   {listPrice, salePrice}
 *   GET/POST/PUT  /api/v2/products/{sku}/stocks   {available}
 *
 * ProductRequest obrigatorios: sku, productGroup, department, productType,
 * brand, name, description, color, flavor, gender, size, height, width,
 * depth, weight, images[{url}]. Opcionais: manufacturerCode, eanIsbn, video,
 * fulfillment{candidate}, attributes[{name, values[]}]. Nomes de brand/color/
 * flavor/size/department vem do CatalogMethods.
 */
class ProductMethods extends BaseMethods
{
    /**
     * @param  array<string, mixed>  $query  page, size, expands
     * @return array<string, mixed>  ListProductResponse {items[]}
     */
    public function listProducts(array $query = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/products', $query);
    }

    /**
     * @param  array<string, mixed>  $product  ProductRequest
     * @return array<string, mixed>
     */
    public function createProduct(array $product): array
    {
        return $this->makeRequest(method: HttpMethod::POST, path: '/api/v2/products', body: $product);
    }

    /**
     * @param  array<int, string>  $expands
     * @return array<string, mixed>  ProductResponse
     */
    public function getProduct(string $sku, array $expands = []): array
    {
        return $this->makeRequest(
            method: HttpMethod::GET,
            path: $this->skuPath($sku),
            query: $expands ? ['expands' => implode(',', $expands)] : [],
        );
    }

    /**
     * @param  array<string, mixed>  $product  ProductRequest
     * @return array<string, mixed>
     */
    public function updateProduct(string $sku, array $product): array
    {
        return $this->makeRequest(method: HttpMethod::PUT, path: $this->skuPath($sku), body: $product);
    }

    /** @return array<string, mixed> ProductStatusResponse {status, reviews[{message, date}]} */
    public function getProductStatus(string $sku): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->skuPath($sku).'/status');
    }

    /**
     * SOMENTE SANDBOX — simula a moderacao da Netshoes.
     *
     * @return array<string, mixed>
     */
    public function updateProductStatus(string $sku, string $status): array
    {
        return $this->makeRequest(
            method: HttpMethod::PUT,
            path: $this->skuPath($sku).'/status',
            body: ['status' => $status],
        );
    }

    /** @return array<string, mixed> PriceResponse {listPrice, salePrice} */
    public function getPrice(string $sku): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->skuPath($sku).'/prices');
    }

    /** @return array<string, mixed> */
    public function createPrice(string $sku, float $listPrice, float $salePrice): array
    {
        return $this->makeRequest(
            method: HttpMethod::POST,
            path: $this->skuPath($sku).'/prices',
            body: ['listPrice' => $listPrice, 'salePrice' => $salePrice],
        );
    }

    /** @return array<string, mixed> */
    public function updatePrice(string $sku, float $listPrice, float $salePrice): array
    {
        return $this->makeRequest(
            method: HttpMethod::PUT,
            path: $this->skuPath($sku).'/prices',
            body: ['listPrice' => $listPrice, 'salePrice' => $salePrice],
        );
    }

    /** @return array<string, mixed> StockResponse {available} */
    public function getStock(string $sku): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->skuPath($sku).'/stocks');
    }

    /** @return array<string, mixed> */
    public function createStock(string $sku, int $available): array
    {
        return $this->makeRequest(
            method: HttpMethod::POST,
            path: $this->skuPath($sku).'/stocks',
            body: ['available' => $available],
        );
    }

    /** @return array<string, mixed> */
    public function updateStock(string $sku, int $available): array
    {
        return $this->makeRequest(
            method: HttpMethod::PUT,
            path: $this->skuPath($sku).'/stocks',
            body: ['available' => $available],
        );
    }

    private function skuPath(string $sku): string
    {
        return '/api/v2/products/'.rawurlencode($sku);
    }
}

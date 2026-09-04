<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Netshoes\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

const NETSHOES_PRODUCT_BASE = 'https://api-marketplace.netshoes.com.br';

beforeEach(function () {
    Http::preventStrayRequests();
    config(['marketplaces.netshoes.api_base' => NETSHOES_PRODUCT_BASE]);
});

function netshoesProduct(): ProductMethods
{
    return MarketPlaces::Netshoes()->products(
        new FakeIntegration(accessToken: 'tok-pr', settings: ['client_id' => 'cli-pr']),
    );
}

function netshoesProductSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn (Request $req) => $req->method() === $method
        && urldecode($req->url()) === $url
        && $req->hasHeader('client_id', 'cli-pr')
        && $req->hasHeader('access_token', 'tok-pr')
        && $req->hasHeader('content-type', 'application/json')
        && ($body === null || $req->data() === $body));
}

describe('Products — /api/v2/products', function () {
    it('listProducts: GET page/size/expands', function () {
        Http::fake(['*' => Http::response(['items' => []])]);

        netshoesProduct()->listProducts(['page' => 0, 'size' => 100, 'expands' => 'images']);

        netshoesProductSent('GET', NETSHOES_PRODUCT_BASE.'/api/v2/products?page=0&size=100&expands=images');
    });

    it('createProduct: POST ProductRequest', function () {
        Http::fake(['*' => Http::response([], 201)]);

        $p = ['sku' => 'WHEY-1', 'productGroup' => 'WHEY', 'name' => 'Whey', 'images' => [['url' => 'https://i/1.jpg']]];
        netshoesProduct()->createProduct($p);

        netshoesProductSent('POST', NETSHOES_PRODUCT_BASE.'/api/v2/products', $p);
    });

    it('getProduct: GET /products/{sku} com expands e rawurlencode', function () {
        Http::fake(['*' => Http::response(['sku' => 'A/B'])]);

        netshoesProduct()->getProduct('A/B', ['images', 'attributes']);

        Http::assertSent(fn (Request $req) => $req->method() === 'GET'
            && $req->url() === NETSHOES_PRODUCT_BASE.'/api/v2/products/A%2FB?expands=images%2Cattributes');
    });

    it('updateProduct: PUT /products/{sku}', function () {
        Http::fake(['*' => Http::response([])]);

        $p = ['sku' => 'WHEY-1', 'name' => 'Whey 900g'];
        netshoesProduct()->updateProduct('WHEY-1', $p);

        netshoesProductSent('PUT', NETSHOES_PRODUCT_BASE.'/api/v2/products/WHEY-1', $p);
    });

    it('getProductStatus: GET /products/{sku}/status', function () {
        Http::fake(['*' => Http::response(['status' => 'approved', 'reviews' => []])]);

        expect(netshoesProduct()->getProductStatus('WHEY-1')['status'])->toBe('approved');
        netshoesProductSent('GET', NETSHOES_PRODUCT_BASE.'/api/v2/products/WHEY-1/status');
    });

    it('updateProductStatus: PUT /products/{sku}/status {status} (sandbox)', function () {
        Http::fake(['*' => Http::response([])]);

        netshoesProduct()->updateProductStatus('WHEY-1', 'approved');

        netshoesProductSent('PUT', NETSHOES_PRODUCT_BASE.'/api/v2/products/WHEY-1/status', ['status' => 'approved']);
    });

    it('getPrice: GET /products/{sku}/prices', function () {
        Http::fake(['*' => Http::response(['listPrice' => 100, 'salePrice' => 90])]);

        expect(netshoesProduct()->getPrice('WHEY-1')['salePrice'])->toBe(90);
        netshoesProductSent('GET', NETSHOES_PRODUCT_BASE.'/api/v2/products/WHEY-1/prices');
    });

    it('createPrice: POST /products/{sku}/prices {listPrice, salePrice}', function () {
        Http::fake(['*' => Http::response([], 201)]);

        netshoesProduct()->createPrice('WHEY-1', 199.9, 149.9);

        netshoesProductSent('POST', NETSHOES_PRODUCT_BASE.'/api/v2/products/WHEY-1/prices', ['listPrice' => 199.9, 'salePrice' => 149.9]);
    });

    it('updatePrice: PUT /products/{sku}/prices', function () {
        Http::fake(['*' => Http::response([])]);

        netshoesProduct()->updatePrice('WHEY-1', 199.9, 139.9);

        netshoesProductSent('PUT', NETSHOES_PRODUCT_BASE.'/api/v2/products/WHEY-1/prices', ['listPrice' => 199.9, 'salePrice' => 139.9]);
    });

    it('getStock: GET /products/{sku}/stocks', function () {
        Http::fake(['*' => Http::response(['available' => 12])]);

        expect(netshoesProduct()->getStock('WHEY-1')['available'])->toBe(12);
        netshoesProductSent('GET', NETSHOES_PRODUCT_BASE.'/api/v2/products/WHEY-1/stocks');
    });

    it('createStock: POST /products/{sku}/stocks {available}', function () {
        Http::fake(['*' => Http::response([], 201)]);

        netshoesProduct()->createStock('WHEY-1', 50);

        netshoesProductSent('POST', NETSHOES_PRODUCT_BASE.'/api/v2/products/WHEY-1/stocks', ['available' => 50]);
    });

    it('updateStock: PUT /products/{sku}/stocks {available}', function () {
        Http::fake(['*' => Http::response([])]);

        netshoesProduct()->updateStock('WHEY-1', 0);

        netshoesProductSent('PUT', NETSHOES_PRODUCT_BASE.'/api/v2/products/WHEY-1/stocks', ['available' => 0]);
    });
});

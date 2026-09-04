<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Netshoes\Endpoints\Catalog\CatalogMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

const NETSHOES_CATALOG_BASE = 'https://api-marketplace.netshoes.com.br';

beforeEach(function () {
    Http::preventStrayRequests();
    config(['marketplaces.netshoes.api_base' => NETSHOES_CATALOG_BASE]);
});

function netshoesCatalog(): CatalogMethods
{
    return MarketPlaces::Netshoes()->catalog(
        new FakeIntegration(accessToken: 'tok-c', settings: ['client_id' => 'cli-c']),
    );
}

function netshoesCatalogSentGet(string $url): void
{
    Http::assertSent(fn (Request $req) => $req->method() === 'GET'
        && $req->url() === $url
        && $req->hasHeader('client_id', 'cli-c')
        && $req->hasHeader('access_token', 'tok-c'));
}

describe('Catalog (Products Templates) — /api/v1', function () {
    it('listBrands', function () {
        Http::fake(['*' => Http::response(['items' => [['code' => 1, 'name' => 'Soldiers']]])]);

        expect(netshoesCatalog()->listBrands()['items'][0]['name'])->toBe('Soldiers');
        netshoesCatalogSentGet(NETSHOES_CATALOG_BASE.'/api/v1/brands');
    });

    it('listColors', function () {
        Http::fake(['*' => Http::response(['items' => []])]);
        netshoesCatalog()->listColors();
        netshoesCatalogSentGet(NETSHOES_CATALOG_BASE.'/api/v1/colors');
    });

    it('listFlavors', function () {
        Http::fake(['*' => Http::response(['items' => []])]);
        netshoesCatalog()->listFlavors();
        netshoesCatalogSentGet(NETSHOES_CATALOG_BASE.'/api/v1/flavors');
    });

    it('listSizes', function () {
        Http::fake(['*' => Http::response(['items' => []])]);
        netshoesCatalog()->listSizes();
        netshoesCatalogSentGet(NETSHOES_CATALOG_BASE.'/api/v1/sizes');
    });

    it('listSellers', function () {
        Http::fake(['*' => Http::response([['code' => 7, 'name' => 'SDN']])]);
        expect(netshoesCatalog()->listSellers()[0]['code'])->toBe(7);
        netshoesCatalogSentGet(NETSHOES_CATALOG_BASE.'/api/v1/sellers');
    });

    it('listDepartments: /bus/{buId}/departments com rawurlencode', function () {
        Http::fake(['*' => Http::response(['items' => []])]);
        netshoesCatalog()->listDepartments('Net shoes');
        netshoesCatalogSentGet(NETSHOES_CATALOG_BASE.'/api/v1/bus/Net%20shoes/departments');
    });

    it('listProductTypes: /department/{code}/productType', function () {
        Http::fake(['*' => Http::response(['items' => []])]);
        netshoesCatalog()->listProductTypes(12);
        netshoesCatalogSentGet(NETSHOES_CATALOG_BASE.'/api/v1/department/12/productType');
    });

    it('listTemplates: /department/{code}/productType/{type}/templates', function () {
        Http::fake(['*' => Http::response(['items' => []])]);
        netshoesCatalog()->listTemplates(12, '345');
        netshoesCatalogSentGet(NETSHOES_CATALOG_BASE.'/api/v1/department/12/productType/345/templates');
    });
});

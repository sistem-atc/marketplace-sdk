<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\OnlineStore\PageMethods;

require_once __DIR__.'/Helpers/ShopifyRest2Helpers.php';

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('Shopify PageMethods', function () {
    it('list', fn () => shopifyRest2Call(fn () => shopifyRest2Make(PageMethods::class)->list(['published_status' => 'published']), 'GET', 'pages.json?published_status=published'));
    it('count', fn () => shopifyRest2Call(fn () => shopifyRest2Make(PageMethods::class)->count(), 'GET', 'pages/count.json', null, ['count' => 3]));
    it('get', fn () => shopifyRest2Call(fn () => shopifyRest2Make(PageMethods::class)->get(6), 'GET', 'pages/6.json'));
    it('create embrulha em page', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(PageMethods::class)->create(['title' => 'Sobre', 'body_html' => '<p>x</p>']),
        'POST', 'pages.json', ['page' => ['title' => 'Sobre', 'body_html' => '<p>x</p>']],
    ));
    it('update embrulha em page', fn () => shopifyRest2Call(fn () => shopifyRest2Make(PageMethods::class)->update(6, ['title' => 'Novo']), 'PUT', 'pages/6.json', ['page' => ['title' => 'Novo']]));
    it('delete', fn () => shopifyRest2Call(fn () => shopifyRest2Make(PageMethods::class)->delete(6), 'DELETE', 'pages/6.json'));
    it('each segue o page_info', function () {
        Http::fake([
            '*/pages.json*' => Http::sequence()
                ->push(['pages' => [['id' => 1]]], 200, ['Link' => '<https://loja-teste.myshopify.com/admin/api/2024-04/pages.json?limit=250&page_info=C2>; rel="next"'])
                ->push(['pages' => [['id' => 2]]], 200, []),
        ]);
        expect(array_column(iterator_to_array(shopifyRest2Make(PageMethods::class)->each()), 'id'))->toBe([1, 2]);
    });
});

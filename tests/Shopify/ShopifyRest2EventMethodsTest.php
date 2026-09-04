<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Event\EventMethods;

require_once __DIR__.'/Helpers/ShopifyRest2Helpers.php';

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('Shopify EventMethods', function () {
    it('list', fn () => shopifyRest2Call(fn () => shopifyRest2Make(EventMethods::class)->list(['filter' => 'Order', 'verb' => 'confirmed']), 'GET', 'events.json?filter=Order&verb=confirmed'));
    it('count', fn () => shopifyRest2Call(fn () => shopifyRest2Make(EventMethods::class)->count(), 'GET', 'events/count.json', null, ['count' => 9]));
    it('get', fn () => shopifyRest2Call(fn () => shopifyRest2Make(EventMethods::class)->get(3), 'GET', 'events/3.json'));
    it('listForOrder', fn () => shopifyRest2Call(fn () => shopifyRest2Make(EventMethods::class)->listForOrder(10), 'GET', 'orders/10/events.json'));
    it('listForProduct', fn () => shopifyRest2Call(fn () => shopifyRest2Make(EventMethods::class)->listForProduct(20, ['limit' => 1]), 'GET', 'products/20/events.json?limit=1'));
    it('each segue o page_info', function () {
        Http::fake([
            '*/events.json*' => Http::sequence()
                ->push(['events' => [['id' => 1]]], 200, ['Link' => '<https://loja-teste.myshopify.com/admin/api/2024-04/events.json?limit=250&page_info=C2>; rel="next"'])
                ->push(['events' => [['id' => 2]]], 200, []),
        ]);
        expect(array_column(iterator_to_array(shopifyRest2Make(EventMethods::class)->each()), 'id'))->toBe([1, 2]);
    });
});

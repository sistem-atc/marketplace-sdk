<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Order\OrderMethods;

require_once __DIR__.'/Helpers/ShopifyRest2Helpers.php';

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('Shopify OrderMethods (chunk2: count/create/update/delete)', function () {
    it('count', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(OrderMethods::class)->count(['status' => 'any', 'financial_status' => 'paid']),
        'GET', 'orders/count.json?status=any&financial_status=paid', null, ['count' => 3],
    ));

    it('create embrulha em order', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(OrderMethods::class)->create(['line_items' => [['variant_id' => 1, 'quantity' => 2]]]),
        'POST', 'orders.json', ['order' => ['line_items' => [['variant_id' => 1, 'quantity' => 2]]]],
    ));

    it('update embrulha em order', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(OrderMethods::class)->update(450789469, ['note' => 'x']),
        'PUT', 'orders/450789469.json', ['order' => ['note' => 'x']],
    ));

    it('delete', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(OrderMethods::class)->delete(450789469),
        'DELETE', 'orders/450789469.json',
    ));

    it('metodos preexistentes continuam intactos', function () {
        $m = shopifyRest2Make(OrderMethods::class);
        foreach (['get', 'getByNumber', 'listByPeriod', 'list', 'cancel', 'close', 'open', 'eachByPeriod'] as $method) {
            expect(method_exists($m, $method))->toBeTrue();
        }
    });
});

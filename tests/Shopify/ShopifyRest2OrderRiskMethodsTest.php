<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Order\OrderRiskMethods;

require_once __DIR__.'/Helpers/ShopifyRest2Helpers.php';

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('Shopify OrderRiskMethods', function () {
    it('list', fn () => shopifyRest2Call(fn () => shopifyRest2Make(OrderRiskMethods::class)->list(10), 'GET', 'orders/10/risks.json'));
    it('get', fn () => shopifyRest2Call(fn () => shopifyRest2Make(OrderRiskMethods::class)->get(10, 5), 'GET', 'orders/10/risks/5.json'));
    it('create embrulha em risk', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(OrderRiskMethods::class)->create(10, ['message' => 'fraude', 'recommendation' => 'cancel']),
        'POST', 'orders/10/risks.json', ['risk' => ['message' => 'fraude', 'recommendation' => 'cancel']],
    ));
    it('update embrulha em risk', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(OrderRiskMethods::class)->update(10, 5, ['message' => 'ok']),
        'PUT', 'orders/10/risks/5.json', ['risk' => ['message' => 'ok']],
    ));
    it('delete', fn () => shopifyRest2Call(fn () => shopifyRest2Make(OrderRiskMethods::class)->delete(10, 5), 'DELETE', 'orders/10/risks/5.json'));
});

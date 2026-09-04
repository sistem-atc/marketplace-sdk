<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Fulfillment\FulfillmentServiceMethods;

require_once __DIR__.'/Helpers/ShopifyRest2Helpers.php';

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('Shopify FulfillmentServiceMethods', function () {
    it('list', fn () => shopifyRest2Call(fn () => shopifyRest2Make(FulfillmentServiceMethods::class)->list(['scope' => 'all']), 'GET', 'fulfillment_services.json?scope=all'));
    it('get', fn () => shopifyRest2Call(fn () => shopifyRest2Make(FulfillmentServiceMethods::class)->get(5), 'GET', 'fulfillment_services/5.json'));
    it('create embrulha em fulfillment_service', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(FulfillmentServiceMethods::class)->create(['name' => '3PL', 'callback_url' => 'https://x']),
        'POST', 'fulfillment_services.json', ['fulfillment_service' => ['name' => '3PL', 'callback_url' => 'https://x']],
    ));
    it('update embrulha em fulfillment_service', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(FulfillmentServiceMethods::class)->update(5, ['name' => 'novo']),
        'PUT', 'fulfillment_services/5.json', ['fulfillment_service' => ['name' => 'novo']],
    ));
    it('delete', fn () => shopifyRest2Call(fn () => shopifyRest2Make(FulfillmentServiceMethods::class)->delete(5), 'DELETE', 'fulfillment_services/5.json'));
});

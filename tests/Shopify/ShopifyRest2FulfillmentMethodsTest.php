<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Fulfillment\FulfillmentMethods;

require_once __DIR__.'/Helpers/ShopifyRest2Helpers.php';

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('Shopify FulfillmentMethods (chunk2: get/count/cancel/tracking/events)', function () {
    it('get', fn () => shopifyRest2Call(fn () => shopifyRest2Make(FulfillmentMethods::class)->get(10, 20), 'GET', 'orders/10/fulfillments/20.json'));
    it('count', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(FulfillmentMethods::class)->count(10, ['created_at_min' => '2026-01-01T00:00:00-03:00']),
        'GET', 'orders/10/fulfillments/count.json?created_at_min=2026-01-01T00:00:00-03:00', null, ['count' => 1],
    ));
    it('listForFulfillmentOrder', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(FulfillmentMethods::class)->listForFulfillmentOrder(77, ['limit' => 5]),
        'GET', 'fulfillment_orders/77/fulfillments.json?limit=5',
    ));
    it('cancel', fn () => shopifyRest2Call(fn () => shopifyRest2Make(FulfillmentMethods::class)->cancel(20), 'POST', 'fulfillments/20/cancel.json', []));
    it('updateTracking embrulha em fulfillment', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(FulfillmentMethods::class)->updateTracking(20, ['notify_customer' => true, 'tracking_info' => ['number' => 'BR1']]),
        'POST', 'fulfillments/20/update_tracking.json', ['fulfillment' => ['notify_customer' => true, 'tracking_info' => ['number' => 'BR1']]],
    ));
    it('listEvents', fn () => shopifyRest2Call(fn () => shopifyRest2Make(FulfillmentMethods::class)->listEvents(10, 20), 'GET', 'orders/10/fulfillments/20/events.json'));
    it('getEvent', fn () => shopifyRest2Call(fn () => shopifyRest2Make(FulfillmentMethods::class)->getEvent(10, 20, 30), 'GET', 'orders/10/fulfillments/20/events/30.json'));
    it('createEvent embrulha em event', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(FulfillmentMethods::class)->createEvent(10, 20, ['status' => 'in_transit']),
        'POST', 'orders/10/fulfillments/20/events.json', ['event' => ['status' => 'in_transit']],
    ));
    it('deleteEvent', fn () => shopifyRest2Call(fn () => shopifyRest2Make(FulfillmentMethods::class)->deleteEvent(10, 20, 30), 'DELETE', 'orders/10/fulfillments/20/events/30.json'));
    it('metodos preexistentes continuam intactos', function () {
        $m = shopifyRest2Make(FulfillmentMethods::class);
        foreach (['list', 'create', 'getFulfillmentOrders', 'createFromFulfillmentOrder'] as $method) {
            expect(method_exists($m, $method))->toBeTrue();
        }
    });
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Fulfillment\FulfillmentOrderMethods;

require_once __DIR__.'/Helpers/ShopifyRest2Helpers.php';

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('Shopify FulfillmentOrderMethods', function () {
    it('get', fn () => shopifyRest2Call(fn () => shopifyRest2Make(FulfillmentOrderMethods::class)->get(77), 'GET', 'fulfillment_orders/77.json'));
    it('listForOrder', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(FulfillmentOrderMethods::class)->listForOrder(10, ['include_financial_summaries' => 'true']),
        'GET', 'orders/10/fulfillment_orders.json?include_financial_summaries=true',
    ));
    it('cancel', fn () => shopifyRest2Call(fn () => shopifyRest2Make(FulfillmentOrderMethods::class)->cancel(77), 'POST', 'fulfillment_orders/77/cancel.json', []));
    it('close com mensagem', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(FulfillmentOrderMethods::class)->close(77, 'sem estoque'),
        'POST', 'fulfillment_orders/77/close.json', ['fulfillment_order' => ['message' => 'sem estoque']],
    ));
    it('hold embrulha em fulfillment_hold', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(FulfillmentOrderMethods::class)->hold(77, ['reason' => 'other', 'reason_notes' => 'x']),
        'POST', 'fulfillment_orders/77/hold.json', ['fulfillment_hold' => ['reason' => 'other', 'reason_notes' => 'x']],
    ));
    it('move manda new_location_id', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(FulfillmentOrderMethods::class)->move(77, 999),
        'POST', 'fulfillment_orders/77/move.json', ['fulfillment_order' => ['new_location_id' => 999]],
    ));
    it('open', fn () => shopifyRest2Call(fn () => shopifyRest2Make(FulfillmentOrderMethods::class)->open(77), 'POST', 'fulfillment_orders/77/open.json', []));
    it('releaseHold', fn () => shopifyRest2Call(fn () => shopifyRest2Make(FulfillmentOrderMethods::class)->releaseHold(77), 'POST', 'fulfillment_orders/77/release_hold.json', []));
    it('reschedule', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(FulfillmentOrderMethods::class)->reschedule(77, '2026-10-01T00:00:00-03:00'),
        'POST', 'fulfillment_orders/77/reschedule.json', ['fulfillment_order' => ['new_fulfill_at' => '2026-10-01T00:00:00-03:00']],
    ));
    it('setDeadline', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(FulfillmentOrderMethods::class)->setDeadline([77, 78], '2026-10-01T00:00:00-03:00'),
        'POST', 'fulfillment_orders/set_fulfillment_orders_deadline.json', ['fulfillment_order_ids' => [77, 78], 'fulfillment_deadline' => '2026-10-01T00:00:00-03:00'],
    ));
    it('sendRequest embrulha em fulfillment_request', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(FulfillmentOrderMethods::class)->sendRequest(77, ['message' => 'vai']),
        'POST', 'fulfillment_orders/77/fulfillment_request.json', ['fulfillment_request' => ['message' => 'vai']],
    ));
    it('acceptRequest', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(FulfillmentOrderMethods::class)->acceptRequest(77, 'ok'),
        'POST', 'fulfillment_orders/77/fulfillment_request/accept.json', ['fulfillment_request' => ['message' => 'ok']],
    ));
    it('rejectRequest com reason', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(FulfillmentOrderMethods::class)->rejectRequest(77, 'nao', ['reason' => 'inventory_out_of_stock']),
        'POST', 'fulfillment_orders/77/fulfillment_request/reject.json', ['fulfillment_request' => ['message' => 'nao', 'reason' => 'inventory_out_of_stock']],
    ));
});

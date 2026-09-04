<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\DraftOrder\DraftOrderMethods;

require_once __DIR__.'/Helpers/ShopifyRest2Helpers.php';

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('Shopify DraftOrderMethods', function () {
    it('list', fn () => shopifyRest2Call(fn () => shopifyRest2Make(DraftOrderMethods::class)->list(['status' => 'open']), 'GET', 'draft_orders.json?status=open'));
    it('count', fn () => shopifyRest2Call(fn () => shopifyRest2Make(DraftOrderMethods::class)->count(), 'GET', 'draft_orders/count.json', null, ['count' => 2]));
    it('get', fn () => shopifyRest2Call(fn () => shopifyRest2Make(DraftOrderMethods::class)->get(1), 'GET', 'draft_orders/1.json'));
    it('create embrulha em draft_order', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(DraftOrderMethods::class)->create(['line_items' => [['title' => 'x', 'price' => '1.00', 'quantity' => 1]]]),
        'POST', 'draft_orders.json', ['draft_order' => ['line_items' => [['title' => 'x', 'price' => '1.00', 'quantity' => 1]]]],
    ));
    it('update embrulha em draft_order', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(DraftOrderMethods::class)->update(1, ['note' => 'n']),
        'PUT', 'draft_orders/1.json', ['draft_order' => ['note' => 'n']],
    ));
    it('delete', fn () => shopifyRest2Call(fn () => shopifyRest2Make(DraftOrderMethods::class)->delete(1), 'DELETE', 'draft_orders/1.json'));
    it('sendInvoice embrulha em draft_order_invoice', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(DraftOrderMethods::class)->sendInvoice(1, ['to' => 'a@b.c']),
        'POST', 'draft_orders/1/send_invoice.json', ['draft_order_invoice' => ['to' => 'a@b.c']],
    ));
    it('sendInvoice vazio manda objeto JSON vazio (nao lista)', function () {
        shopifyRest2Call(fn () => shopifyRest2Make(DraftOrderMethods::class)->sendInvoice(1), 'POST', 'draft_orders/1/send_invoice.json');
        Http::assertSent(fn ($r) => $r->body() === '{"draft_order_invoice":{}}');
    });
    it('complete e PUT com payment_pending na query', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(DraftOrderMethods::class)->complete(1, true),
        'PUT', 'draft_orders/1/complete.json?payment_pending=true',
    ));

    it('each segue o page_info', function () {
        Http::fake([
            '*/draft_orders.json*' => Http::sequence()
                ->push(['draft_orders' => [['id' => 1]]], 200, ['Link' => '<https://loja-teste.myshopify.com/admin/api/2024-04/draft_orders.json?limit=250&page_info=C2>; rel="next"'])
                ->push(['draft_orders' => [['id' => 2]]], 200, []),
        ]);
        $all = iterator_to_array(shopifyRest2Make(DraftOrderMethods::class)->each(['status' => 'open']));
        expect(array_column($all, 'id'))->toBe([1, 2]);
        Http::assertSentCount(2);
        Http::assertSent(fn ($r) => str_contains($r->url(), 'page_info=C2') && ! str_contains($r->url(), 'status='));
    });
});

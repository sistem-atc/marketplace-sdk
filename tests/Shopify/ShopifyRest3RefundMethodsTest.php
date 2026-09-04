<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\Endpoints\Refund\RefundMethods;

function shopifyRest3RefundMethods(): SistemAtc\Marketplaces\Shopify\Endpoints\Refund\RefundMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shpat_fake',
        refreshToken: null,
        settings: ['shop_domain' => 'test-store.myshopify.com'],
        active: true,
        expired: false,
    );

    return new SistemAtc\Marketplaces\Shopify\Endpoints\Refund\RefundMethods(HttpClientFactory::make($integration), $integration);
}

/** Afirma verbo + URL completa + header do token (+ body, quando informado). */
function shopifyRest3RefundSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(function (Request $r) use ($method, $url, $body): bool {
        return $r->method() === $method
            && $r->url() === $url
            && $r->hasHeader('X-Shopify-Access-Token', 'shpat_fake')
            && ($body === null || $r->data() === $body);
    });
}

beforeEach(function () {
    config(['marketplaces.shopify.api_version' => '2024-04']);
    Http::preventStrayRequests();
});

$base = 'https://test-store.myshopify.com/admin/api/2024-04';

describe('Shopify RefundMethods (REST chunk 3)', function () use ($base) {
    $refund = ['shipping' => ['full_refund' => true], 'refund_line_items' => [['line_item_id' => 1, 'quantity' => 1, 'restock_type' => 'no_restock']]];

    it('list -> GET orders/{id}/refunds.json', function () use ($base) {
        Http::fake(['*' => Http::response(['refunds' => []])]);
        shopifyRest3RefundMethods()->list(100, ['limit' => 5]);
        shopifyRest3RefundSent('GET', "$base/orders/100/refunds.json?limit=5");
    });

    it('get -> GET orders/{id}/refunds/{rid}.json', function () use ($base) {
        Http::fake(['*' => Http::response(['refund' => ['id' => 7]])]);
        shopifyRest3RefundMethods()->get(100, 7);
        shopifyRest3RefundSent('GET', "$base/orders/100/refunds/7.json");
    });

    it('calculate -> POST orders/{id}/refunds/calculate.json embrulhado em refund', function () use ($base, $refund) {
        Http::fake(['*' => Http::response(['refund' => ['transactions' => []]])]);
        shopifyRest3RefundMethods()->calculate(100, $refund);
        shopifyRest3RefundSent('POST', "$base/orders/100/refunds/calculate.json", ['refund' => $refund]);
    });

    it('create -> POST orders/{id}/refunds.json embrulhado em refund', function () use ($base, $refund) {
        Http::fake(['*' => Http::response(['refund' => ['id' => 7]], 201)]);
        shopifyRest3RefundMethods()->create(100, $refund);
        shopifyRest3RefundSent('POST', "$base/orders/100/refunds.json", ['refund' => $refund]);
    });
});

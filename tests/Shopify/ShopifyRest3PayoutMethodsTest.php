<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\Endpoints\Payments\PayoutMethods;

function shopifyRest3PayoutMethods(): SistemAtc\Marketplaces\Shopify\Endpoints\Payments\PayoutMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shpat_fake',
        refreshToken: null,
        settings: ['shop_domain' => 'test-store.myshopify.com'],
        active: true,
        expired: false,
    );

    return new SistemAtc\Marketplaces\Shopify\Endpoints\Payments\PayoutMethods(HttpClientFactory::make($integration), $integration);
}

/** Afirma verbo + URL completa + header do token (+ body, quando informado). */
function shopifyRest3PayoutSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify PayoutMethods (REST chunk 3)', function () use ($base) {
    it('list -> GET shopify_payments/payouts.json com filtros', function () use ($base) {
        Http::fake(['*' => Http::response(['payouts' => []])]);
        shopifyRest3PayoutMethods()->list(['status' => 'paid', 'date_min' => '2026-09-01']);
        shopifyRest3PayoutSent('GET', "$base/shopify_payments/payouts.json?status=paid&date_min=2026-09-01");
    });

    it('get -> GET shopify_payments/payouts/{id}.json', function () use ($base) {
        Http::fake(['*' => Http::response(['payout' => ['id' => 9]])]);
        shopifyRest3PayoutMethods()->get(9);
        shopifyRest3PayoutSent('GET', "$base/shopify_payments/payouts/9.json");
    });

    it('balanceTransactions -> GET shopify_payments/balance/transactions.json', function () use ($base) {
        Http::fake(['*' => Http::response(['transactions' => []])]);
        shopifyRest3PayoutMethods()->balanceTransactions(['payout_id' => 9]);
        shopifyRest3PayoutSent('GET', "$base/shopify_payments/balance/transactions.json?payout_id=9");
    });

    it('tenderTransactions -> GET tender_transactions.json', function () use ($base) {
        Http::fake(['*' => Http::response(['tender_transactions' => []])]);
        shopifyRest3PayoutMethods()->tenderTransactions(['processed_at_min' => '2026-09-01T00:00:00-03:00']);
        shopifyRest3PayoutSent('GET', "$base/tender_transactions.json?processed_at_min=2026-09-01T00%3A00%3A00-03%3A00");
    });

    it('each / eachBalanceTransaction / eachTenderTransaction seguem o Link header', function () use ($base) {
        Http::fake(['*' => Http::sequence()
            ->push(['payouts' => [['id' => 1]]], 200, ['Link' => "<$base/shopify_payments/payouts.json?limit=250&page_info=Z2>; rel=\"next\""])
            ->push(['payouts' => [['id' => 2]]], 200, [])
            ->push(['transactions' => [['id' => 3]]], 200, [])
            ->push(['tender_transactions' => [['id' => 4]]], 200, [])]);
        $m = shopifyRest3PayoutMethods();
        expect(array_column(iterator_to_array($m->each()), 'id'))->toBe([1, 2])
            ->and(array_column(iterator_to_array($m->eachBalanceTransaction()), 'id'))->toBe([3])
            ->and(array_column(iterator_to_array($m->eachTenderTransaction()), 'id'))->toBe([4]);
    });
});

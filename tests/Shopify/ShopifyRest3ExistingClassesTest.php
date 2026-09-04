<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\Endpoints\Product\ProductMethods;

function shopifyRest3ExistingMethods(): SistemAtc\Marketplaces\Shopify\Endpoints\Product\ProductMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shpat_fake',
        refreshToken: null,
        settings: ['shop_domain' => 'test-store.myshopify.com'],
        active: true,
        expired: false,
    );

    return new SistemAtc\Marketplaces\Shopify\Endpoints\Product\ProductMethods(HttpClientFactory::make($integration), $integration);
}

/** Afirma verbo + URL completa + header do token (+ body, quando informado). */
function shopifyRest3ExistingSent(string $method, string $url, ?array $body = null): void
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

use SistemAtc\Marketplaces\Shopify\Endpoints\Billing\BillingMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Transaction\TransactionMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Webhooks;

function shopifyRest3ExistingIntegration(): FakeIntegration
{
    return new FakeIntegration(accessToken: 'shpat_fake', refreshToken: null, settings: ['shop_domain' => 'test-store.myshopify.com'], active: true, expired: false);
}

describe('Shopify classes existentes — acrescimos do REST chunk 3', function () use ($base) {
    it('ProductMethods::each segue o Link header', function () use ($base) {
        Http::fake(['*' => Http::sequence()
            ->push(['products' => [['id' => 1]]], 200, ['Link' => "<$base/products.json?limit=250&page_info=PR2>; rel=\"next\""])
            ->push(['products' => [['id' => 2]]], 200, [])]);
        expect(array_column(iterator_to_array(shopifyRest3ExistingMethods()->each(['status' => 'active'])), 'id'))->toBe([1, 2]);
        shopifyRest3ExistingSent('GET', "$base/products.json?status=active&limit=250");
        shopifyRest3ExistingSent('GET', "$base/products.json?limit=250&page_info=PR2");
    });

    it('TransactionMethods::create -> POST orders/{id}/transactions.json embrulhado em transaction', function () use ($base) {
        Http::fake(['*' => Http::response(['transaction' => ['id' => 1]], 201)]);
        $i = shopifyRest3ExistingIntegration();
        (new TransactionMethods(HttpClientFactory::make($i), $i))->create(100, ['kind' => 'capture', 'amount' => '10.00']);
        shopifyRest3ExistingSent('POST', "$base/orders/100/transactions.json", ['transaction' => ['kind' => 'capture', 'amount' => '10.00']]);
    });

    it('BillingMethods: recurring charges list/get/create/delete/customize', function () use ($base) {
        Http::fake(['*' => Http::response(['recurring_application_charges' => [], 'recurring_application_charge' => ['id' => 5]])]);
        $i = shopifyRest3ExistingIntegration();
        $b = new BillingMethods(HttpClientFactory::make($i), $i);
        $b->listRecurringCharges();
        $b->getRecurringCharge(5);
        $b->createRecurringCharge(['name' => 'Plano', 'price' => '10.00', 'return_url' => 'https://x/ok']);
        $b->deleteRecurringCharge(5);
        $b->customizeRecurringCharge(5, '200.00');
        shopifyRest3ExistingSent('GET', "$base/recurring_application_charges.json");
        shopifyRest3ExistingSent('GET', "$base/recurring_application_charges/5.json");
        shopifyRest3ExistingSent('POST', "$base/recurring_application_charges.json", ['recurring_application_charge' => ['name' => 'Plano', 'price' => '10.00', 'return_url' => 'https://x/ok']]);
        shopifyRest3ExistingSent('DELETE', "$base/recurring_application_charges/5.json");
        shopifyRest3ExistingSent('PUT', "$base/recurring_application_charges/5/customize.json?recurring_application_charge%5Bcapped_amount%5D=200.00");
    });

    it('BillingMethods: usage charges list/get/create', function () use ($base) {
        Http::fake(['*' => Http::response(['usage_charges' => [], 'usage_charge' => ['id' => 6]])]);
        $i = shopifyRest3ExistingIntegration();
        $b = new BillingMethods(HttpClientFactory::make($i), $i);
        $b->listUsageCharges(5);
        $b->getUsageCharge(5, 6);
        $b->createUsageCharge(5, ['description' => 'uso', 'price' => '1.00']);
        shopifyRest3ExistingSent('GET', "$base/recurring_application_charges/5/usage_charges.json");
        shopifyRest3ExistingSent('GET', "$base/recurring_application_charges/5/usage_charges/6.json");
        shopifyRest3ExistingSent('POST', "$base/recurring_application_charges/5/usage_charges.json", ['usage_charge' => ['description' => 'uso', 'price' => '1.00']]);
    });

    it('Webhooks: count/get/update', function () use ($base) {
        Http::fake(['*' => Http::response(['count' => 2, 'webhook' => ['id' => 8]])]);
        $i = shopifyRest3ExistingIntegration();
        $w = new Webhooks(HttpClientFactory::make($i), $i);
        expect($w->count(['topic' => 'orders/create']))->toBe(2);
        $w->get(8);
        $w->update(8, ['address' => 'https://x/hook']);
        shopifyRest3ExistingSent('GET', "$base/webhooks/count.json?topic=orders%2Fcreate");
        shopifyRest3ExistingSent('GET', "$base/webhooks/8.json");
        shopifyRest3ExistingSent('PUT', "$base/webhooks/8.json", ['webhook' => ['address' => 'https://x/hook']]);
    });
});

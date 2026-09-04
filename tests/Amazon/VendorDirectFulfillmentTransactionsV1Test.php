<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentTransactionsV1;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function vendorDfTransactionsV1Integration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: [
            'client_id' => 'test-client',
            'client_secret' => 'test-secret',
            'marketplace_id' => 'A2Q3Y263D00KWC',
        ],
        active: true,
        expired: false,
    );
}

function vendorDfTransactionsV1Endpoint(): VendorDirectFulfillmentTransactionsV1
{
    return new VendorDirectFulfillmentTransactionsV1(new Client(vendorDfTransactionsV1Integration()));
}

function vendorDfTransactionsV1AssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const VENDOR_DIRECT_FULFILLMENT_TRANSACTIONS_V1_BASE = 'https://sellingpartnerapi-na.amazon.com/vendor/directFulfillment/transactions/v1';

it('getTransactionStatus GETs /transactions/TX%201', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_TRANSACTIONS_V1_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfTransactionsV1Endpoint()->getTransactionStatus('TX 1');

    expect($resp['ok'])->toBeTrue();
    vendorDfTransactionsV1AssertSent('GET', VENDOR_DIRECT_FULFILLMENT_TRANSACTIONS_V1_BASE.'/transactions/TX%201');
});

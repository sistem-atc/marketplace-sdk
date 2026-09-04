<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentTransactions;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function vendorDfTransactionsIntegration(): FakeIntegration
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

function vendorDfTransactionsEndpoint(): VendorDirectFulfillmentTransactions
{
    return new VendorDirectFulfillmentTransactions(new Client(vendorDfTransactionsIntegration()));
}

function vendorDfTransactionsAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const VENDOR_DIRECT_FULFILLMENT_TRANSACTIONS_BASE = 'https://sellingpartnerapi-na.amazon.com/vendor/directFulfillment/transactions/2021-12-28';

it('getTransactionStatus GETs /transactions/TX-1', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_TRANSACTIONS_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfTransactionsEndpoint()->getTransactionStatus('TX-1');

    expect($resp['ok'])->toBeTrue();
    vendorDfTransactionsAssertSent('GET', VENDOR_DIRECT_FULFILLMENT_TRANSACTIONS_BASE.'/transactions/TX-1');
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\VendorTransactionStatus;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function vendorTransactionStatusIntegration(): FakeIntegration
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

function vendorTransactionStatusEndpoint(): VendorTransactionStatus
{
    return new VendorTransactionStatus(new Client(vendorTransactionStatusIntegration()));
}

function vendorTransactionStatusAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const VENDOR_TRANSACTION_STATUS_BASE = 'https://sellingpartnerapi-na.amazon.com/vendor/transactions/v1';

it('getTransaction GETs /transactions/20190905T03001Z-abc%201', function () {
    Http::fake([VENDOR_TRANSACTION_STATUS_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorTransactionStatusEndpoint()->getTransaction('20190905T03001Z-abc 1');

    expect($resp['ok'])->toBeTrue();
    vendorTransactionStatusAssertSent('GET', VENDOR_TRANSACTION_STATUS_BASE.'/transactions/20190905T03001Z-abc%201');
});

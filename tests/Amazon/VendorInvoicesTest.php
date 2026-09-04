<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\VendorInvoices;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function vendorInvoicesIntegration(): FakeIntegration
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

function vendorInvoicesEndpoint(): VendorInvoices
{
    return new VendorInvoices(new Client(vendorInvoicesIntegration()));
}

function vendorInvoicesAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const VENDOR_INVOICES_BASE = 'https://sellingpartnerapi-na.amazon.com/vendor/payments/v1';

it('submitInvoices POSTs /invoices with the body', function () {
    Http::fake([VENDOR_INVOICES_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorInvoicesEndpoint()->submitInvoices($body);

    expect($resp['ok'])->toBeTrue();
    vendorInvoicesAssertSent('POST', VENDOR_INVOICES_BASE.'/invoices', $body);
});

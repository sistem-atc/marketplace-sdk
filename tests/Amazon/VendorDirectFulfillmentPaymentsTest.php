<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentPayments;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function vendorDfPaymentsIntegration(): FakeIntegration
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

function vendorDfPaymentsEndpoint(): VendorDirectFulfillmentPayments
{
    return new VendorDirectFulfillmentPayments(new Client(vendorDfPaymentsIntegration()));
}

function vendorDfPaymentsAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const VENDOR_DIRECT_FULFILLMENT_PAYMENTS_BASE = 'https://sellingpartnerapi-na.amazon.com/vendor/directFulfillment/payments/v1';

it('submitInvoice POSTs /invoices with the body', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_PAYMENTS_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorDfPaymentsEndpoint()->submitInvoice($body);

    expect($resp['ok'])->toBeTrue();
    vendorDfPaymentsAssertSent('POST', VENDOR_DIRECT_FULFILLMENT_PAYMENTS_BASE.'/invoices', $body);
});

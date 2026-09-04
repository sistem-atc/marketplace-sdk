<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\VendorDirectFulfillmentSandbox;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function vendorDfSandboxIntegration(): FakeIntegration
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

function vendorDfSandboxEndpoint(): VendorDirectFulfillmentSandbox
{
    return new VendorDirectFulfillmentSandbox(new Client(vendorDfSandboxIntegration()));
}

function vendorDfSandboxAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const VENDOR_DIRECT_FULFILLMENT_SANDBOX_BASE = 'https://sellingpartnerapi-na.amazon.com/vendor/directFulfillment/sandbox/2021-10-28';

it('generateOrderScenarios POSTs /orders with the body', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SANDBOX_BASE.'/*' => Http::response(['ok' => true], 200)]);
    $body = ['k' => 'v', 'n' => 1];

    $resp = vendorDfSandboxEndpoint()->generateOrderScenarios($body);

    expect($resp['ok'])->toBeTrue();
    vendorDfSandboxAssertSent('POST', VENDOR_DIRECT_FULFILLMENT_SANDBOX_BASE.'/orders', $body);
});

it('getOrderScenarios GETs /transactions/TX%201', function () {
    Http::fake([VENDOR_DIRECT_FULFILLMENT_SANDBOX_BASE.'/*' => Http::response(['ok' => true], 200)]);

    $resp = vendorDfSandboxEndpoint()->getOrderScenarios('TX 1');

    expect($resp['ok'])->toBeTrue();
    vendorDfSandboxAssertSent('GET', VENDOR_DIRECT_FULFILLMENT_SANDBOX_BASE.'/transactions/TX%201');
});

<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\ExternalFulfillment;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function amazonExternalFulfillmentIntegration(): FakeIntegration
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

function amazonExternalFulfillmentEndpoint(): ExternalFulfillment
{
    return new ExternalFulfillment(new Client(amazonExternalFulfillmentIntegration()));
}

/**
 * Fake generico + assert de verbo, URL completa, header x-amz-access-token e
 * (quando houver) chaves do body JSON.
 */
function amazonExternalFulfillmentAssertSent(string $method, string $url, array $bodyContains = []): void
{
    Http::assertSent(function (Request $request) use ($method, $url, $bodyContains): bool {
        if ($request->method() !== $method || $request->url() !== $url) {
            return false;
        }
        if ($request->header('x-amz-access-token')[0] !== 'Atza|valid-token') {
            return false;
        }
        foreach ($bodyContains as $key => $value) {
            if (data_get($request->data(), $key) !== $value) {
                return false;
            }
        }

        return true;
    });
}

it('getShipments → GET /externalFulfillment/2024-09-11/shipments?status=ACCEPTED&locationId=LOC-1&maxResults=50', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonExternalFulfillmentEndpoint()->getShipments('ACCEPTED', ['locationId' => 'LOC-1', 'maxResults' => 50]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonExternalFulfillmentAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/externalFulfillment/2024-09-11/shipments?status=ACCEPTED&locationId=LOC-1&maxResults=50');
});

it('getShipment → GET /externalFulfillment/2024-09-11/shipments/SHP-1', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonExternalFulfillmentEndpoint()->getShipment('SHP-1');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonExternalFulfillmentAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/externalFulfillment/2024-09-11/shipments/SHP-1');
});

it('processShipment → POST /externalFulfillment/2024-09-11/shipments/SHP-1?operation=REJECT', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonExternalFulfillmentEndpoint()->processShipment('SHP-1', 'REJECT', ['lineItems' => []]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonExternalFulfillmentAssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/externalFulfillment/2024-09-11/shipments/SHP-1?operation=REJECT', ['lineItems' => []]);
});

it('createPackages → POST /externalFulfillment/2024-09-11/shipments/SHP-1/packages', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonExternalFulfillmentEndpoint()->createPackages('SHP-1', ['packages' => [['id' => 'PKG-1']]]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonExternalFulfillmentAssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/externalFulfillment/2024-09-11/shipments/SHP-1/packages', ['packages.0.id' => 'PKG-1']);
});

it('updatePackage → PUT /externalFulfillment/2024-09-11/shipments/SHP-1/packages/PKG-1', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonExternalFulfillmentEndpoint()->updatePackage('SHP-1', 'PKG-1', ['weight' => ['value' => 1]]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonExternalFulfillmentAssertSent('PUT', 'https://sellingpartnerapi-na.amazon.com/externalFulfillment/2024-09-11/shipments/SHP-1/packages/PKG-1', ['weight.value' => 1]);
});

it('updatePackageStatus → PATCH /externalFulfillment/2024-09-11/shipments/SHP-1/packages/PKG-1?status=SHIPPED', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonExternalFulfillmentEndpoint()->updatePackageStatus('SHP-1', 'PKG-1', ['status' => 'SHIPPED'], ['trackingDetails' => ['trackingId' => 'TRK-1']]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonExternalFulfillmentAssertSent('PATCH', 'https://sellingpartnerapi-na.amazon.com/externalFulfillment/2024-09-11/shipments/SHP-1/packages/PKG-1?status=SHIPPED', ['trackingDetails.trackingId' => 'TRK-1']);
});

it('retrieveShippingOptions → GET /externalFulfillment/2024-09-11/shipments/SHP-1/shippingOptions?packageId=PKG-1', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonExternalFulfillmentEndpoint()->retrieveShippingOptions('SHP-1', 'PKG-1');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonExternalFulfillmentAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/externalFulfillment/2024-09-11/shipments/SHP-1/shippingOptions?packageId=PKG-1');
});

it('generateInvoice → POST /externalFulfillment/2024-09-11/shipments/SHP-1/invoice', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonExternalFulfillmentEndpoint()->generateInvoice('SHP-1');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonExternalFulfillmentAssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/externalFulfillment/2024-09-11/shipments/SHP-1/invoice');
});

it('retrieveInvoice → GET /externalFulfillment/2024-09-11/shipments/SHP-1/invoice', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonExternalFulfillmentEndpoint()->retrieveInvoice('SHP-1');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonExternalFulfillmentAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/externalFulfillment/2024-09-11/shipments/SHP-1/invoice');
});

it('generateShipLabels → PUT /externalFulfillment/2024-09-11/shipments/SHP-1/shipLabels?operation=GENERATE&shippingOptionId=OPT-1', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonExternalFulfillmentEndpoint()->generateShipLabels('SHP-1', 'GENERATE', ['shippingOptionId' => 'OPT-1'], ['packageIds' => ['PKG-1']]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonExternalFulfillmentAssertSent('PUT', 'https://sellingpartnerapi-na.amazon.com/externalFulfillment/2024-09-11/shipments/SHP-1/shipLabels?operation=GENERATE&shippingOptionId=OPT-1', ['packageIds.0' => 'PKG-1']);
});

it('listReturns → GET /externalFulfillment/2024-09-11/returns?status=CREATED&maxResults=20', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonExternalFulfillmentEndpoint()->listReturns(['status' => 'CREATED', 'maxResults' => 20]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonExternalFulfillmentAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/externalFulfillment/2024-09-11/returns?status=CREATED&maxResults=20');
});

it('getReturn → GET /externalFulfillment/2024-09-11/returns/RET-1', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonExternalFulfillmentEndpoint()->getReturn('RET-1');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonExternalFulfillmentAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/externalFulfillment/2024-09-11/returns/RET-1');
});

it('batchInventory → POST /externalFulfillment/inventory/2024-09-11/inventories', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonExternalFulfillmentEndpoint()->batchInventory(['requests' => [['skuId' => 'SKU-1', 'quantity' => 5]]]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonExternalFulfillmentAssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/externalFulfillment/inventory/2024-09-11/inventories', ['requests.0.skuId' => 'SKU-1']);
});

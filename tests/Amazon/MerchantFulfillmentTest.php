<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\MerchantFulfillment;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function amazonMerchantFulfillmentIntegration(): FakeIntegration
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

function amazonMerchantFulfillmentEndpoint(): MerchantFulfillment
{
    return new MerchantFulfillment(new Client(amazonMerchantFulfillmentIntegration()));
}

/**
 * Fake generico + assert de verbo, URL completa, header x-amz-access-token e
 * (quando houver) chaves do body JSON.
 */
function amazonMerchantFulfillmentAssertSent(string $method, string $url, array $bodyContains = []): void
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

it('getEligibleShipmentServices → POST /mfn/v0/eligibleShippingServices', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonMerchantFulfillmentEndpoint()->getEligibleShipmentServices(['ShipmentRequestDetails' => ['AmazonOrderId' => '701-1']]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonMerchantFulfillmentAssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/mfn/v0/eligibleShippingServices', ['ShipmentRequestDetails.AmazonOrderId' => '701-1']);
});

it('getShipment → GET /mfn/v0/shipments/SHP-1', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonMerchantFulfillmentEndpoint()->getShipment('SHP-1');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonMerchantFulfillmentAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/mfn/v0/shipments/SHP-1');
});

it('cancelShipment → DELETE /mfn/v0/shipments/SHP-1', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonMerchantFulfillmentEndpoint()->cancelShipment('SHP-1');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonMerchantFulfillmentAssertSent('DELETE', 'https://sellingpartnerapi-na.amazon.com/mfn/v0/shipments/SHP-1');
});

it('createShipment → POST /mfn/v0/shipments', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonMerchantFulfillmentEndpoint()->createShipment(['ShippingServiceId' => 'SVC-1']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonMerchantFulfillmentAssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/mfn/v0/shipments', ['ShippingServiceId' => 'SVC-1']);
});

it('getAdditionalSellerInputs → POST /mfn/v0/additionalSellerInputs', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonMerchantFulfillmentEndpoint()->getAdditionalSellerInputs(['ShippingServiceId' => 'SVC-1', 'OrderId' => '701-1']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonMerchantFulfillmentAssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/mfn/v0/additionalSellerInputs', ['OrderId' => '701-1']);
});

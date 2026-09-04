<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\ShippingV1;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function amazonShippingV1Integration(): FakeIntegration
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

function amazonShippingV1Endpoint(): ShippingV1
{
    return new ShippingV1(new Client(amazonShippingV1Integration()));
}

/**
 * Fake generico + assert de verbo, URL completa, header x-amz-access-token e
 * (quando houver) chaves do body JSON.
 */
function amazonShippingV1AssertSent(string $method, string $url, array $bodyContains = []): void
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

it('createShipment → POST /shipping/v1/shipments', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV1Endpoint()->createShipment(['clientReferenceId' => 'REF-1']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV1AssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/shipping/v1/shipments', ['clientReferenceId' => 'REF-1']);
});

it('getShipment → GET /shipping/v1/shipments/SHP-1', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV1Endpoint()->getShipment('SHP-1');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV1AssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/shipping/v1/shipments/SHP-1');
});

it('cancelShipment → POST /shipping/v1/shipments/SHP-1/cancel', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV1Endpoint()->cancelShipment('SHP-1');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV1AssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/shipping/v1/shipments/SHP-1/cancel');
});

it('purchaseLabels → POST /shipping/v1/shipments/SHP-1/purchaseLabels', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV1Endpoint()->purchaseLabels('SHP-1', ['rateId' => 'R1']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV1AssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/shipping/v1/shipments/SHP-1/purchaseLabels', ['rateId' => 'R1']);
});

it('retrieveShippingLabel → POST /shipping/v1/shipments/SHP-1/containers/TRK-1/label', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV1Endpoint()->retrieveShippingLabel('SHP-1', 'TRK-1', ['labelSpecification' => ['labelFormat' => 'PNG']]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV1AssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/shipping/v1/shipments/SHP-1/containers/TRK-1/label', ['labelSpecification.labelFormat' => 'PNG']);
});

it('purchaseShipment → POST /shipping/v1/purchaseShipment', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV1Endpoint()->purchaseShipment(['serviceType' => 'Amazon Shipping Standard']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV1AssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/shipping/v1/purchaseShipment', ['serviceType' => 'Amazon Shipping Standard']);
});

it('getRates → POST /shipping/v1/rates', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV1Endpoint()->getRates(['serviceTypes' => ['Amazon Shipping Standard']]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV1AssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/shipping/v1/rates', ['serviceTypes.0' => 'Amazon Shipping Standard']);
});

it('getAccount → GET /shipping/v1/account', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV1Endpoint()->getAccount();

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV1AssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/shipping/v1/account');
});

it('getTrackingInformation → GET /shipping/v1/tracking/TRK%201', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV1Endpoint()->getTrackingInformation('TRK 1');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV1AssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/shipping/v1/tracking/TRK%201');
});

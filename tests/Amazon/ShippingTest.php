<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Shipping;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function amazonShippingV2Integration(): FakeIntegration
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

function amazonShippingV2Endpoint(): Shipping
{
    return new Shipping(new Client(amazonShippingV2Integration()));
}

/**
 * Fake generico + assert de verbo, URL completa, header x-amz-access-token e
 * (quando houver) chaves do body JSON.
 */
function amazonShippingV2AssertSent(string $method, string $url, array $bodyContains = []): void
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

it('getRates → POST /shipping/v2/shipments/rates', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->getRates(['shipTo' => ['name' => 'X']]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/shipments/rates', ['shipTo.name' => 'X']);
});

it('directPurchaseShipment → POST /shipping/v2/shipments/directPurchase', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->directPurchaseShipment(['shipTo' => ['name' => 'X']]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/shipments/directPurchase', ['shipTo.name' => 'X']);
});

it('purchaseShipment → POST /shipping/v2/shipments', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->purchaseShipment(['requestToken' => 'T', 'rateId' => 'R']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/shipments', ['requestToken' => 'T', 'rateId' => 'R']);
});

it('oneClickShipment → POST /shipping/v2/oneClickShipment', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->oneClickShipment(['shipTo' => ['name' => 'X']]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/oneClickShipment', ['shipTo.name' => 'X']);
});

it('getTracking → GET /shipping/v2/tracking?trackingId=TRK-1&carrierId=AMZN_UK', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->getTracking('TRK-1', 'AMZN_UK');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/tracking?trackingId=TRK-1&carrierId=AMZN_UK');
});

it('getShipmentDocuments → GET /shipping/v2/shipments/SHP-1/documents?packageClientReferenceId=PKG-1&format=PDF&dpi=300', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->getShipmentDocuments('SHP-1', 'PKG-1', ['format' => 'PDF', 'dpi' => 300]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/shipments/SHP-1/documents?packageClientReferenceId=PKG-1&format=PDF&dpi=300');
});

it('cancelShipment → PUT /shipping/v2/shipments/SHP-1/cancel', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->cancelShipment('SHP-1');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('PUT', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/shipments/SHP-1/cancel');
});

it('getAdditionalInputs → GET /shipping/v2/shipments/additionalInputs/schema?requestToken=T&rateId=R', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->getAdditionalInputs('T', 'R');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/shipments/additionalInputs/schema?requestToken=T&rateId=R');
});

it('getCarrierAccountFormInputs → GET /shipping/v2/carrierAccountFormInputs', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->getCarrierAccountFormInputs();

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/carrierAccountFormInputs');
});

it('getCarrierAccounts → PUT /shipping/v2/carrierAccounts', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->getCarrierAccounts(['clientReferenceDetails' => []]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('PUT', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/carrierAccounts');
});

it('linkCarrierAccount → PUT /shipping/v2/carrierAccounts/UPS', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->linkCarrierAccount('UPS', ['clientReferenceDetails' => [], 'carrierAccountType' => 'X']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('PUT', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/carrierAccounts/UPS', ['carrierAccountType' => 'X']);
});

it('linkCarrierAccountPost → POST /shipping/v2/carrierAccounts/UPS', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->linkCarrierAccountPost('UPS', ['carrierAccountType' => 'X']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/carrierAccounts/UPS', ['carrierAccountType' => 'X']);
});

it('unlinkCarrierAccount → PUT /shipping/v2/carrierAccounts/UPS/unlink', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->unlinkCarrierAccount('UPS', ['clientReferenceDetails' => []]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('PUT', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/carrierAccounts/UPS/unlink');
});

it('generateCollectionForm → POST /shipping/v2/collectionForms', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->generateCollectionForm(['carrierId' => 'UPS']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/collectionForms', ['carrierId' => 'UPS']);
});

it('getCollectionFormHistory → PUT /shipping/v2/collectionForms/history', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->getCollectionFormHistory(['maxResults' => 10]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('PUT', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/collectionForms/history', ['maxResults' => 10]);
});

it('getUnmanifestedShipments → PUT /shipping/v2/unmanifestedShipments', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->getUnmanifestedShipments(['clientReferenceDetails' => []]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('PUT', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/unmanifestedShipments');
});

it('getCollectionForm → GET /shipping/v2/collectionForms/CF-1', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->getCollectionForm('CF-1');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/collectionForms/CF-1');
});

it('getAccessPoints → GET /shipping/v2/accessPoints?accessPointTypes=HELIX%2CCOUNTER&countryCode=BR&postalCode=01310-100', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->getAccessPoints(['HELIX', 'COUNTER'], 'BR', '01310-100');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/accessPoints?accessPointTypes=HELIX%2CCOUNTER&countryCode=BR&postalCode=01310-100');
});

it('submitNdrFeedback → POST /shipping/v2/ndrFeedback', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->submitNdrFeedback(['trackingId' => 'TRK-1']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/ndrFeedback', ['trackingId' => 'TRK-1']);
});

it('createClaim → POST /shipping/v2/claims', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonShippingV2Endpoint()->createClaim(['trackingId' => 'TRK-1', 'claimType' => 'LOST']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonShippingV2AssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/shipping/v2/claims', ['claimType' => 'LOST']);
});

it('withBusinessId envia x-amzn-shipping-business-id em todas as chamadas e não altera a instância original', function () {
    Http::fake(['*' => Http::response(['payload' => []], 200)]);

    $plain = amazonShippingV2Endpoint();
    $us = $plain->withBusinessId('AmazonShipping_US');

    expect($us)->not->toBe($plain);

    $us->getCarrierAccountFormInputs();
    $us->getRates(['shipTo' => []]);
    $us->cancelShipment('SHIP-1');
    $plain->getCarrierAccountFormInputs();

    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && str_ends_with($r->url(), '/shipping/v2/carrierAccountFormInputs')
        && $r->hasHeader('x-amzn-shipping-business-id', 'AmazonShipping_US'));
    Http::assertSent(fn (Request $r) => $r->method() === 'POST'
        && str_ends_with($r->url(), '/shipping/v2/shipments/rates')
        && $r->hasHeader('x-amzn-shipping-business-id', 'AmazonShipping_US'));
    Http::assertSent(fn (Request $r) => $r->method() === 'PUT'
        && str_ends_with($r->url(), '/shipping/v2/shipments/SHIP-1/cancel')
        && $r->hasHeader('x-amzn-shipping-business-id', 'AmazonShipping_US'));
    // instância original: sem o header
    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && str_ends_with($r->url(), '/shipping/v2/carrierAccountFormInputs')
        && ! $r->hasHeader('x-amzn-shipping-business-id'));
});

it('purchaseShipment / directPurchaseShipment / generateCollectionForm enviam x-amzn-IdempotencyKey quando informado', function () {
    Http::fake(['*' => Http::response(['payload' => []], 200)]);

    $ep = amazonShippingV2Endpoint()->withBusinessId('AmazonShipping_UK');
    $ep->purchaseShipment(['rateId' => 'r1'], idempotencyKey: 'key-1');
    $ep->directPurchaseShipment(['shipTo' => []], idempotencyKey: 'key-2');
    $ep->generateCollectionForm(['carrierId' => 'c1'], idempotencyKey: 'key-3');

    Http::assertSent(fn (Request $r) => $r->url() === 'https://sellingpartnerapi-na.amazon.com/shipping/v2/shipments'
        && $r->hasHeader('x-amzn-IdempotencyKey', 'key-1')
        && $r->hasHeader('x-amzn-shipping-business-id', 'AmazonShipping_UK'));
    Http::assertSent(fn (Request $r) => $r->url() === 'https://sellingpartnerapi-na.amazon.com/shipping/v2/shipments/directPurchase'
        && $r->hasHeader('x-amzn-IdempotencyKey', 'key-2'));
    Http::assertSent(fn (Request $r) => $r->url() === 'https://sellingpartnerapi-na.amazon.com/shipping/v2/collectionForms'
        && $r->hasHeader('x-amzn-IdempotencyKey', 'key-3'));
});

it('sem withBusinessId nem idempotencyKey, nenhum dos headers opcionais é enviado', function () {
    Http::fake(['*' => Http::response(['payload' => []], 200)]);

    amazonShippingV2Endpoint()->purchaseShipment(['rateId' => 'r1']);

    Http::assertSent(fn (Request $r) => $r->url() === 'https://sellingpartnerapi-na.amazon.com/shipping/v2/shipments'
        && ! $r->hasHeader('x-amzn-IdempotencyKey')
        && ! $r->hasHeader('x-amzn-shipping-business-id'));
});

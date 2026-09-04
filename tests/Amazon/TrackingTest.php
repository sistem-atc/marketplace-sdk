<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Tracking;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function amazonTrackingIntegration(): FakeIntegration
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

function amazonTrackingEndpoint(): Tracking
{
    return new Tracking(new Client(amazonTrackingIntegration()));
}

/**
 * Fake generico + assert de verbo, URL completa, header x-amz-access-token e
 * (quando houver) chaves do body JSON.
 */
function amazonTrackingAssertSent(string $method, string $url, array $bodyContains = []): void
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

it('getShipmentTracking → GET /tracking/2026-01-30/shipments/track?carrierTracking.trackingNumber=TRK-1&carrierTracking.carrierCode=AMZN', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonTrackingEndpoint()->getShipmentTracking(['carrierTracking.trackingNumber' => 'TRK-1', 'carrierTracking.carrierCode' => 'AMZN']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonTrackingAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/tracking/2026-01-30/shipments/track?carrierTracking.trackingNumber=TRK-1&carrierTracking.carrierCode=AMZN');
});

it('getShipmentTracking envia Accept-Language quando informado', function () {
    Http::fake(['*' => Http::response(['ok' => true], 200)]);

    amazonTrackingEndpoint()->getShipmentTracking(['id' => 'SHP-1'], acceptLanguage: 'pt-BR');

    Http::assertSent(fn (Request $r) => $r->url() === 'https://sellingpartnerapi-na.amazon.com/tracking/2026-01-30/shipments/track?id=SHP-1'
        && $r->hasHeader('Accept-Language', 'pt-BR'));
});

it('getShipmentTracking não envia Accept-Language por padrão', function () {
    Http::fake(['*' => Http::response(['ok' => true], 200)]);

    amazonTrackingEndpoint()->getShipmentTracking(['id' => 'SHP-1']);

    Http::assertSent(fn (Request $r) => $r->url() === 'https://sellingpartnerapi-na.amazon.com/tracking/2026-01-30/shipments/track?id=SHP-1'
        && ! $r->hasHeader('Accept-Language'));
});

<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Vehicles;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function vehiclesEndpoint(): Vehicles
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new Vehicles(new Client($integration));
}

it('getVehicles GET /catalog/2024-11-01/automotive/vehicles com marketplaceId + vehicleType + pageToken', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/catalog/2024-11-01/automotive/vehicles*' => Http::response([
            'vehicles' => [['identifiers' => [], 'vehicleType' => 'CAR']],
            'pagination' => ['nextToken' => 'N2'],
        ]),
    ]);

    $resp = vehiclesEndpoint()->getVehicles('A2Q3Y263D00KWC', 'CAR', ['pageToken' => 'N1', 'updatedAfter' => '2026-01-01T00:00:00Z']);
    expect($resp['pagination']['nextToken'])->toBe('N2');

    $expected = 'https://sellingpartnerapi-na.amazon.com/catalog/2024-11-01/automotive/vehicles?'
        .http_build_query(['marketplaceId' => 'A2Q3Y263D00KWC', 'vehicleType' => 'CAR', 'pageToken' => 'N1', 'updatedAfter' => '2026-01-01T00:00:00Z']);

    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === $expected
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});

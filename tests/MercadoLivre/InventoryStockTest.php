<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function mlInventoryIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config(['marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com']);
    Http::preventStrayRequests();
});

it('fulfillmentStock usa /inventories/{id}/stock/fulfillment', function () {
    Http::fake([
        'api.mercadolibre.com/inventories/*/stock/fulfillment' => Http::response([
            'inventory_id' => 'LCQI05831',
            'total' => 60,
            'available_quantity' => 59,
            'not_available_quantity' => 1,
            'not_available_detail' => [
                ['status' => 'transfer', 'quantity' => 1],
            ],
        ], 200),
    ]);

    $resp = MarketPlaces::MercadoLivre()->inventory(mlInventoryIntegration())
        ->fulfillmentStock('LCQI05831');

    expect($resp['available_quantity'])->toBe(59)
        ->and($resp['total'])->toBe(60)
        ->and($resp['inventory_id'])->toBe('LCQI05831');

    Http::assertSent(function ($req) {
        return str_contains($req->url(), '/inventories/LCQI05831/stock/fulfillment');
    });
});

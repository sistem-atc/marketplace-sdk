<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function mlItemIntegration(): FakeIntegration
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

it('multiGet usa /items?ids=...&attributes=... (max 20 ids)', function () {
    Http::fake([
        'api.mercadolibre.com/items*' => Http::response([
            ['code' => 200, 'body' => ['id' => 'MLB1', 'seller_custom_field' => '14']],
            ['code' => 200, 'body' => ['id' => 'MLB2', 'seller_custom_field' => '15']],
        ], 200),
    ]);

    $resp = MarketPlaces::MercadoLivre()->items(mlItemIntegration())
        ->multiGet(['MLB1', 'MLB2'], ['id', 'seller_custom_field']);

    expect($resp)->toHaveCount(2)
        ->and($resp[0]->body?->sellerCustomField)->toBe('14');

    Http::assertSent(function ($req) {
        return str_contains($req->url(), '/items?')
            && str_contains($req->url(), 'ids=MLB1%2CMLB2')
            && str_contains($req->url(), 'attributes=id%2Cseller_custom_field');
    });
});

it('multiGet retorna [] sem ids', function () {
    expect(MarketPlaces::MercadoLivre()->items(mlItemIntegration())->multiGet([]))->toBe([]);
});

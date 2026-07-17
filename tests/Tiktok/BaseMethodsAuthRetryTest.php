<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tiktok\Exceptions\TiktokRequestException;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttApiIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'OLD-ACCESS',
        refreshToken: 'RT-CURRENT',
        settings: [
            'app_key' => 'ak',
            'app_secret' => 'as',
            'shop_id' => 'shop1',
            'shop_cipher' => 'cipher1',
        ],
        active: true,
        expired: false, // expires_at "valido" — token morto server-side
    );
}

it('refreshes and retries once on 105002 expired credentials, then succeeds', function () {
    $i = ttApiIntegration();

    $sequence = Http::sequence()
        // 1a chamada do Open API -> 105002 (token rejeitado server-side)
        ->push(['code' => 105002, 'message' => 'Expired credentials'], 200)
        // 2a chamada (apos forceRefresh) -> sucesso
        ->push(['code' => 0, 'message' => 'success', 'data' => ['orders' => [], 'next_page_token' => '']], 200);

    Http::fake([
        // Auth API: refresh devolve token novo
        'auth.tiktok-shops.com/*' => Http::response([
            'code' => 0,
            'data' => [
                'access_token' => 'NEW-ACCESS',
                'access_token_expire_in' => now()->addDay()->timestamp,
                'refresh_token' => 'RT-ROTATED',
                'refresh_token_expire_in' => now()->addDays(30)->timestamp,
            ],
        ], 200),
        'open-api.tiktokglobalshop.com/*' => $sequence,
    ]);

    $result = MarketPlaces::Tiktok()->orders($i)->getOrderList(
        timeGe: now()->subHour()->timestamp,
        timeLt: now()->timestamp,
        sortField: 'update_time',
    );

    expect($result->orders)->toBe([]);

    // Token foi rotacionado e a 2a chamada usou o token novo no header.
    expect($i->getAccessToken())->toBe('NEW-ACCESS');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'auth.tiktok-shops.com');
    });
});

it('does NOT retry on non-auth logical errors (throws immediately)', function () {
    $i = ttApiIntegration();

    Http::fake([
        'auth.tiktok-shops.com/*' => Http::response(['code' => 0, 'data' => [
            'access_token' => 'NEW', 'access_token_expire_in' => now()->addDay()->timestamp,
            'refresh_token' => 'RT2', 'refresh_token_expire_in' => now()->addDays(30)->timestamp,
        ]], 200),
        // erro de negocio nao-auth -> sem retry, estoura
        'open-api.tiktokglobalshop.com/*' => Http::response(['code' => 12000001, 'message' => 'order error'], 200),
    ]);

    expect(fn () => MarketPlaces::Tiktok()->orders($i)->getOrderList(
        timeGe: now()->subHour()->timestamp,
        timeLt: now()->timestamp,
    ))->toThrow(TiktokRequestException::class);
});

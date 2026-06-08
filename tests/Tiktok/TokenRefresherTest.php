<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tiktok\Exceptions\TiktokAuthenticationException;
use SistemAtc\Marketplaces\Tiktok\Support\TokenRefresher;

function ttIntegrationWithCreds(array $over = []): FakeIntegration
{
    return new FakeIntegration(
        accessToken: $over['access_token'] ?? 'OLD-ACCESS',
        refreshToken: $over['refresh_token'] ?? 'RT-CURRENT',
        settings: $over['settings'] ?? [
            'app_key' => 'ak',
            'app_secret' => 'as',
            'shop_id' => 'shop1',
            'shop_cipher' => 'cipher1',
        ],
        active: $over['is_active'] ?? true,
        expired: $over['expired'] ?? false,
    );
}

it('refresh() short-circuits when token is still valid', function () {
    Http::fake(); // se chamar a API, falha o assert abaixo
    $i = ttIntegrationWithCreds(['expired' => false]);

    $token = TokenRefresher::refresh($i);

    expect($token)->toBe('OLD-ACCESS');
    Http::assertNothingSent();
});

it('forceRefresh() hits the Auth API even when expires_at says valid', function () {
    $i = ttIntegrationWithCreds(['expired' => false]);

    $newEpoch = now()->addDay()->getTimestamp();
    Http::fake([
        'auth.tiktok-shops.com/*' => Http::response([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'access_token' => 'NEW-ACCESS',
                'access_token_expire_in' => $newEpoch,
                'refresh_token' => 'RT-ROTATED',
                'refresh_token_expire_in' => now()->addDays(30)->timestamp,
            ],
        ], 200),
    ]);

    $token = TokenRefresher::forceRefresh($i);

    expect($token)->toBe('NEW-ACCESS');
    Http::assertSentCount(1);

    expect($i->getAccessToken())->toBe('NEW-ACCESS')
        ->and($i->getRefreshToken())->toBe('RT-ROTATED');

    // access_token_expire_in e' EPOCH UNIX; o host faz round-trip exato via
    // cast datetime. No FakeIntegration assertamos o TTL passado ao
    // updateTokens (epoch - now), que deve ser positivo (~86400s).
    expect($i->lastExpiresIn)->toBeGreaterThan(0);
});

it('forceRefresh() reuses a token already rotated by another worker (no API hit)', function () {
    Http::fake();

    // Cenario de corrida: o objeto em memoria carrega o token rejeitado
    // ('OLD-ACCESS'), mas outra thread JA' rotacionou pra 'ALREADY-NEW'.
    // Ao recarregar via refresh() dentro do lock, forceRefresh deve detectar
    // a divergencia e reaproveitar sem bater no Auth API.
    $i = ttIntegrationWithCreds([
        'access_token' => 'OLD-ACCESS',
        'expired' => false,
    ]);

    $i->simulateExternalRotation('ALREADY-NEW');

    $token = TokenRefresher::forceRefresh($i);

    expect($token)->toBe('ALREADY-NEW');
    Http::assertNothingSent();
});

it('refresh() lanca em erro logico (refresh_token revogado)', function () {
    $i = ttIntegrationWithCreds(['expired' => true]);

    Http::fake([
        'auth.tiktok-shops.com/*' => Http::response([
            'code' => 105010,
            'message' => 'refresh token invalid',
        ], 200),
    ]);

    expect(fn () => TokenRefresher::refresh($i))
        ->toThrow(TiktokAuthenticationException::class);
});

it('refresh() does NOT deactivate on transient 5xx', function () {
    $i = ttIntegrationWithCreds(['expired' => true]);

    Http::fake([
        'auth.tiktok-shops.com/*' => Http::response(['code' => 500], 503),
    ]);

    expect(fn () => TokenRefresher::refresh($i))
        ->toThrow(TiktokAuthenticationException::class);
});

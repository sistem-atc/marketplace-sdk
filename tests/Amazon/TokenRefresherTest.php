<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Support\TokenRefresher;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    config(['marketplaces.amazon.lwa_token_url' => 'https://api.amazon.com/auth/o2/token']);
    Http::preventStrayRequests();
});

/**
 * Factory helper: FakeIntegration Amazon com settings padrao de credenciais.
 * Credenciais sao SEMPRE per-Integration — sem fallback config.
 */
function makeAmazonIntegration(array $overrides = []): FakeIntegration
{
    return new FakeIntegration(
        accessToken: $overrides['access_token'] ?? 'ACCESS',
        refreshToken: $overrides['refresh_token'] ?? 'REFRESH',
        settings: $overrides['settings'] ?? [
            'client_id' => 'amzn1.application-oa2-client.test',
            'client_secret' => 'test-secret',
            'marketplace_id' => 'A2Q3Y263D00KWC',
        ],
        active: true,
        expired: $overrides['expired'] ?? false,
    );
}

it('exchanges authorization code for refresh_token via LWA', function () {
    Http::fake([
        'https://api.amazon.com/auth/o2/token' => Http::response([
            'access_token' => 'Atza|test-access',
            'refresh_token' => 'Atzr|test-refresh',
            'expires_in' => 3600,
            'token_type' => 'bearer',
        ], 200),
    ]);

    $integration = makeAmazonIntegration();

    $result = TokenRefresher::exchangeAuthorizationCode($integration, 'ANxx-test-code');

    expect($result['access_token'])->toBe('Atza|test-access');
    expect($result['refresh_token'])->toBe('Atzr|test-refresh');
    expect($result['expires_in'])->toBe(3600);

    Http::assertSent(fn ($r) => $r->url() === 'https://api.amazon.com/auth/o2/token'
        && $r['grant_type'] === 'authorization_code'
        && $r['code'] === 'ANxx-test-code'
        && $r['client_id'] === 'amzn1.application-oa2-client.test'
        && $r['client_secret'] === 'test-secret'
    );
});

it('throws when authorization code exchange returns error', function () {
    Http::fake([
        'https://api.amazon.com/auth/o2/token' => Http::response([
            'error' => 'invalid_grant',
            'error_description' => 'Authorization code has expired',
        ], 400),
    ]);

    $integration = makeAmazonIntegration();

    expect(fn () => TokenRefresher::exchangeAuthorizationCode($integration, 'expired-code'))
        ->toThrow(RuntimeException::class, 'LWA code exchange failed');
});

it('throws on exchange when integration settings missing credentials', function () {
    Http::fake();

    $integration = makeAmazonIntegration(['settings' => []]);

    expect(fn () => TokenRefresher::exchangeAuthorizationCode($integration, 'some-code'))
        ->toThrow(RuntimeException::class, 'sem credenciais LWA');

    Http::assertNothingSent();
});

it('refreshes access_token using refresh_token (token expirado)', function () {
    Http::fake([
        'https://api.amazon.com/auth/o2/token' => Http::response([
            'access_token' => 'Atza|new-access',
            'refresh_token' => 'Atzr|same-refresh',
            'expires_in' => 3600,
        ], 200),
    ]);

    $integration = makeAmazonIntegration([
        'refresh_token' => 'Atzr|existing-refresh',
        'access_token' => 'old-token',
        'expired' => true,
    ]);

    $token = TokenRefresher::refresh($integration);

    expect($token)->toBe('Atza|new-access');
    expect($integration->getAccessToken())->toBe('Atza|new-access');
    expect($integration->getRefreshToken())->toBe('Atzr|same-refresh');
    expect($integration->lastExpiresIn)->toBe(3600);
});

it('preserves existing refresh_token when LWA response omits it', function () {
    Http::fake([
        'https://api.amazon.com/auth/o2/token' => Http::response([
            'access_token' => 'Atza|new-access',
            'expires_in' => 3600,
        ], 200),
    ]);

    $integration = makeAmazonIntegration([
        'refresh_token' => 'Atzr|original',
        'expired' => true,
    ]);

    TokenRefresher::refresh($integration);

    expect($integration->getRefreshToken())->toBe('Atzr|original');
});

it('refresh skips LWA hit when token is still valid (idempotente)', function () {
    Http::preventStrayRequests();

    $integration = makeAmazonIntegration([
        'access_token' => 'Atza|valid',
        'refresh_token' => 'Atzr|valid',
        'expired' => false,
    ]);

    $token = TokenRefresher::refresh($integration);

    expect($token)->toBe('Atza|valid');
    Http::assertNothingSent();
});

it('refresh triggers LWA hit when token within expiry margin', function () {
    Http::fake([
        'https://api.amazon.com/auth/o2/token' => Http::response([
            'access_token' => 'Atza|just-refreshed',
            'refresh_token' => 'Atzr|same',
            'expires_in' => 3600,
        ], 200),
    ]);

    $integration = makeAmazonIntegration([
        'access_token' => 'Atza|old',
        'refresh_token' => 'Atzr|same',
        'expired' => true,
    ]);

    $token = TokenRefresher::refresh($integration);

    expect($token)->toBe('Atza|just-refreshed');
});

it('force refresh ignora o guard mesmo com token valido', function () {
    Http::fake([
        'https://api.amazon.com/auth/o2/token' => Http::response([
            'access_token' => 'Atza|forced',
            'refresh_token' => 'Atzr|same',
            'expires_in' => 3600,
        ], 200),
    ]);

    $integration = makeAmazonIntegration([
        'access_token' => 'Atza|still-valid',
        'refresh_token' => 'Atzr|same',
        'expired' => false,
    ]);

    $token = TokenRefresher::refresh($integration, force: true);

    expect($token)->toBe('Atza|forced');
    Http::assertSent(fn ($r) => $r['grant_type'] === 'refresh_token');
});

it('rejects refresh when integration settings are missing credentials', function () {
    Http::fake();

    $integration = makeAmazonIntegration([
        'refresh_token' => 'some-refresh',
        'expired' => true,
        'settings' => [],
    ]);

    expect(fn () => TokenRefresher::refresh($integration))
        ->toThrow(RuntimeException::class, 'sem credenciais LWA');

    Http::assertNothingSent();
});

it('uses per-Integration credentials AND custom lwa_endpoint from settings', function () {
    Http::fake([
        'https://custom-lwa.example.com/oauth/token' => Http::response([
            'access_token' => 'manual-flow-token',
            'refresh_token' => 'manual-flow-refresh',
            'expires_in' => 3600,
        ], 200),
    ]);

    $integration = makeAmazonIntegration([
        'refresh_token' => 'manual-refresh',
        'expired' => true,
        'settings' => [
            'client_id' => 'manual-client-id',
            'client_secret' => 'manual-client-secret',
            'lwa_endpoint' => 'https://custom-lwa.example.com/oauth/token',
        ],
    ]);

    TokenRefresher::refresh($integration);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://custom-lwa.example.com/oauth/token'
            && $request['client_id'] === 'manual-client-id'
            && $request['client_secret'] === 'manual-client-secret'
            && $request['refresh_token'] === 'manual-refresh';
    });

    expect($integration->getAccessToken())->toBe('manual-flow-token');
});

it('uses default LWA endpoint from config when settings omit lwa_endpoint', function () {
    Http::fake([
        'https://api.amazon.com/auth/o2/token' => Http::response([
            'access_token' => 'oauth-flow-token',
            'refresh_token' => 'oauth-flow-refresh',
            'expires_in' => 3600,
        ], 200),
    ]);

    $integration = makeAmazonIntegration([
        'refresh_token' => 'oauth-refresh',
        'expired' => true,
    ]);

    TokenRefresher::refresh($integration);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.amazon.com/auth/o2/token'
            && $request['client_id'] === 'amzn1.application-oa2-client.test'
            && $request['client_secret'] === 'test-secret';
    });
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Tiktok\Exceptions\TiktokAuthenticationException;
use SistemAtc\Marketplaces\Tiktok\Support\OAuth;

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('OAuth::authorizationUrl', function () {
    it('monta a URL de autorização do TikTok Shop com service_id e state', function () {
        $url = OAuth::authorizationUrl('svc-123', '9|abc');

        expect($url)->toStartWith('https://services.tiktokshop.com/open/authorize?');
        parse_str(parse_url($url, PHP_URL_QUERY), $q);
        expect($q['service_id'])->toBe('svc-123');
        expect($q['state'])->toBe('9|abc');
    });

    it('omite state quando não informado', function () {
        $url = OAuth::authorizationUrl('svc-123');
        expect($url)->not->toContain('state=');
    });
});

describe('OAuth::exchangeAuthorizationCode', function () {
    it('troca o auth_code por tokens (grant_type=authorized_code)', function () {
        Http::fake([
            'auth.tiktok-shops.com/api/v2/token/get*' => Http::response([
                'code' => 0,
                'message' => 'success',
                'data' => [
                    'access_token' => 'AT',
                    'refresh_token' => 'RT',
                    'access_token_expire_in' => 1893456000,
                    'seller_name' => 'Loja X',
                ],
            ], 200),
        ]);

        $data = OAuth::exchangeAuthorizationCode('app-key', 'app-secret', 'AUTHCODE');

        expect($data['access_token'])->toBe('AT');
        expect($data['refresh_token'])->toBe('RT');
        expect($data['seller_name'])->toBe('Loja X');

        Http::assertSent(function ($req) {
            return str_contains($req->url(), 'grant_type=authorized_code')
                && str_contains($req->url(), 'auth_code=AUTHCODE')
                && str_contains($req->url(), 'app_key=app-key');
        });
    });

    it('lança quando code != 0 (erro lógico do TikTok apesar de HTTP 200)', function () {
        Http::fake([
            'auth.tiktok-shops.com/api/v2/token/get*' => Http::response([
                'code' => 36004003,
                'message' => 'invalid auth_code',
                'data' => [],
            ], 200),
        ]);

        OAuth::exchangeAuthorizationCode('app-key', 'app-secret', 'BAD');
    })->throws(TiktokAuthenticationException::class, 'auth_code');

    it('lança quando a resposta não tem access_token/refresh_token', function () {
        Http::fake([
            'auth.tiktok-shops.com/api/v2/token/get*' => Http::response([
                'code' => 0,
                'data' => ['access_token' => 'AT'], // sem refresh_token
            ], 200),
        ]);

        OAuth::exchangeAuthorizationCode('app-key', 'app-secret', 'CODE');
    })->throws(TiktokAuthenticationException::class);
});

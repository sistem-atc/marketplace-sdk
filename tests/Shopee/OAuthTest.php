<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Support\OAuth;

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
});

it('monta a authorizationUrl assinada com publicSign', function () {
    $url = OAuth::authorizationUrl(
        partnerId: 2030136,
        partnerKey: 'fake-key',
        redirectUri: 'https://host.test/oauth/callback/shopee',
        timestamp: 1_700_000_000,
    );

    expect($url)->toStartWith('https://partner.shopeemobile.com/api/v2/shop/auth_partner?');
    parse_str(parse_url($url, PHP_URL_QUERY), $q);

    expect($q['partner_id'])->toBe('2030136');
    expect($q['timestamp'])->toBe('1700000000');
    expect($q['redirect'])->toBe('https://host.test/oauth/callback/shopee');

    $expected = hash_hmac('sha256', '2030136/api/v2/shop/auth_partner1700000000', 'fake-key');
    expect($q['sign'])->toBe($expected);
});

it('troca o code por tokens (grant inicial)', function () {
    Http::fake([
        'partner.shopeemobile.com/api/v2/auth/token/get*' => Http::response([
            'access_token' => 'AT',
            'refresh_token' => 'RT',
            'expire_in' => 14400,
        ], 200),
    ]);

    $tokens = OAuth::exchangeAuthorizationCode(
        partnerId: 2030136,
        partnerKey: 'fake-key',
        code: 'THE-CODE',
        shopId: 999999,
        timestamp: 1_700_000_000,
    );

    expect($tokens)->toBe([
        'access_token' => 'AT',
        'refresh_token' => 'RT',
        'expires_in' => 14400,
    ]);

    Http::assertSent(function ($req) {
        $b = $req->data();

        return str_contains($req->url(), '/api/v2/auth/token/get')
            && $b['code'] === 'THE-CODE'
            && $b['shop_id'] === 999999
            && $b['partner_id'] === 2030136;
    });
});

it('lança em erro lógico da Shopee (HTTP 200 + error)', function () {
    Http::fake([
        'partner.shopeemobile.com/api/v2/auth/token/get*' => Http::response([
            'error' => 'error_auth',
            'message' => 'invalid code',
        ], 200),
    ]);

    OAuth::exchangeAuthorizationCode(2030136, 'fake-key', 'BAD', 999999, 1_700_000_000);
})->throws(\SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeAuthenticationException::class);

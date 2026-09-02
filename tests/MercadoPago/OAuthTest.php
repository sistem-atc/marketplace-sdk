<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoPago\Exceptions\MercadoPagoAuthenticationException;
use SistemAtc\Marketplaces\MercadoPago\Support\OAuth;

it('monta a URL de autorizacao com platform_id=mp e state', function () {
    $url = OAuth::authorizationUrl('APP123', 'https://erp.test/cb', 'st4te');

    expect($url)->toStartWith('https://auth.mercadopago.com.br/authorization?')
        ->and($url)->toContain('client_id=APP123')
        ->and($url)->toContain('response_type=code')
        ->and($url)->toContain('platform_id=mp')
        ->and($url)->toContain('state=st4te')
        ->and($url)->toContain('redirect_uri='.urlencode('https://erp.test/cb'));
});

it('troca o code por tokens via form-urlencoded em /oauth/token', function () {
    Http::fake(['api.mercadopago.com/oauth/token' => Http::response([
        'access_token' => 'APP_USR-x', 'refresh_token' => 'TG-y', 'expires_in' => 21600, 'user_id' => 42,
    ])]);

    $data = OAuth::exchangeAuthorizationCode('cli', 'sec', 'TG-code', 'https://erp.test/cb');

    expect($data['access_token'])->toBe('APP_USR-x')->and($data['user_id'])->toBe(42);
    Http::assertSent(fn (Request $r) => $r->method() === 'POST'
        && $r->url() === 'https://api.mercadopago.com/oauth/token'
        && $r->isForm()
        && $r['grant_type'] === 'authorization_code'
        && $r['code'] === 'TG-code'
        && $r['redirect_uri'] === 'https://erp.test/cb');
});

it('refresh manda grant_type=refresh_token', function () {
    Http::fake(['api.mercadopago.com/oauth/token' => Http::response(['access_token' => 'new', 'refresh_token' => 'rolling'])]);

    expect(OAuth::refresh('cli', 'sec', 'old')['refresh_token'])->toBe('rolling');
    Http::assertSent(fn (Request $r) => $r['grant_type'] === 'refresh_token' && $r['refresh_token'] === 'old');
});

it('falha de OAuth vira MercadoPagoAuthenticationException', function () {
    Http::fake(['api.mercadopago.com/oauth/token' => Http::response(['error' => 'invalid_grant'], 400)]);

    OAuth::refresh('cli', 'sec', 'revogado');
})->throws(MercadoPagoAuthenticationException::class);

it('resposta sem access_token tambem falha', function () {
    Http::fake(['api.mercadopago.com/oauth/token' => Http::response(['scope' => 'x'])]);

    OAuth::exchangeAuthorizationCode('cli', 'sec', 'c', 'r');
})->throws(MercadoPagoAuthenticationException::class);

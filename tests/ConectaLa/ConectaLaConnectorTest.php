<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\ConectaLa\Exceptions\ConectaLaAuthenticationException;
use SistemAtc\Marketplaces\ConectaLa\Exceptions\ConectaLaRequestException;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function conectaLaIntegration(array $settings = []): FakeIntegration
{
    return new FakeIntegration(
        accessToken: null,
        refreshToken: null,
        settings: $settings ?: [
            'api_key' => 'K', 'store_key' => 'S', 'provider_key' => 'P', 'user_email' => 'u@x.com',
        ],
        active: true,
    );
}

beforeEach(function () {
    config(['marketplaces.conectala.api_base' => 'http://teste.conectala.com.br/app/Api/V1']);
});

it('orders()->queue manda os headers x-* e bate no GET /Orders', function () {
    Http::fake(['teste.conectala.com.br/app/Api/V1/Orders*' => Http::response(['data' => []], 200)]);

    $r = MarketPlaces::ConectaLa()->orders(conectaLaIntegration())->queue(['new_order' => 1]);

    expect($r)->toBe(['data' => []]);
    Http::assertSent(fn ($req) => str_contains($req->url(), '/Orders')
        && $req->hasHeader('x-api-key', 'K')
        && $req->hasHeader('x-store-key', 'S')
        && $req->hasHeader('x-provider-key', 'P'));
});

it('createNfe faz POST em /Orders/nfe com o payload', function () {
    Http::fake(['*/Orders/nfe' => Http::response(['ok' => true], 200)]);

    $r = MarketPlaces::ConectaLa()->orders(conectaLaIntegration())->createNfe(['xml' => '<nfe/>']);

    expect($r)->toBe(['ok' => true]);
    Http::assertSent(fn ($req) => $req->method() === 'POST'
        && str_contains($req->url(), '/Orders/nfe')
        && $req['xml'] === '<nfe/>');
});

it('products()->updateStock faz PUT em /Products/{sku}', function () {
    Http::fake(['*/Products/ABC-1' => Http::response(['ok' => true], 200)]);

    MarketPlaces::ConectaLa()->products(conectaLaIntegration())->updateStock('ABC-1', ['stock' => 7]);

    Http::assertSent(fn ($req) => $req->method() === 'PUT' && str_contains($req->url(), '/Products/ABC-1'));
});

it('erro HTTP vira ConectaLaRequestException com a mensagem da API', function () {
    Http::fake(['*/Infos' => Http::response(['message' => 'chave invalida'], 401)]);

    expect(fn () => MarketPlaces::ConectaLa()->infos(conectaLaIntegration())->store())
        ->toThrow(ConectaLaRequestException::class, 'chave invalida');
});

it('sem api_key lanca ConectaLaAuthenticationException', function () {
    expect(fn () => MarketPlaces::ConectaLa()->infos(conectaLaIntegration(['store_key' => 'S']))->store())
        ->toThrow(ConectaLaAuthenticationException::class);
});

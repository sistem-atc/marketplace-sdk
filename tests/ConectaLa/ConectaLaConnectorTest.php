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

it('financial()->extract bate no GET /financeiro/extrato/extrato', function () {
    Http::fake(['*/financeiro/extrato/extrato*' => Http::response(['data' => []], 200)]);
    MarketPlaces::ConectaLa()->financial(conectaLaIntegration())->extract(['start_date' => '2026-06-01']);
    Http::assertSent(fn ($req) => str_contains($req->url(), '/financeiro/extrato/extrato'));
});

it('financial()->conciliationBatches bate no GET /Financial/conciliationlote', function () {
    Http::fake(['*/Financial/conciliationlote*' => Http::response(['data' => []], 200)]);
    MarketPlaces::ConectaLa()->financial(conectaLaIntegration())->conciliationBatches();
    Http::assertSent(fn ($req) => str_contains($req->url(), '/Financial/conciliationlote'));
});

it('catalogs()->stores e collections()->list batem nos paths certos', function () {
    Http::fake([
        '*/GetCatalogs/stores*' => Http::response(['data' => []], 200),
        '*/Collections/all*' => Http::response(['data' => []], 200),
    ]);
    MarketPlaces::ConectaLa()->catalogs(conectaLaIntegration())->stores();
    MarketPlaces::ConectaLa()->collections(conectaLaIntegration())->list(['name' => 'x']);
    Http::assertSent(fn ($req) => str_contains($req->url(), '/GetCatalogs/stores'));
    Http::assertSent(fn ($req) => str_contains($req->url(), '/Collections/all'));
});

it('variations/tracking/companies/stores/users batem nos paths certos', function () {
    Http::fake(['*' => Http::response(['ok' => true], 200)]);
    $i = conectaLaIntegration();

    MarketPlaces::ConectaLa()->variations($i)->get('SKU1');
    MarketPlaces::ConectaLa()->tracking($i)->send('ORD1', ['code' => 'BR1']);
    MarketPlaces::ConectaLa()->companies($i)->list();
    MarketPlaces::ConectaLa()->stores($i)->update('9', ['name' => 'x']);
    MarketPlaces::ConectaLa()->users($i)->create(['email' => 'u@x.com']);

    Http::assertSent(fn ($r) => str_contains($r->url(), '/Variations/SKU1') && $r->method() === 'GET');
    Http::assertSent(fn ($r) => str_contains($r->url(), '/Tracking/ORD1') && $r->method() === 'POST');
    Http::assertSent(fn ($r) => str_contains($r->url(), '/Companies') && $r->method() === 'GET');
    Http::assertSent(fn ($r) => str_contains($r->url(), '/Stores/9') && $r->method() === 'PUT');
    Http::assertSent(fn ($r) => str_contains($r->url(), '/Users') && $r->method() === 'POST');
});

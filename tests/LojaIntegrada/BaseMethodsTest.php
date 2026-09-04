<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Brand\BrandMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Enviali\EnvialiMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Marketing\MarketingMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Exceptions\LojaIntegradaRequestException;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

it('rootUrl respeita api_base custom das settings (tira o /v1)', function () {
    Http::preventStrayRequests();
    Http::fake(['sandbox.li.test/*' => Http::response([], 200)]);
    $i = lojaIntegradaIntegration(['api_key' => 'k1', 'application_key' => 'a1', 'api_base' => 'https://sandbox.li.test/v1/']);

    lojaIntegradaMethods(MarketingMethods::class, $i)->listActiveAutomations();

    Http::assertSent(fn ($req) => $req->url() === 'https://sandbox.li.test/v3/marketing/automations/active');
});

it('headers extras e multipart não vazam pra chamada seguinte (client clonado)', function () {
    lojaIntegradaFake();
    $i = lojaIntegradaIntegration();
    $enviali = lojaIntegradaMethods(EnvialiMethods::class, $i);
    $brand = lojaIntegradaMethods(BrandMethods::class, $i);

    $enviali->wallets(correlationId: 'cid');
    $brand->updateImageByFile(1, 'BIN');
    $brand->update(1, ['nome' => 'X']);

    Http::assertSentCount(3);
    Http::assertSent(fn ($req) => str_ends_with($req->url(), '/marca/1/')
        && ! $req->hasHeader('x-correlation-id')
        && ! $req->isMultipart()
        && $req->isJson()
        && $req['nome'] === 'X');
});

it('erro 4xx vira LojaIntegradaRequestException com o status', function () {
    lojaIntegradaFake(['error' => 'nao encontrado'], 404);

    expect(fn () => lojaIntegradaMethods(BrandMethods::class)->get(1))
        ->toThrow(LojaIntegradaRequestException::class);
});

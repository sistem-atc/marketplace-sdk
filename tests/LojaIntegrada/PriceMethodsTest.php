<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Price\PriceMethods;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

beforeEach(fn () => lojaIntegradaFake());

it('list faz GET produto_preco/', function () {
    lojaIntegradaMethods(PriceMethods::class)->list();
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/produto_preco/?limit=20&offset=0');
});

it('get faz GET produto_preco/{id}/ com id_externo', function () {
    lojaIntegradaMethods(PriceMethods::class)->get('EXT-1', byExternalId: true);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/produto_preco/EXT-1/?id_externo=1');
});

it('getSet faz GET produto_preco/set/', function () {
    lojaIntegradaMethods(PriceMethods::class)->getSet([5, 6, 7]);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/produto_preco/set/5;6;7/');
});

it('update faz PUT produto_preco/{id}/ com cheio/promocional', function () {
    lojaIntegradaMethods(PriceMethods::class)->update(174389010, ['cheio' => 32.84, 'promocional' => 20]);
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/produto_preco/174389010/', ['cheio' => 32.84, 'promocional' => 20]);
});

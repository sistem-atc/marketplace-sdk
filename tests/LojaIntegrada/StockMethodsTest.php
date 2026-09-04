<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Stock\StockMethods;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

beforeEach(fn () => lojaIntegradaFake());

it('list faz GET produto_estoque/', function () {
    lojaIntegradaMethods(StockMethods::class)->list(offset: 20);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/produto_estoque/?limit=20&offset=20');
});

it('get faz GET produto_estoque/{id}/', function () {
    lojaIntegradaMethods(StockMethods::class)->get(174389010);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/produto_estoque/174389010/');
});

it('getSet faz GET produto_estoque/set/a;b/', function () {
    lojaIntegradaMethods(StockMethods::class)->getSet([1, 2]);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/produto_estoque/set/1;2/');
});

it('update faz PUT produto_estoque/{id}/ com o body', function () {
    lojaIntegradaMethods(StockMethods::class)->update(174389010, ['gerenciado' => true, 'quantidade' => 6]);
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/produto_estoque/174389010/', ['gerenciado' => true, 'quantidade' => 6]);
});

it('updateQuantity manda só quantidade', function () {
    lojaIntegradaMethods(StockMethods::class)->updateQuantity(174389010, 3);
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/produto_estoque/174389010/', ['quantidade' => 3]);
});

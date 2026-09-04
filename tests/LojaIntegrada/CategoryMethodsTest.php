<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Category\CategoryMethods;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

beforeEach(fn () => lojaIntegradaFake());

it('list faz GET categoria/ com limit/offset e filtros', function () {
    lojaIntegradaMethods(CategoryMethods::class)->list(limit: 10, offset: 20, filters: ['nome' => 'Camisetas']);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/categoria/?limit=10&offset=20&nome=Camisetas');
});

it('get faz GET categoria/{id}/ e aceita id_externo', function () {
    lojaIntegradaMethods(CategoryMethods::class)->get(18248952, byExternalId: true);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/categoria/18248952/?id_externo=1');
});

it('getSet junta ids com ; no path', function () {
    lojaIntegradaMethods(CategoryMethods::class)->getSet([11216243, 11216247]);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/categoria/set/11216243;11216247/');
});

it('create faz POST categoria/ com o body', function () {
    lojaIntegradaMethods(CategoryMethods::class)->create(['nome' => 'Camisetas', 'categoria_pai' => null]);
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v1/categoria/', ['nome' => 'Camisetas', 'categoria_pai' => null]);
});

it('update faz PUT categoria/{id}/ com body e id_externo na query', function () {
    lojaIntegradaMethods(CategoryMethods::class)->update(77, ['nome' => 'Coloridas'], byExternalId: true);
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/categoria/77/?id_externo=1', ['nome' => 'Coloridas']);
});

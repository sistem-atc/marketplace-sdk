<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Grade\GradeMethods;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

beforeEach(fn () => lojaIntegradaFake());

it('list faz GET grades/', function () {
    lojaIntegradaMethods(GradeMethods::class)->list(limit: 5);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/grades/?limit=5&offset=0');
});

it('get faz GET grades/{id}/', function () {
    lojaIntegradaMethods(GradeMethods::class)->get(12);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/grades/12/');
});

it('create faz POST grades/ com nome e nome_visivel', function () {
    lojaIntegradaMethods(GradeMethods::class)->create(nome: 'Tamanho', nomeVisivel: 'Tam.');
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v1/grades/', ['nome' => 'Tamanho', 'nome_visivel' => 'Tam.']);
});

it('createVariation faz POST grade/{id}/variacao/', function () {
    lojaIntegradaMethods(GradeMethods::class)->createVariation(12, 'GG');
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v1/grade/12/variacao/', ['nome' => 'GG']);
});

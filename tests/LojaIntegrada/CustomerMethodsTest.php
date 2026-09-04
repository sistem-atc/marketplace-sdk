<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Customer\CustomerMethods;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

beforeEach(fn () => lojaIntegradaFake());

it('list faz GET cliente/', function () {
    lojaIntegradaMethods(CustomerMethods::class)->list();
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/cliente/?limit=20&offset=0');
});

it('get faz GET cliente/{id}/', function () {
    lojaIntegradaMethods(CustomerMethods::class)->get(91367652);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/cliente/91367652/');
});

it('search faz GET cliente/search/?cliente_email=', function () {
    lojaIntegradaMethods(CustomerMethods::class)->search('a@b.com');
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/cliente/search/?cliente_email=a%40b.com');
});

it('create faz POST cliente/', function () {
    lojaIntegradaMethods(CustomerMethods::class)->create(['email' => 'a@b.com', 'nome' => 'A', 'tipo' => 'PF']);
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v1/cliente/', ['email' => 'a@b.com', 'nome' => 'A', 'tipo' => 'PF']);
});

it('updateGroup faz PUT cliente/{id}/grupo/ com grupo', function () {
    lojaIntegradaMethods(CustomerMethods::class)->updateGroup(91367652, 'VIPS');
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/cliente/91367652/grupo/', ['grupo' => 'VIPS']);
});

it('listGroups faz GET grupo/', function () {
    lojaIntegradaMethods(CustomerMethods::class)->listGroups();
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/grupo/?limit=20&offset=0');
});

it('getGroup faz GET grupo/{id}/', function () {
    lojaIntegradaMethods(CustomerMethods::class)->getGroup(1);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/grupo/1/');
});

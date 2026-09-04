<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Coupon\CouponMethods;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

beforeEach(fn () => lojaIntegradaFake());

it('list faz GET cupom/', function () {
    lojaIntegradaMethods(CouponMethods::class)->list(filters: ['ativo' => 1]);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/cupom/?limit=20&offset=0&ativo=1');
});

it('get faz GET cupom/{id}/', function () {
    lojaIntegradaMethods(CouponMethods::class)->get(8);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/cupom/8/');
});

it('create faz POST cupom/', function () {
    lojaIntegradaMethods(CouponMethods::class)->create(['codigo' => 'CUPOM20OFF', 'tipo' => 'porcentagem', 'valor' => '20.00']);
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v1/cupom/', ['codigo' => 'CUPOM20OFF', 'tipo' => 'porcentagem', 'valor' => '20.00']);
});

it('update faz PUT cupom/{id}/', function () {
    lojaIntegradaMethods(CouponMethods::class)->update(8, ['ativo' => false]);
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/cupom/8/', ['ativo' => false]);
});

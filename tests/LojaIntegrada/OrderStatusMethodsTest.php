<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\OrderStatus\OrderStatusMethods;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

beforeEach(fn () => lojaIntegradaFake());

it('list faz GET situacao/', function () {
    lojaIntegradaMethods(OrderStatusMethods::class)->list();
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/situacao/?limit=20&offset=0');
});

it('getForOrder faz GET situacao/pedido/{numero}/', function () {
    lojaIntegradaMethods(OrderStatusMethods::class)->getForOrder(15230);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/situacao/pedido/15230/');
});

it('updateForOrder faz PUT situacao/pedido/{numero}/ com codigo', function () {
    lojaIntegradaMethods(OrderStatusMethods::class)->updateForOrder(15230, 'pedido_enviado');
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/situacao/pedido/15230/', ['codigo' => 'pedido_enviado']);
});

it('history busca por numero ou id_externo', function () {
    lojaIntegradaMethods(OrderStatusMethods::class)->history(numero: 165);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/situacao_historico/search/?numero=165');
});

it('history por id_externo', function () {
    lojaIntegradaMethods(OrderStatusMethods::class)->history(idExterno: 2);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/situacao_historico/search/?id_externo=2');
});

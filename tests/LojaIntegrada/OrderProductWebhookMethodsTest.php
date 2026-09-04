<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Webhook\WebhookMethods;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

beforeEach(fn () => lojaIntegradaFake());

// --- OrderMethods (novos)

it('updateExternalId faz PUT pedido/{numero}/ com id_externo', function () {
    lojaIntegradaMethods(OrderMethods::class)->updateExternalId(15230, 667);
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/pedido/15230/', ['id_externo' => 667]);
});

it('updateShipmentTracking faz PUT pedido_envio/{id}/ com objeto', function () {
    lojaIntegradaMethods(OrderMethods::class)->updateShipmentTracking(4242, 'PJ258408772BR');
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/pedido_envio/4242/', ['objeto' => 'PJ258408772BR']);
});

it('createIntegrationSale faz POST integration/sales/', function () {
    lojaIntegradaMethods(OrderMethods::class)->createIntegrationSale(['info' => ['status' => 'paid']]);
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v1/integration/sales/', ['info' => ['status' => 'paid']]);
});

it('updateIntegrationSale faz PUT integration/sales/{id}/', function () {
    lojaIntegradaMethods(OrderMethods::class)->updateIntegrationSale('S1', ['info' => ['status' => 'shipped']]);
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/integration/sales/S1/', ['info' => ['status' => 'shipped']]);
});

// --- ProductMethods (id_externo / descricao_completa / alias)

it('product get sem opções mantém GET produto/{id}/ limpo', function () {
    lojaIntegradaMethods(ProductMethods::class)->get(1);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/produto/1/');
});

it('product get com id_externo e descricao_completa', function () {
    lojaIntegradaMethods(ProductMethods::class)->get('EXT', byExternalId: true, fullDescription: true);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/produto/EXT/?id_externo=1&descricao_completa=1');
});

it('product update com id_externo na query e body', function () {
    lojaIntegradaMethods(ProductMethods::class)->update('EXT', ['ativo' => true], byExternalId: true);
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/produto/EXT/?id_externo=1', ['ativo' => true]);
});

it('updateAlias faz PUT produto/{id}/alias/ com absolute_path e replace_main', function () {
    lojaIntegradaMethods(ProductMethods::class)->updateAlias(1, '/nova-url', replaceMain: true);
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/produto/1/alias/?replace_main=1', ['absolute_path' => '/nova-url']);
});

// --- WebhookMethods (/webhooks/v1)

it('registerOrderWebhook faz PUT /webhooks/v1/pedido fora do /v1', function () {
    lojaIntegradaMethods(WebhookMethods::class)->registerOrderWebhook('https://h/li', 'tok');
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/webhooks/v1/pedido', ['notifyUrl' => 'https://h/li', 'token' => 'tok']);
});

it('unregisterOrderWebhook faz DELETE /webhooks/v1/pedido com body', function () {
    lojaIntegradaMethods(WebhookMethods::class)->unregisterOrderWebhook('https://h/li', 'tok');
    lojaIntegradaAssertSent('DELETE', 'https://api.awsli.com.br/webhooks/v1/pedido', ['notifyUrl' => 'https://h/li', 'token' => 'tok']);
});

it('registerProductWebhook faz PUT /webhooks/v1/produto', function () {
    lojaIntegradaMethods(WebhookMethods::class)->registerProductWebhook('https://h/li', 'tok');
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/webhooks/v1/produto', ['notifyUrl' => 'https://h/li', 'token' => 'tok']);
});

it('unregisterProductWebhook faz DELETE /webhooks/v1/produto', function () {
    lojaIntegradaMethods(WebhookMethods::class)->unregisterProductWebhook('https://h/li', 'tok');
    lojaIntegradaAssertSent('DELETE', 'https://api.awsli.com.br/webhooks/v1/produto', ['notifyUrl' => 'https://h/li', 'token' => 'tok']);
});

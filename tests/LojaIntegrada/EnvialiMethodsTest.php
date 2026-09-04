<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Enviali\EnvialiMethods;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

beforeEach(fn () => lojaIntegradaFake());

it('listPostages faz GET /enviali/v2/postage fora do /v1 com x-correlation-id informado', function () {
    lojaIntegradaMethods(EnvialiMethods::class)->listPostages(['status' => 'billed'], correlationId: 'cid-1');
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/enviali/v2/postage?status=billed', headers: ['x-correlation-id' => 'cid-1']);
});

it('listPostages gera x-correlation-id (uuid) quando não informado', function () {
    lojaIntegradaMethods(EnvialiMethods::class)->listPostages();
    Http::assertSent(fn ($req) => $req->url() === 'https://api.awsli.com.br/enviali/v2/postage'
        && preg_match('/^[0-9a-f-]{36}$/', $req->header('x-correlation-id')[0]) === 1);
});

it('getPostage faz GET /enviali/v2/postage/{id}', function () {
    lojaIntegradaMethods(EnvialiMethods::class)->getPostage(55, correlationId: 'c');
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/enviali/v2/postage/55', headers: ['x-correlation-id' => 'c']);
});

it('cancelPostage faz DELETE /enviali/v2/postage?id=', function () {
    lojaIntegradaMethods(EnvialiMethods::class)->cancelPostage([55, 56], correlationId: 'c');
    lojaIntegradaAssertSent('DELETE', 'https://api.awsli.com.br/enviali/v2/postage?id=55%2C56', headers: ['x-correlation-id' => 'c']);
});

it('billPostage faz POST /enviali/v2/postage/bill com postage[]', function () {
    lojaIntegradaMethods(EnvialiMethods::class)->billPostage([['order' => ['id' => '1']]], correlationId: 'c');
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/enviali/v2/postage/bill', ['postage' => [['order' => ['id' => '1']]]], headers: ['x-correlation-id' => 'c']);
});

it('estimate faz POST /enviali/v2/postage/estimate', function () {
    lojaIntegradaMethods(EnvialiMethods::class)->estimate(['zipcode' => '88104600', 'merchandise_value' => 5500], correlationId: 'c');
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/enviali/v2/postage/estimate', ['zipcode' => '88104600', 'merchandise_value' => 5500], headers: ['x-correlation-id' => 'c']);
});

it('documents faz GET /enviali/v2/postage/doc?ids=', function () {
    lojaIntegradaMethods(EnvialiMethods::class)->documents(55, correlationId: 'c');
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/enviali/v2/postage/doc?ids=55', headers: ['x-correlation-id' => 'c']);
});

it('pdf faz GET /enviali/v2/postage/pdf?ids=&paperType=', function () {
    lojaIntegradaMethods(EnvialiMethods::class)->pdf([55], paperType: 'A4', correlationId: 'c');
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/enviali/v2/postage/pdf?ids=55&paperType=A4', headers: ['x-correlation-id' => 'c']);
});

it('tracking faz GET /enviali/v2/postage/tracking/{codigo}', function () {
    lojaIntegradaMethods(EnvialiMethods::class)->tracking('PJ258408772BR');
    Http::assertSent(fn ($req) => $req->method() === 'GET' && $req->url() === 'https://api.awsli.com.br/enviali/v2/postage/tracking/PJ258408772BR');
});

it('wallets faz GET /enviali/v2/wallets', function () {
    lojaIntegradaMethods(EnvialiMethods::class)->wallets(correlationId: 'c');
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/enviali/v2/wallets', headers: ['x-correlation-id' => 'c']);
});

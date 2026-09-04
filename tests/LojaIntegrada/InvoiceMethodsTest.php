<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Invoice\InvoiceMethods;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

beforeEach(fn () => lojaIntegradaFake());

it('create faz POST multipart em integration/pedido/nf/ e preenche account_key das settings', function () {
    lojaIntegradaMethods(InvoiceMethods::class)->create(['sale_number' => '189', 'invoice_number' => '00003', 'serie' => '1', 'access_key' => str_repeat('0', 44)]);
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v1/integration/pedido/nf/', ['account_key' => 'k1', 'sale_number' => '189', 'invoice_number' => '00003', 'serie' => '1'], multipart: true);
});

it('update faz PUT multipart em integration/pedido/nf/ respeitando account_key informado', function () {
    lojaIntegradaMethods(InvoiceMethods::class)->update(['account_key' => 'outra', 'sale_number' => '189', 'url_xml' => 'https://x/nf.xml']);
    lojaIntegradaAssertSent('PUT', 'https://api.awsli.com.br/v1/integration/pedido/nf/', ['account_key' => 'outra', 'url_xml' => 'https://x/nf.xml'], multipart: true);
});

it('get faz GET pedido_nf/{id}/', function () {
    lojaIntegradaMethods(InvoiceMethods::class)->get(10);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/pedido_nf/10/');
});

it('linkDce faz POST multipart em integration/pedido/dce/', function () {
    lojaIntegradaMethods(InvoiceMethods::class)->linkDce(['order_number' => '51179', 'access_key' => 'K', 'url_xml' => 'https://x/dce.xml']);
    lojaIntegradaAssertSent('POST', 'https://api.awsli.com.br/v1/integration/pedido/dce/', ['order_number' => '51179', 'url_xml' => 'https://x/dce.xml'], multipart: true);
});

it('unlinkDce faz DELETE integration/pedido/dce/?order_number=', function () {
    lojaIntegradaMethods(InvoiceMethods::class)->unlinkDce(51179);
    lojaIntegradaAssertSent('DELETE', 'https://api.awsli.com.br/v1/integration/pedido/dce/?order_number=51179');
});

it('getDce faz GET pedido_dce/{numero}/', function () {
    lojaIntegradaMethods(InvoiceMethods::class)->getDce(51179);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/pedido_dce/51179/');
});

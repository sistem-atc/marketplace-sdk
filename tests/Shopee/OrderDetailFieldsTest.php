<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

/**
 * TRAVA DE REGRESSAO.
 *
 * A Shopee so devolve o que esta em `response_optional_fields`. Em 2026-06-08 a
 * migracao do conector interno pra esta lib trocou o default de 33 campos por 9
 * e derrubou total_amount/fulfillment_flag/payment_method/shipping_carrier em
 * silencio — 99% dos pedidos Shopee de julho ficaram sem valor e sem
 * classificacao Full/FBS no ERP, por ~40 dias, sem UM erro sequer.
 *
 * Estes testes existem pra que tirar um campo do default quebre a suite, e nao
 * a producao.
 */
function shopeeOrderIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::fake([
        'partner.shopeemobile.com/api/v2/order/get_order_detail*' => Http::response([
            'response' => ['order_list' => [['order_sn' => 'X1']]],
        ]),
    ]);
});

it('pede os campos que o ERP projeta — tirar um deles apaga o dado, nao so deixa de trazer', function (string $campo) {
    MarketPlaces::Shopee()->orders(shopeeOrderIntegration())->getOrderDetail(['X1']);

    Http::assertSent(function ($req) use ($campo) {
        $fields = urldecode($req->url());

        return str_contains($fields, $campo);
    });
})->with([
    // orders.total_value
    'total_amount',
    // orders.logistic_type — dispara o pipeline de 2 NFes do Full/FBS
    'fulfillment_flag',
    // order_payments (o projector faz early-return sem este campo)
    'payment_method',
    // orders.shipping_mode
    'shipping_carrier',
    // orders.status + item_list/invoice_data do pipeline fiscal
    'order_status',
    'item_list',
    'invoice_data',
    // PII do comprador
    'recipient_address',
    'buyer_cpf_id',
]);

it('deixa o chamador restringir os campos quando quer so um pedaco', function () {
    MarketPlaces::Shopee()->orders(shopeeOrderIntegration())->getOrderDetail(['X1'], ['invoice_data']);

    Http::assertSent(function ($req) {
        $url = urldecode($req->url());

        return str_contains($url, 'response_optional_fields=invoice_data')
            && ! str_contains($url, 'total_amount');
    });
});

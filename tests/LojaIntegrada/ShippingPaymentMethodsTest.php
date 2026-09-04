<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Payment\PaymentMethods;
use SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Shipping\ShippingMethods;

require_once __DIR__.'/Support/lojaIntegradaHelpers.php';

beforeEach(fn () => lojaIntegradaFake());

it('shipping list faz GET envio/', function () {
    lojaIntegradaMethods(ShippingMethods::class)->list();
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/envio/?limit=20&offset=0');
});

it('shipping get faz GET envio/{id}/', function () {
    lojaIntegradaMethods(ShippingMethods::class)->get(4);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/envio/4/');
});

it('payment list faz GET pagamento/', function () {
    lojaIntegradaMethods(PaymentMethods::class)->list();
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/pagamento/?limit=20&offset=0');
});

it('payment get faz GET pagamento/{id}/', function () {
    lojaIntegradaMethods(PaymentMethods::class)->get(2);
    lojaIntegradaAssertSent('GET', 'https://api.awsli.com.br/v1/pagamento/2/');
});

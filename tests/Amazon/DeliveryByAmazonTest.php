<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\DeliveryByAmazon;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

const DBA = 'https://sellingpartnerapi-na.amazon.com/delivery/2022-07-01';

beforeEach(function () {
    Http::preventStrayRequests();
});

function deliveryByAmazonEndpoint(): DeliveryByAmazon
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new DeliveryByAmazon(new Client($integration));
}

it('submitInvoice POST /invoice?orderId= com body base64', function () {
    Http::fake([DBA.'/invoice*' => Http::response(['errors' => []])]);
    $body = [
        'invoiceContent' => base64_encode('<nfeProc/>'),
        'contentMD5Value' => base64_encode(md5('<nfeProc/>', true)),
        'invoiceType' => 'Outbound',
        'programType' => 'EasyShip',
        'marketplaceId' => 'A2Q3Y263D00KWC',
    ];
    expect(deliveryByAmazonEndpoint()->submitInvoice($body, orderId: '701-1234567-1234567'))->toBe(['errors' => []]);
    Http::assertSent(fn (Request $r) => $r->method() === 'POST'
        && $r->url() === DBA.'/invoice?orderId=701-1234567-1234567'
        && $r->header('x-amz-access-token')[0] === 'Atza|valid-token'
        && $r->data() === $body);
});

it('submitInvoice aceita shipmentId no lugar de orderId', function () {
    Http::fake([DBA.'/invoice*' => Http::response(['errors' => []])]);
    deliveryByAmazonEndpoint()->submitInvoice(['invoiceType' => 'Outbound'], shipmentId: 'SHP-1');
    Http::assertSent(fn (Request $r) => $r->method() === 'POST' && $r->url() === DBA.'/invoice?shipmentId=SHP-1');
});

it('getInvoiceStatus GET /invoice/status com marketplaceId + invoiceType + programType + orderId', function () {
    Http::fake([DBA.'/invoice/status*' => Http::response(['amazonOrderId' => '701-1', 'invoiceStatus' => 'Accepted'])]);
    $resp = deliveryByAmazonEndpoint()->getInvoiceStatus('A2Q3Y263D00KWC', 'Outbound', 'SelfShip', orderId: '701-1');
    expect($resp['invoiceStatus'])->toBe('Accepted');
    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === DBA.'/invoice/status?'.http_build_query(['orderId' => '701-1', 'marketplaceId' => 'A2Q3Y263D00KWC', 'invoiceType' => 'Outbound', 'programType' => 'SelfShip'])
        && $r->header('x-amz-access-token')[0] === 'Atza|valid-token');
});

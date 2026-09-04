<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\FulfillmentInboundV0;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function fulfillmentInboundV0Integration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );
}

function fulfillmentInboundV0(): FulfillmentInboundV0
{
    return new FulfillmentInboundV0(new Client(fulfillmentInboundV0Integration()));
}

function fulfillmentInboundV0AssertSent(string $method, string $url): void
{
    Http::assertSent(fn (Request $r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
}

it('getPrepInstructions sends ShipToCountryCode + lists', function () {
    Http::fake(['*' => Http::response(['payload' => ['SKUPrepInstructionsList' => []]])]);

    $resp = fulfillmentInboundV0()->getPrepInstructions('BR', ['SellerSKUList' => 'SKU-A,SKU-B']);

    expect($resp['payload'])->toHaveKey('SKUPrepInstructionsList');
    fulfillmentInboundV0AssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/fba/inbound/v0/prepInstructions?ShipToCountryCode=BR&SellerSKUList=SKU-A%2CSKU-B');
});

it('getLabels sends PageType + LabelType and optionals', function () {
    Http::fake(['*' => Http::response(['payload' => ['DownloadURL' => 'https://x/y.pdf']])]);

    fulfillmentInboundV0()->getLabels('FBA15DJCQ1ZF', 'PackageLabel_Letter_2', 'UNIQUE', ['NumberOfPackages' => 3]);

    fulfillmentInboundV0AssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/fba/inbound/v0/shipments/FBA15DJCQ1ZF/labels?PageType=PackageLabel_Letter_2&LabelType=UNIQUE&NumberOfPackages=3');
});

it('getBillOfLading hits the shipment BOL path', function () {
    Http::fake(['*' => Http::response(['payload' => ['DownloadURL' => 'https://x/bol.pdf']])]);

    fulfillmentInboundV0()->getBillOfLading('FBA15DJCQ1ZF');

    fulfillmentInboundV0AssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/fba/inbound/v0/shipments/FBA15DJCQ1ZF/billOfLading');
});

it('getShipments sends QueryType + MarketplaceId + NextToken', function () {
    Http::fake(['*' => Http::response(['payload' => ['ShipmentData' => [], 'NextToken' => null]])]);

    fulfillmentInboundV0()->getShipments('NEXT_TOKEN', 'A2Q3Y263D00KWC', ['NextToken' => 'abc']);

    fulfillmentInboundV0AssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/fba/inbound/v0/shipments?QueryType=NEXT_TOKEN&MarketplaceId=A2Q3Y263D00KWC&NextToken=abc');
});

it('getShipmentItemsByShipmentId hits the shipment items path', function () {
    Http::fake(['*' => Http::response(['payload' => ['ItemData' => []]])]);

    fulfillmentInboundV0()->getShipmentItemsByShipmentId('FBA15DJCQ1ZF', ['MarketplaceId' => 'A2Q3Y263D00KWC']);

    fulfillmentInboundV0AssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/fba/inbound/v0/shipments/FBA15DJCQ1ZF/items?MarketplaceId=A2Q3Y263D00KWC');
});

it('getShipmentItems sends QueryType + MarketplaceId + window', function () {
    Http::fake(['*' => Http::response(['payload' => ['ItemData' => []]])]);

    fulfillmentInboundV0()->getShipmentItems('DATE_RANGE', 'A2Q3Y263D00KWC', ['LastUpdatedAfter' => '2026-01-01T00:00:00Z']);

    fulfillmentInboundV0AssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/fba/inbound/v0/shipmentItems?QueryType=DATE_RANGE&MarketplaceId=A2Q3Y263D00KWC&LastUpdatedAfter=2026-01-01T00%3A00%3A00Z');
});

<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Orders;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function ordersExtras(): Orders
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new Orders(new Client($integration));
}

/** Um único request enviado, com verbo + URL exata + token LWA. */
function ordersExtrasAssertSent(string $method, string $url, ?array $body = null, string $token = 'Atza|valid-token'): void
{
    Http::assertSent(function (Request $req) use ($method, $url, $body, $token): bool {
        if ($req->method() !== $method || $req->url() !== $url) {
            return false;
        }
        if ($req->header('x-amz-access-token')[0] !== $token) {
            return false;
        }
        if ($body !== null && $req->data() !== $body) {
            return false;
        }

        return true;
    });
}

use SistemAtc\Marketplaces\Amazon\Endpoints\OrdersV2026;

const ORD = 'https://sellingpartnerapi-na.amazon.com/orders/v0/orders/701-1111111-1111111';
const RDT_URL = 'https://sellingpartnerapi-na.amazon.com/tokens/2021-03-01/restrictedDataToken';

function ordersExtrasFakeRdt(string $path, array $response): void
{
    Http::fake([
        RDT_URL => Http::response(['restrictedDataToken' => 'Atz.rdt|abc', 'expiresIn' => 3600]),
        '*'.$path.'*' => Http::response($response),
    ]);
}

function ordersExtrasAssertRdtRequested(string $method, string $path): void
{
    Http::assertSent(fn (Request $r) => $r->url() === RDT_URL
        && $r->method() === 'POST'
        && $r->data() === ['restrictedResources' => [['method' => $method, 'path' => $path]]]);
}

it('getOrderBuyerInfo pede RDT e chama /buyerInfo com o RDT', function () {
    ordersExtrasFakeRdt('/orders/v0/orders/701-1111111-1111111/buyerInfo', ['payload' => ['BuyerEmail' => 'x@y.com']]);

    $resp = ordersExtras()->getOrderBuyerInfo('701-1111111-1111111');

    expect($resp['payload']['BuyerEmail'])->toBe('x@y.com');
    ordersExtrasAssertRdtRequested('GET', '/orders/v0/orders/701-1111111-1111111/buyerInfo');
    ordersExtrasAssertSent('GET', ORD.'/buyerInfo', token: 'Atz.rdt|abc');
});

it('getOrderAddress pede RDT e chama /address', function () {
    ordersExtrasFakeRdt('/orders/v0/orders/701-1111111-1111111/address', ['payload' => ['ShippingAddress' => ['City' => 'SP']]]);

    $resp = ordersExtras()->getOrderAddress('701-1111111-1111111');

    expect($resp['payload']['ShippingAddress']['City'])->toBe('SP');
    ordersExtrasAssertRdtRequested('GET', '/orders/v0/orders/701-1111111-1111111/address');
    ordersExtrasAssertSent('GET', ORD.'/address', token: 'Atz.rdt|abc');
});

it('getOrderItemsBuyerInfo pede RDT e pagina por NextToken', function () {
    ordersExtrasFakeRdt('/orders/v0/orders/701-1111111-1111111/orderItems/buyerInfo', ['payload' => ['OrderItems' => []]]);

    ordersExtras()->getOrderItemsBuyerInfo('701-1111111-1111111', ['NextToken' => 'N2']);

    ordersExtrasAssertRdtRequested('GET', '/orders/v0/orders/701-1111111-1111111/orderItems/buyerInfo');
    ordersExtrasAssertSent('GET', ORD.'/orderItems/buyerInfo?NextToken=N2', token: 'Atz.rdt|abc');
});

it('getOrderRegulatedInfo pede RDT e chama /regulatedInfo', function () {
    ordersExtrasFakeRdt('/orders/v0/orders/701-1111111-1111111/regulatedInfo', ['payload' => ['RequiresDosageLabel' => false]]);

    ordersExtras()->getOrderRegulatedInfo('701-1111111-1111111');

    ordersExtrasAssertRdtRequested('GET', '/orders/v0/orders/701-1111111-1111111/regulatedInfo');
    ordersExtrasAssertSent('GET', ORD.'/regulatedInfo', token: 'Atz.rdt|abc');
});

it('updateVerificationStatus PATCH /regulatedInfo com o body embrulhado', function () {
    Http::fake([ORD.'/regulatedInfo' => Http::response([], 204)]);

    ordersExtras()->updateVerificationStatus('701-1111111-1111111', ['status' => 'Approved', 'externalReviewerId' => 'rev-1']);

    ordersExtrasAssertSent('PATCH', ORD.'/regulatedInfo', ['regulatedOrderVerificationStatus' => ['status' => 'Approved', 'externalReviewerId' => 'rev-1']]);
});

it('updateShipmentStatus POST /shipment com marketplaceId + shipmentStatus + extras', function () {
    Http::fake([ORD.'/shipment' => Http::response([], 204)]);

    ordersExtras()->updateShipmentStatus('701-1111111-1111111', 'A2Q3Y263D00KWC', 'ReadyForPickup', ['orderItems' => [['orderItemId' => '1', 'quantity' => 1]]]);

    ordersExtrasAssertSent('POST', ORD.'/shipment', ['marketplaceId' => 'A2Q3Y263D00KWC', 'shipmentStatus' => 'ReadyForPickup', 'orderItems' => [['orderItemId' => '1', 'quantity' => 1]]]);
});

it('confirmShipment POST /shipmentConfirmation com packageDetail', function () {
    Http::fake([ORD.'/shipmentConfirmation' => Http::response([], 204)]);

    $pkg = ['packageReferenceId' => 'PKG1', 'carrierCode' => 'Correios', 'trackingNumber' => 'BR123', 'shipDate' => '2026-09-01T12:00:00Z', 'orderItems' => [['orderItemId' => '1', 'quantity' => 1]]];
    ordersExtras()->confirmShipment('701-1111111-1111111', 'A2Q3Y263D00KWC', $pkg);

    ordersExtrasAssertSent('POST', ORD.'/shipmentConfirmation', ['marketplaceId' => 'A2Q3Y263D00KWC', 'packageDetail' => $pkg]);
});

it('OrdersV2026::searchOrders GET /orders/2026-01-01/orders com arrays csv', function () {
    Http::fake(['*/orders/2026-01-01/orders?*' => Http::response(['orders' => [], 'pagination' => []])]);

    $integration = new FakeIntegration(accessToken: 'Atza|valid-token', refreshToken: 'Atzr|refresh', settings: ['client_id' => 'c', 'client_secret' => 's', 'marketplace_id' => 'A2Q3Y263D00KWC'], active: true, expired: false);
    (new OrdersV2026(new Client($integration)))->searchOrders(['marketplaceIds' => ['A2Q3Y263D00KWC'], 'includedData' => ['PROCEEDS', 'EXPENSE'], 'maxResultsPerPage' => 50]);

    ordersExtrasAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/orders/2026-01-01/orders?marketplaceIds=A2Q3Y263D00KWC&includedData=PROCEEDS%2CEXPENSE&maxResultsPerPage=50');
});

it('OrdersV2026::getOrder GET /orders/2026-01-01/orders/{id}; restricted usa RDT', function () {
    ordersExtrasFakeRdt('/orders/2026-01-01/orders/701-1111111-1111111', ['order' => ['orderId' => '701-1111111-1111111']]);

    $integration = new FakeIntegration(accessToken: 'Atza|valid-token', refreshToken: 'Atzr|refresh', settings: ['client_id' => 'c', 'client_secret' => 's', 'marketplace_id' => 'A2Q3Y263D00KWC'], active: true, expired: false);
    $resp = (new OrdersV2026(new Client($integration)))->getOrder('701-1111111-1111111', ['BUYER', 'RECIPIENT'], restricted: true);

    expect($resp['order']['orderId'])->toBe('701-1111111-1111111');
    ordersExtrasAssertRdtRequested('GET', '/orders/2026-01-01/orders/701-1111111-1111111');
    ordersExtrasAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/orders/2026-01-01/orders/701-1111111-1111111?includedData=BUYER%2CRECIPIENT', token: 'Atz.rdt|abc');
});

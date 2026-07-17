<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Endpoints\Orders;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

/**
 * Helper — Integration Amazon com access_token valido (expired:false →
 * TokenRefresher pula o hit no LWA via guard isExpired).
 */
function amazonClientIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: [
            'client_id' => 'test-client',
            'client_secret' => 'test-secret',
            'marketplace_id' => 'A2Q3Y263D00KWC',
        ],
        active: true,
        expired: false,
    );
}

it('getOrder parses the payload header from GET /orders/{id}', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/orders/v0/orders/701-6247639-1914647' => Http::response([
            'payload' => [
                'AmazonOrderId' => '701-6247639-1914647',
                'OrderStatus' => 'Shipped',
                'FulfillmentChannel' => 'AFN',
                'MarketplaceId' => 'A2Q3Y263D00KWC',
                'PurchaseDate' => '2025-09-01T10:00:00Z',
                'LastUpdateDate' => '2025-09-03T15:00:00Z',
                'OrderTotal' => ['Amount' => '99.90', 'CurrencyCode' => 'BRL'],
            ],
        ], 200),
    ]);

    $orders = MarketPlaces::Amazon()->client(amazonClientIntegration())->orders();

    $header = $orders->getOrder('701-6247639-1914647');

    expect($header->amazonOrderId)->toBe('701-6247639-1914647');
    expect($header->orderStatus)->toBe('Shipped');
    expect($header->fulfillmentChannel)->toBe('AFN');
    expect($header->orderTotal?->amount)->toBe('99.90');
});

it('getOrderItems returns the OrderItems list from GET /orders/{id}/orderItems', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/orders/v0/orders/701-6247639-1914647/orderItems' => Http::response([
            'payload' => [
                'AmazonOrderId' => '701-6247639-1914647',
                'OrderItems' => [
                    [
                        'OrderItemId' => '1111',
                        'SellerSKU' => 'SKU-A',
                        'Title' => 'Whey Protein 900g',
                        'QuantityOrdered' => 2,
                        'ItemPrice' => ['Amount' => '199.80', 'CurrencyCode' => 'BRL'],
                    ],
                ],
            ],
        ], 200),
    ]);

    $orders = MarketPlaces::Amazon()->client(amazonClientIntegration())->orders();

    $items = $orders->getOrderItems('701-6247639-1914647');

    expect($items)->toHaveCount(1);
    expect($items[0]->sellerSku)->toBe('SKU-A');
    expect($items[0]->quantityOrdered)->toBe(2);
});

it('getOrder returns [] on 404 (order não existe mais)', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/orders/v0/orders/000-0000000-0000000' => Http::response([
            'errors' => [['code' => 'NotFound', 'message' => 'Order not found']],
        ], 404),
    ]);

    $orders = MarketPlaces::Amazon()->client(amazonClientIntegration())->orders();

    expect($orders->getOrder('000-0000000-0000000')->amazonOrderId)->toBeNull();
});

it('getOrderItems returns [] when payload has no OrderItems', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/orders/v0/orders/701-0000000-0000000/orderItems' => Http::response([
            'payload' => ['AmazonOrderId' => '701-0000000-0000000'],
        ], 200),
    ]);

    $orders = MarketPlaces::Amazon()->client(amazonClientIntegration())->orders();

    expect($orders->getOrderItems('701-0000000-0000000'))->toBe([]);
});

it('orders() factory on the Amazon client returns an Orders endpoint', function () {
    $client = MarketPlaces::Amazon()->client(amazonClientIntegration());

    expect($client->orders())->toBeInstanceOf(Orders::class);
});

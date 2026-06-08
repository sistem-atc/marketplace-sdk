<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function mlOrdersIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config([
        'marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.access_token_ttl_seconds' => 21600,
        'mercadolivre.default_site_id' => 'MLB',
    ]);
    Http::preventStrayRequests();
});

describe('MarketPlaces::MercadoLivre()->orders()->get($orderId)', function () {
    it('devolve o payload completo do /orders/{id} com keys que o projector consome', function () {
        Http::fake([
            'api.mercadolibre.com/orders/2000016428434244' => Http::response([
                'id' => 2000016428434244,
                'pack_id' => null,
                'status' => 'paid',
                'status_detail' => null,
                'date_created' => '2026-04-15T10:00:00.000-03:00',
                'date_last_updated' => '2026-04-16T12:00:00.000-03:00',
                'total_amount' => 199.90,
                'paid_amount' => 199.90,
                'shipping_cost' => 0,
                'order_items' => [
                    [
                        'item' => [
                            'id' => 'MLB-1',
                            'title' => 'Whey 900g',
                            'seller_sku' => 'SKU-A',
                            'seller_custom_field' => 'SKU-A',
                        ],
                        'quantity' => 1,
                        'unit_price' => 199.90,
                        'sale_fee' => 20.0,
                    ],
                ],
                'payments' => [
                    [
                        'id' => 7001,
                        'status' => 'approved',
                        'payment_type' => 'credit_card',
                        'date_approved' => '2026-04-15T10:05:00.000-03:00',
                    ],
                ],
                'buyer' => ['id' => 333, 'nickname' => 'BUYER_TEST'],
                'seller' => ['id' => 999],
                'shipping' => ['id' => 555],
                'coupon' => ['amount' => 0],
                'tags' => ['paid'],
            ], 200),
        ]);

        $integration = mlOrdersIntegration();
        $payload = MarketPlaces::MercadoLivre()->orders($integration)->get('2000016428434244');

        expect($payload)->toHaveKeys([
            'id', 'status', 'date_created', 'date_last_updated',
            'total_amount', 'shipping_cost', 'order_items',
            'payments', 'buyer', 'seller', 'shipping', 'coupon', 'tags',
        ]);
        expect($payload['id'])->toBe(2000016428434244);
        expect($payload['status'])->toBe('paid');
        expect($payload['order_items'][0]['item']['seller_sku'])->toBe('SKU-A');
        expect($payload['payments'][0]['status'])->toBe('approved');
        expect($payload['buyer']['nickname'])->toBe('BUYER_TEST');
    });

    it('aceita orderId int alem de string (numeros longos ja no integer-safe range)', function () {
        Http::fake([
            'api.mercadolibre.com/orders/*' => Http::response([
                'id' => 2000016428434244,
                'status' => 'paid',
            ], 200),
        ]);

        $integration = mlOrdersIntegration();
        $payload = MarketPlaces::MercadoLivre()->orders($integration)->get(2000016428434244);

        expect($payload['id'])->toBe(2000016428434244);
        Http::assertSent(fn ($req) => str_contains($req->url(), '/orders/2000016428434244'));
    });

    it('estoura MercadoLivreRequestException com status 404 quando order nao existe', function () {
        Http::fake([
            'api.mercadolibre.com/orders/9999999999999999' => Http::response([
                'message' => 'Order not found',
                'error' => 'not_found',
            ], 404),
        ]);

        $integration = mlOrdersIntegration();

        expect(fn () => MarketPlaces::MercadoLivre()->orders($integration)->get('9999999999999999'))
            ->toThrow(MercadoLivreRequestException::class);

        try {
            MarketPlaces::MercadoLivre()->orders($integration)->get('9999999999999999');
        } catch (MercadoLivreRequestException $e) {
            expect($e->status())->toBe(404);
        }
    });

    it('estoura MercadoLivreRequestException com status 429 quando rate limit batido', function () {
        Http::fake([
            'api.mercadolibre.com/orders/2000000000000001' => Http::response([
                'message' => 'too many requests',
                'error' => 'too_many_requests',
            ], 429),
        ]);

        $integration = mlOrdersIntegration();

        try {
            MarketPlaces::MercadoLivre()->orders($integration)->get('2000000000000001');
            throw new RuntimeException('deveria ter estourado 429');
        } catch (MercadoLivreRequestException $e) {
            expect($e->status())->toBe(429);
        }
    });

    it('retenta uma vez em 401 (token revogado) via BaseMethods + refresh forcado', function () {
        Http::fakeSequence('api.mercadolibre.com/orders/2000000000000002')
            ->push(['message' => 'invalid_token'], 401)
            ->push([
                'id' => 2000000000000002,
                'status' => 'paid',
            ], 200);

        $integration = mlOrdersIntegration();
        $payload = MarketPlaces::MercadoLivre()->orders($integration)->get('2000000000000002');

        expect($payload['id'])->toBe(2000000000000002);
        Http::assertSentCount(2);
    });
});

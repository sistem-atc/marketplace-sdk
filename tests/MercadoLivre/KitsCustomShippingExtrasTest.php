<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\UserProduct\UserProductMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Shipment\ShipmentMethods;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Promotion\PromotionMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function kitsShipExtrasIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function kitsShipExtrasOrders(): OrderMethods
{
    $integration = kitsShipExtrasIntegration();

    return new OrderMethods(HttpClientFactory::make($integration), $integration);
}

function kitsShipExtrasUserProducts(): UserProductMethods
{
    $integration = kitsShipExtrasIntegration();

    return new UserProductMethods(HttpClientFactory::make($integration), $integration);
}

function kitsShipExtrasShipments(): ShipmentMethods
{
    $integration = kitsShipExtrasIntegration();

    return new ShipmentMethods(HttpClientFactory::make($integration), $integration);
}

function kitsShipExtrasPromotions(): PromotionMethods
{
    $integration = kitsShipExtrasIntegration();

    return new PromotionMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL (todos os pedaços) + Bearer + body/headers da única
 * requisição disparada.
 *
 * @param  array<int,string>  $urlParts
 * @param  array<int,string>  $bodyParts
 * @param  array<string,string>  $headers
 */
function kitsShipExtrasAssertSent(string $method, array $urlParts, array $bodyParts = [], array $headers = []): void
{
    Http::assertSent(function ($req) use ($method, $urlParts, $bodyParts, $headers) {
        if ($req->method() !== $method || $req->header('Authorization')[0] !== 'Bearer ml-bearer') {
            return false;
        }
        foreach ($urlParts as $part) {
            if (! str_contains($req->url(), $part)) {
                return false;
            }
        }
        foreach ($bodyParts as $part) {
            if (! str_contains($req->body(), $part)) {
                return false;
            }
        }
        foreach ($headers as $name => $value) {
            if (($req->header($name)[0] ?? null) !== $value) {
                return false;
            }
        }

        return true;
    });
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

describe('OrderMethods::bundle', function () {
    it('GET /orders/{id}/bundle', function () {
        Http::fake(['api.mercadolibre.com/orders/2000012907378394/bundle' => Http::response(['bundles' => []])]);

        $result = kitsShipExtrasOrders()->bundle(2000012907378394);

        expect($result)->toBe(['bundles' => []]);

        kitsShipExtrasAssertSent('GET', ['/orders/2000012907378394/bundle']);
    });
});

describe('UserProductMethods — kits e estoque selling_address', function () {
    it('bundles: GET /user-products/{id}/bundles', function () {
        Http::fake(['api.mercadolibre.com/user-products/MLBU1/bundles' => Http::response(['bundles' => ['MLBU9']])]);

        $result = kitsShipExtrasUserProducts()->bundles('MLBU1');

        expect($result['bundles'])->toBe(['MLBU9']);

        kitsShipExtrasAssertSent('GET', ['/user-products/MLBU1/bundles']);
    });

    it('updateSellingAddressStock: PUT stock/type/selling_address com x-version', function () {
        Http::fake(['api.mercadolibre.com/user-products/MLBU1/stock/type/selling_address' => Http::response('')]);

        $result = kitsShipExtrasUserProducts()->updateSellingAddressStock('MLBU1', 10, 3);

        expect($result)->toBe([]);

        kitsShipExtrasAssertSent('PUT', ['/user-products/MLBU1/stock/type/selling_address'], ['"quantity":10'], ['x-version' => '3']);
    });
});

describe('ShipmentMethods::updateCustomShipment', function () {
    it('PUT /shipments/{id} com tracking/status/speed', function () {
        Http::fake(['api.mercadolibre.com/shipments/4545' => Http::response(['ok' => true])]);

        $result = kitsShipExtrasShipments()->updateCustomShipment(4545, ['status' => 'shipped', 'tracking_number' => '1234', 'receiver_id' => 12345678, 'speed' => 120]);

        kitsShipExtrasAssertSent('PUT', ['/shipments/4545'], ['"status":"shipped"', '"tracking_number":"1234"', '"speed":120']);
    });
});

describe('PromotionMethods — cupom do vendedor', function () {
    it('createCouponCampaign: POST /seller-promotions/promotions com promotion_type SELLER_COUPON_CAMPAIGN', function () {
        Http::fake(['api.mercadolibre.com/seller-promotions/promotions*' => Http::response(['id' => 'C-MLB1'])]);

        $result = kitsShipExtrasPromotions()->createCouponCampaign(['name' => 'Cupom 10', 'start_date' => '2026-09-01T00:00:00Z']);

        kitsShipExtrasAssertSent('POST', ['/seller-promotions/promotions', 'app_version=v2'], ['"promotion_type":"SELLER_COUPON_CAMPAIGN"', '"name":"Cupom 10"']);
    });

    it('getPromotion aceita SELLER_COUPON_CAMPAIGN na query', function () {
        Http::fake(['api.mercadolibre.com/seller-promotions/promotions/C-MLB1*' => Http::response(['ok' => true])]);

        $result = kitsShipExtrasPromotions()->getPromotion('C-MLB1', 'SELLER_COUPON_CAMPAIGN');

        kitsShipExtrasAssertSent('GET', ['/seller-promotions/promotions/C-MLB1', 'promotion_type=SELLER_COUPON_CAMPAIGN', 'app_version=v2']);
    });

    it('deletePromotion aceita SELLER_COUPON_CAMPAIGN', function () {
        Http::fake(['api.mercadolibre.com/seller-promotions/promotions/C-MLB1*' => Http::response([])]);

        $result = kitsShipExtrasPromotions()->deletePromotion('C-MLB1', 'SELLER_COUPON_CAMPAIGN');

        kitsShipExtrasAssertSent('DELETE', ['/seller-promotions/promotions/C-MLB1', 'promotion_type=SELLER_COUPON_CAMPAIGN']);
    });
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Marketing\MarketingMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function marketingExtrasIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );
}

function marketingExtrasMethods(): MarketingMethods
{
    $integration = marketingExtrasIntegration();

    return new MarketingMethods(HttpClientFactory::make($integration), $integration);
}

/** Verbo + path + assinatura de shop (partner_id, timestamp, sign, access_token, shop_id). */
function marketingExtrasAssertShopCall(string $verb, string $path, ?callable $extra = null): void
{
    Http::assertSent(function ($req) use ($verb, $path, $extra) {
        parse_str((string) parse_url($req->url(), PHP_URL_QUERY), $q);

        return $req->method() === $verb
            && str_contains($req->url(), $path)
            && ($q['partner_id'] ?? null) === '2030136'
            && ($q['shop_id'] ?? null) === '999999'
            && isset($q['timestamp'], $q['sign'], $q['access_token'])
            && ($extra === null || $extra($req, $q));
    });
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

describe('MarketingMethods — discount (escrita)', function () {
    it('addDiscount POST com nome e periodo no body', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/discount/add_discount*' => Http::response(['response' => ['discount_id' => 1]])]);

        $resp = marketingExtrasMethods()->addDiscount(discountName: 'Promo', startTime: 1700000000, endTime: 1700100000);

        expect($resp['discount_id'])->toBe(1);
        marketingExtrasAssertShopCall('POST', '/api/v2/discount/add_discount', fn ($req) => $req['discount_name'] === 'Promo'
            && $req['start_time'] === 1700000000 && $req['end_time'] === 1700100000);
    });

    it('addDiscountItem POST com discount_id + item_list', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/discount/add_discount_item*' => Http::response(['response' => ['count' => 1]])]);

        marketingExtrasMethods()->addDiscountItem(discountId: 55, itemList: [['item_id' => 9, 'purchase_limit' => 0, 'item_promotion_price' => 10.5]]);

        marketingExtrasAssertShopCall('POST', '/api/v2/discount/add_discount_item', fn ($req) => $req['discount_id'] === 55
            && $req['item_list'][0]['item_id'] === 9);
    });

    it('deleteDiscount POST com discount_id', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/discount/delete_discount*' => Http::response(['response' => ['discount_id' => 55]])]);

        marketingExtrasMethods()->deleteDiscount(55);

        marketingExtrasAssertShopCall('POST', '/api/v2/discount/delete_discount', fn ($req) => $req['discount_id'] === 55);
    });

    it('deleteDiscountItem so manda model_id quando informado', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/discount/delete_discount_item*' => Http::response(['response' => []])]);

        marketingExtrasMethods()->deleteDiscountItem(discountId: 55, itemId: 9);
        marketingExtrasAssertShopCall('POST', '/api/v2/discount/delete_discount_item', fn ($req) => $req['item_id'] === 9 && ! isset($req['model_id']));

        marketingExtrasMethods()->deleteDiscountItem(discountId: 55, itemId: 9, modelId: 3);
        marketingExtrasAssertShopCall('POST', '/api/v2/discount/delete_discount_item', fn ($req) => ($req['model_id'] ?? null) === 3);
    });

    it('updateDiscount envia apenas campos informados', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/discount/update_discount*' => Http::response(['response' => ['discount_id' => 55]])]);

        marketingExtrasMethods()->updateDiscount(discountId: 55, endTime: 1700200000);

        marketingExtrasAssertShopCall('POST', '/api/v2/discount/update_discount', fn ($req) => $req['discount_id'] === 55
            && $req['end_time'] === 1700200000 && ! isset($req['discount_name']) && ! isset($req['start_time']));
    });

    it('updateDiscountItem POST com item_list', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/discount/update_discount_item*' => Http::response(['response' => []])]);

        marketingExtrasMethods()->updateDiscountItem(55, [['item_id' => 9, 'item_promotion_price' => 8.9]]);

        marketingExtrasAssertShopCall('POST', '/api/v2/discount/update_discount_item', fn ($req) => $req['item_list'][0]['item_promotion_price'] === 8.9);
    });

    it('endDiscount POST com discount_id', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/discount/end_discount*' => Http::response(['response' => ['discount_id' => 55]])]);

        marketingExtrasMethods()->endDiscount(55);

        marketingExtrasAssertShopCall('POST', '/api/v2/discount/end_discount', fn ($req) => $req['discount_id'] === 55);
    });

    it('getSipDiscounts GET; region na query so quando informada', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/discount/get_sip_discounts*' => Http::response(['response' => ['sip_discount_list' => []]])]);

        marketingExtrasMethods()->getSipDiscounts();
        marketingExtrasAssertShopCall('GET', '/api/v2/discount/get_sip_discounts', fn ($req, $q) => ! isset($q['region']));

        marketingExtrasMethods()->getSipDiscounts('MY');
        marketingExtrasAssertShopCall('GET', '/api/v2/discount/get_sip_discounts', fn ($req, $q) => ($q['region'] ?? null) === 'MY');
    });

    it('setSipDiscount POST com region + rate', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/discount/set_sip_discount*' => Http::response(['response' => []])]);

        marketingExtrasMethods()->setSipDiscount(region: 'MY', sipDiscountRate: 15);

        marketingExtrasAssertShopCall('POST', '/api/v2/discount/set_sip_discount', fn ($req) => $req['region'] === 'MY' && $req['sip_discount_rate'] === 15);
    });

    it('deleteSipDiscount POST com region', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/discount/delete_sip_discount*' => Http::response(['response' => []])]);

        marketingExtrasMethods()->deleteSipDiscount('MY');

        marketingExtrasAssertShopCall('POST', '/api/v2/discount/delete_sip_discount', fn ($req) => $req['region'] === 'MY');
    });
});

describe('MarketingMethods — voucher (detalhe + escrita)', function () {
    it('getVoucher GET com voucher_id na query', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/voucher/get_voucher*' => Http::response(['response' => ['voucher_id' => 77, 'voucher_code' => 'ABC']])]);

        $resp = marketingExtrasMethods()->getVoucher(77);

        expect($resp['voucher_code'])->toBe('ABC');
        marketingExtrasAssertShopCall('GET', '/api/v2/voucher/get_voucher', fn ($req, $q) => ($q['voucher_id'] ?? null) === '77');
    });

    it('addVoucher POST com payload completo', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/voucher/add_voucher*' => Http::response(['response' => ['voucher_id' => 78]])]);

        $resp = marketingExtrasMethods()->addVoucher([
            'voucher_name' => 'Frete', 'voucher_code' => 'FRETE10', 'start_time' => 1, 'end_time' => 2,
            'voucher_type' => 1, 'reward_type' => 1, 'usage_quantity' => 100, 'min_basket_price' => 50.0, 'discount_amount' => 10.0,
        ]);

        expect($resp['voucher_id'])->toBe(78);
        marketingExtrasAssertShopCall('POST', '/api/v2/voucher/add_voucher', fn ($req) => $req['voucher_code'] === 'FRETE10' && $req['reward_type'] === 1);
    });

    it('updateVoucher mescla voucher_id com os campos', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/voucher/update_voucher*' => Http::response(['response' => ['voucher_id' => 78]])]);

        marketingExtrasMethods()->updateVoucher(78, ['usage_quantity' => 200]);

        marketingExtrasAssertShopCall('POST', '/api/v2/voucher/update_voucher', fn ($req) => $req['voucher_id'] === 78 && $req['usage_quantity'] === 200);
    });

    it('deleteVoucher POST com voucher_id', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/voucher/delete_voucher*' => Http::response(['response' => ['voucher_id' => 78]])]);

        marketingExtrasMethods()->deleteVoucher(78);

        marketingExtrasAssertShopCall('POST', '/api/v2/voucher/delete_voucher', fn ($req) => $req['voucher_id'] === 78);
    });

    it('endVoucher POST com voucher_id', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/voucher/end_voucher*' => Http::response(['response' => ['voucher_id' => 78]])]);

        marketingExtrasMethods()->endVoucher(78);

        marketingExtrasAssertShopCall('POST', '/api/v2/voucher/end_voucher', fn ($req) => $req['voucher_id'] === 78);
    });
});

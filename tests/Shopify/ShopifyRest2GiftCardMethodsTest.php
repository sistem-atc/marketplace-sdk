<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\GiftCard\GiftCardMethods;

require_once __DIR__.'/Helpers/ShopifyRest2Helpers.php';

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('Shopify GiftCardMethods', function () {
    it('list', fn () => shopifyRest2Call(fn () => shopifyRest2Make(GiftCardMethods::class)->list(['status' => 'enabled']), 'GET', 'gift_cards.json?status=enabled'));
    it('count', fn () => shopifyRest2Call(fn () => shopifyRest2Make(GiftCardMethods::class)->count(['status' => 'disabled']), 'GET', 'gift_cards/count.json?status=disabled', null, ['count' => 0]));
    it('get', fn () => shopifyRest2Call(fn () => shopifyRest2Make(GiftCardMethods::class)->get(8), 'GET', 'gift_cards/8.json'));
    it('search', fn () => shopifyRest2Call(fn () => shopifyRest2Make(GiftCardMethods::class)->search('last_characters:mnop', ['limit' => 1]), 'GET', 'gift_cards/search.json?query=last_characters:mnop&limit=1'));
    it('create embrulha em gift_card', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(GiftCardMethods::class)->create(['initial_value' => '25.00']),
        'POST', 'gift_cards.json', ['gift_card' => ['initial_value' => '25.00']],
    ));
    it('update embrulha em gift_card', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(GiftCardMethods::class)->update(8, ['note' => 'n']),
        'PUT', 'gift_cards/8.json', ['gift_card' => ['note' => 'n']],
    ));
    it('disable', fn () => shopifyRest2Call(fn () => shopifyRest2Make(GiftCardMethods::class)->disable(8), 'POST', 'gift_cards/8/disable.json', ['gift_card' => ['id' => 8]]));
    it('listAdjustments', fn () => shopifyRest2Call(fn () => shopifyRest2Make(GiftCardMethods::class)->listAdjustments(8), 'GET', 'gift_cards/8/adjustments.json'));
    it('getAdjustment', fn () => shopifyRest2Call(fn () => shopifyRest2Make(GiftCardMethods::class)->getAdjustment(8, 2), 'GET', 'gift_cards/8/adjustments/2.json'));
    it('createAdjustment embrulha em adjustment', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(GiftCardMethods::class)->createAdjustment(8, ['amount' => 10, 'note' => 'bonus']),
        'POST', 'gift_cards/8/adjustments.json', ['adjustment' => ['amount' => 10, 'note' => 'bonus']],
    ));
    it('each segue o page_info', function () {
        Http::fake([
            '*/gift_cards.json*' => Http::sequence()
                ->push(['gift_cards' => [['id' => 1]]], 200, ['Link' => '<https://loja-teste.myshopify.com/admin/api/2024-04/gift_cards.json?limit=250&page_info=C2>; rel="next"'])
                ->push(['gift_cards' => [['id' => 2]]], 200, []),
        ]);
        expect(array_column(iterator_to_array(shopifyRest2Make(GiftCardMethods::class)->each()), 'id'))->toBe([1, 2]);
    });
});

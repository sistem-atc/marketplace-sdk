<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Inventory\InventoryMethods;

require_once __DIR__.'/Helpers/ShopifyRest2Helpers.php';

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('Shopify InventoryMethods', function () {
    it('listItems manda ids CSV', fn () => shopifyRest2Call(fn () => shopifyRest2Make(InventoryMethods::class)->listItems([1, 2]), 'GET', 'inventory_items.json?ids=1,2'));
    it('getItem', fn () => shopifyRest2Call(fn () => shopifyRest2Make(InventoryMethods::class)->getItem(1), 'GET', 'inventory_items/1.json'));
    it('updateItem embrulha em inventory_item', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(InventoryMethods::class)->updateItem(1, ['sku' => 'ABC', 'cost' => '10.00']),
        'PUT', 'inventory_items/1.json', ['inventory_item' => ['sku' => 'ABC', 'cost' => '10.00']],
    ));
    it('listLevels', fn () => shopifyRest2Call(fn () => shopifyRest2Make(InventoryMethods::class)->listLevels(['location_ids' => '5']), 'GET', 'inventory_levels.json?location_ids=5'));
    it('adjustLevel sem embrulho', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(InventoryMethods::class)->adjustLevel(1, 5, -2),
        'POST', 'inventory_levels/adjust.json', ['inventory_item_id' => 1, 'location_id' => 5, 'available_adjustment' => -2],
    ));
    it('connectLevel', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(InventoryMethods::class)->connectLevel(1, 5, true),
        'POST', 'inventory_levels/connect.json', ['inventory_item_id' => 1, 'location_id' => 5, 'relocate_if_necessary' => true],
    ));
    it('setLevel', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(InventoryMethods::class)->setLevel(1, 5, 42),
        'POST', 'inventory_levels/set.json', ['inventory_item_id' => 1, 'location_id' => 5, 'available' => 42, 'disconnect_if_necessary' => false],
    ));
    it('deleteLevel usa query', fn () => shopifyRest2Call(fn () => shopifyRest2Make(InventoryMethods::class)->deleteLevel(1, 5), 'DELETE', 'inventory_levels.json?inventory_item_id=1&location_id=5'));
    it('listLocations', fn () => shopifyRest2Call(fn () => shopifyRest2Make(InventoryMethods::class)->listLocations(), 'GET', 'locations.json'));
    it('countLocations', fn () => shopifyRest2Call(fn () => shopifyRest2Make(InventoryMethods::class)->countLocations(), 'GET', 'locations/count.json', null, ['count' => 1]));
    it('getLocation', fn () => shopifyRest2Call(fn () => shopifyRest2Make(InventoryMethods::class)->getLocation(5), 'GET', 'locations/5.json'));
    it('locationInventoryLevels', fn () => shopifyRest2Call(fn () => shopifyRest2Make(InventoryMethods::class)->locationInventoryLevels(5), 'GET', 'locations/5/inventory_levels.json'));
    it('locationsForMove', fn () => shopifyRest2Call(fn () => shopifyRest2Make(InventoryMethods::class)->locationsForMove(77), 'GET', 'fulfillment_orders/77/locations_for_move.json'));
    it('eachLevels segue o page_info', function () {
        Http::fake([
            '*/inventory_levels.json*' => Http::sequence()
                ->push(['inventory_levels' => [['inventory_item_id' => 1]]], 200, ['Link' => '<https://loja-teste.myshopify.com/admin/api/2024-04/inventory_levels.json?limit=250&page_info=C2>; rel="next"'])
                ->push(['inventory_levels' => [['inventory_item_id' => 2]]], 200, []),
        ]);
        expect(iterator_to_array(shopifyRest2Make(InventoryMethods::class)->eachLevels(['location_ids' => '5'])))->toHaveCount(2);
    });
});

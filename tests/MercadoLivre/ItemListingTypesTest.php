<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Item\ItemMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function itemListingTypesIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function itemListingTypesMethods(): ItemMethods
{
    $integration = itemListingTypesIntegration();

    return new ItemMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL (todos os pedaços) + Bearer + body/headers da única
 * requisição disparada.
 *
 * @param  array<int,string>  $urlParts
 * @param  array<int,string>  $bodyParts
 * @param  array<string,string>  $headers
 */
function itemListingTypesAssertSent(string $method, array $urlParts, array $bodyParts = [], array $headers = []): void
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
    Http::fake(['api.mercadolibre.com/*' => Http::response(['ok' => true], 200)]);
});

describe('ItemMethods — tipos de publicação', function () {
    it('siteListingTypes GET /sites/{site}/listing_types', function () {
        itemListingTypesMethods()->siteListingTypes('MLB');
        itemListingTypesAssertSent('GET', ['/sites/MLB/listing_types']);
    });

    it('siteListingType GET /sites/{site}/listing_types/{type}', function () {
        itemListingTypesMethods()->siteListingType('gold_special', 'MLA');
        itemListingTypesAssertSent('GET', ['/sites/MLA/listing_types/gold_special']);
    });

    it('listingExposures GET /sites/{site}/listing_exposures', function () {
        itemListingTypesMethods()->listingExposures();
        itemListingTypesAssertSent('GET', ['/sites/MLB/listing_exposures']);
    });

    it('listingExposure GET /sites/{site}/listing_exposures/{id}', function () {
        itemListingTypesMethods()->listingExposure('high');
        itemListingTypesAssertSent('GET', ['/sites/MLB/listing_exposures/high']);
    });

    it('userAvailableListingTypes GET /users/{id}/available_listing_types?category_id', function () {
        itemListingTypesMethods()->userAvailableListingTypes(1234, 'MLB1055');
        itemListingTypesAssertSent('GET', ['/users/1234/available_listing_types', 'category_id=MLB1055']);
    });

    it('userAvailableListingType GET /users/{id}/available_listing_type/{type}?category_id', function () {
        itemListingTypesMethods()->userAvailableListingType(1234, 'free', 'MLB1055');
        itemListingTypesAssertSent('GET', ['/users/1234/available_listing_type/free', 'category_id=MLB1055']);
    });

    it('availableListingTypes GET /items/{id}/available_listing_types', function () {
        itemListingTypesMethods()->availableListingTypes('MLB1');
        itemListingTypesAssertSent('GET', ['/items/MLB1/available_listing_types']);
    });

    it('availableUpgrades GET /items/{id}/available_upgrades', function () {
        itemListingTypesMethods()->availableUpgrades('MLB1');
        itemListingTypesAssertSent('GET', ['/items/MLB1/available_upgrades']);
    });

    it('availableDowngrades GET /items/{id}/available_downgrades', function () {
        itemListingTypesMethods()->availableDowngrades('MLB1');
        itemListingTypesAssertSent('GET', ['/items/MLB1/available_downgrades']);
    });

    it('changeListingType POST /items/{id}/listing_type com body {id}', function () {
        itemListingTypesMethods()->changeListingType('MLB1', 'gold_pro');
        itemListingTypesAssertSent('POST', ['/items/MLB1/listing_type'], ['"id":"gold_pro"']);
    });
});

describe('ItemMethods — preços e PxQ', function () {
    it('prices GET /items/{id}/prices com show-all-prices + display_version', function () {
        itemListingTypesMethods()->prices('MLB1');
        itemListingTypesAssertSent('GET', ['/items/MLB1/prices', 'display_version=true'], [], ['show-all-prices' => 'true']);
    });

    it('prices sem show-all-prices nem display_version quando desligados', function () {
        itemListingTypesMethods()->prices('MLB1', showAllPrices: false, displayVersion: false);
        Http::assertSent(fn ($req) => str_ends_with($req->url(), '/items/MLB1/prices')
            && ! $req->hasHeader('show-all-prices'));
    });

    it('pricePerQuantity GET /items/{id}/prices/price-per-quantity', function () {
        itemListingTypesMethods()->pricePerQuantity('MLB1');
        itemListingTypesAssertSent('GET', ['/items/MLB1/prices/price-per-quantity']);
    });

    it('setPricePerQuantity POST com X-Version, body e remove-absolute-pxq', function () {
        itemListingTypesMethods()->setPricePerQuantity('MLB1', [
            ['type' => 'discount_percentage', 'percentage' => 3.63, 'conditions' => ['min_purchase_unit' => 2]],
        ], 13, removeAbsolutePxq: true);
        itemListingTypesAssertSent(
            'POST',
            ['/items/MLB1/prices/price-per-quantity', 'remove-absolute-pxq=true'],
            ['"price_per_quantity":[{"type":"discount_percentage","percentage":3.63'],
            ['X-Version' => '13']
        );
    });

    it('setPricePerQuantity com lista vazia remove faixas (sem query)', function () {
        itemListingTypesMethods()->setPricePerQuantity('MLB1', [], 12);
        Http::assertSent(fn ($req) => str_ends_with($req->url(), '/items/MLB1/prices/price-per-quantity')
            && $req->body() === '{"price_per_quantity":[]}'
            && $req->header('X-Version')[0] === '12');
    });

    it('setStandardQuantityPrices POST /items/{id}/prices/standard/quantity', function () {
        itemListingTypesMethods()->setStandardQuantityPrices('MLB1', [
            ['type' => 'standard', 'amount' => 399, 'currency_id' => 'BRL', 'amount_tax_inclusion_type' => 'net'],
        ], removePercentagePxq: true);
        itemListingTypesAssertSent(
            'POST',
            ['/items/MLB1/prices/standard/quantity', 'remove_percentage_pxq=true'],
            ['"prices":[{"type":"standard","amount":399,"currency_id":"BRL","amount_tax_inclusion_type":"net"}]']
        );
    });

    it('netPricesEligibility GET /business/v1/sites/{site}/users/{user}/items/{item}/options/net-prices/seller/eligibility', function () {
        itemListingTypesMethods()->netPricesEligibility(655590662, 'MLB4177849003');
        itemListingTypesAssertSent('GET', ['/business/v1/sites/MLB/users/655590662/items/MLB4177849003/options/net-prices/seller/eligibility']);
    });

    it('pricePerQuantityRecommendations POST /prices-per-quantity/v1/recommendations', function () {
        itemListingTypesMethods()->pricePerQuantityRecommendations('MLB4958146279', [2, 5, 10], 185.0);
        itemListingTypesAssertSent(
            'POST',
            ['/prices-per-quantity/v1/recommendations'],
            ['"item_id":"MLB4958146279"', '"range_item_quantities":[2,5,10]', '"standard_amount":185', '"currency":"BRL"']
        );
    });
});

describe('ItemMethods — catálogo', function () {
    it('catalogListingEligibility GET /items/{id}/catalog_listing_eligibility', function () {
        itemListingTypesMethods()->catalogListingEligibility('MLB1');
        itemListingTypesAssertSent('GET', ['/items/MLB1/catalog_listing_eligibility']);
    });

    it('catalogListingEligibilityMultiGet GET /multiget/catalog_listing_eligibility?ids', function () {
        itemListingTypesMethods()->catalogListingEligibilityMultiGet(['MLB1', 'MLB2']);
        itemListingTypesAssertSent('GET', ['/multiget/catalog_listing_eligibility', 'ids=MLB1%2CMLB2']);
    });

    it('catalogListingEligibilityMultiGet devolve [] sem ids e não chama a API', function () {
        expect(itemListingTypesMethods()->catalogListingEligibilityMultiGet([]))->toBe([]);
        Http::assertNothingSent();
    });

    it('priceToWin GET /items/{id}/price_to_win?version=v2', function () {
        itemListingTypesMethods()->priceToWin('MLB1');
        itemListingTypesAssertSent('GET', ['/items/MLB1/price_to_win', 'version=v2']);
    });

    it('createCatalogListing POST /items/catalog_listings com variation_id opcional', function () {
        itemListingTypesMethods()->createCatalogListing('MLM1477978125', 'MLM15996654', 174997747229);
        itemListingTypesAssertSent(
            'POST',
            ['/items/catalog_listings'],
            ['"item_id":"MLM1477978125"', '"catalog_product_id":"MLM15996654"', '"variation_id":174997747229']
        );
    });

    it('createCatalogListing sem variação não manda variation_id', function () {
        itemListingTypesMethods()->createCatalogListing('MLM1', 'MLM2');
        Http::assertSent(fn ($req) => ! str_contains($req->body(), 'variation_id'));
    });

    it('catalogForewarningDate GET /items/{id}/catalog_forewarning/date', function () {
        itemListingTypesMethods()->catalogForewarningDate('MLB1');
        itemListingTypesAssertSent('GET', ['/items/MLB1/catalog_forewarning/date']);
    });
});

describe('ItemMethods — migração user_product_listings', function () {
    it('validateUserProductListings GET /items/{id}/user_product_listings/validate', function () {
        itemListingTypesMethods()->validateUserProductListings('MLB1');
        itemListingTypesAssertSent('GET', ['/items/MLB1/user_product_listings/validate']);
    });

    it('migrateToUserProductListings POST /sites/{site}/items/user_product_listings', function () {
        itemListingTypesMethods()->migrateToUserProductListings('MLM1234', 'MLM');
        itemListingTypesAssertSent('POST', ['/sites/MLM/items/user_product_listings'], ['"item_id":"MLM1234"']);
    });

    it('migrationLiveListing GET /items/{id}/migration_live_listing', function () {
        itemListingTypesMethods()->migrationLiveListing('MLB1');
        itemListingTypesAssertSent('GET', ['/items/MLB1/migration_live_listing']);
    });
});

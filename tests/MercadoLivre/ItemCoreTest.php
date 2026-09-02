<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Item\ItemMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function itemCoreIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function itemCoreMethods(): ItemMethods
{
    $integration = itemCoreIntegration();

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
function itemCoreAssertSent(string $method, array $urlParts, array $bodyParts = [], array $headers = []): void
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

describe('ItemMethods — descrição', function () {
    it('description: GET /items/{id}/description', function () {
        Http::fake(['api.mercadolibre.com/items/MLB1/description' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->description('MLB1');

        expect($result)->toBe(['ok' => true]);

        itemCoreAssertSent('GET', ['/items/MLB1/description']);
    });

    it('createDescription: POST com plain_text', function () {
        Http::fake(['api.mercadolibre.com/items/MLB1/description' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->createDescription('MLB1', 'Texto');

        itemCoreAssertSent('POST', ['/items/MLB1/description'], ['"plain_text":"Texto"']);
    });

    it('updateDescription: PUT com api_version=2', function () {
        Http::fake(['api.mercadolibre.com/items/MLB1/description*' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->updateDescription('MLB1', 'Novo');

        itemCoreAssertSent('PUT', ['/items/MLB1/description', 'api_version=2'], ['"plain_text":"Novo"']);
    });
});

describe('ItemMethods — variações', function () {
    it('variations: GET lista', function () {
        Http::fake(['api.mercadolibre.com/items/MLB1/variations' => Http::response([['id' => 1]])]);

        $result = itemCoreMethods()->variations('MLB1');

        expect($result)->toBe([['id' => 1]]);

        itemCoreAssertSent('GET', ['/items/MLB1/variations']);
    });

    it('variation: GET uma variação', function () {
        Http::fake(['api.mercadolibre.com/items/MLB1/variations/55' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->variation('MLB1', 55);

        itemCoreAssertSent('GET', ['/items/MLB1/variations/55']);
    });

    it('createVariation: POST body da variação', function () {
        Http::fake(['api.mercadolibre.com/items/MLB1/variations' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->createVariation('MLB1', ['price' => 100, 'attribute_combinations' => [['id' => 'COLOR', 'value_id' => '52035']]]);

        itemCoreAssertSent('POST', ['/items/MLB1/variations'], ['"price":100', '"COLOR"']);
    });

    it('deleteVariation: DELETE', function () {
        Http::fake(['api.mercadolibre.com/items/MLB1/variations/55' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->deleteVariation('MLB1', 55);

        itemCoreAssertSent('DELETE', ['/items/MLB1/variations/55']);
    });
});

describe('ItemMethods — imagens', function () {
    it('uploadPicture: POST multipart em /pictures/items/upload', function () {
        Http::fake(['api.mercadolibre.com/pictures/items/upload' => Http::response(['id' => '123-MLA456'])]);

        $result = itemCoreMethods()->uploadPicture('bytes', 'foto.jpg');

        expect($result)->toBe(['id' => '123-MLA456']);

        itemCoreAssertSent('POST', ['/pictures/items/upload'], ['foto.jpg', 'bytes']);
    });

    it('addPicture: POST /items/{id}/pictures com id', function () {
        Http::fake(['api.mercadolibre.com/items/MLB1/pictures' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->addPicture('MLB1', pictureId: 'PIC1');

        itemCoreAssertSent('POST', ['/items/MLB1/pictures'], ['"id":"PIC1"']);
    });

    it('addPicture: POST com source (sem id)', function () {
        Http::fake(['api.mercadolibre.com/items/MLB1/pictures' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->addPicture('MLB1', source: 'https://x/y.jpg');

        itemCoreAssertSent('POST', ['/items/MLB1/pictures'], ['"source":"https:\\/\\/x\\/y.jpg"']);
    });

    it('pictureErrors: GET /pictures/{id}/errors', function () {
        Http::fake(['api.mercadolibre.com/pictures/970736-MLU1_092017/errors' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->pictureErrors('970736-MLU1_092017');

        itemCoreAssertSent('GET', ['/pictures/970736-MLU1_092017/errors']);
    });
});

describe('ItemMethods — relist / validate / preços standard', function () {
    it('relist: POST /items/{id}/relist', function () {
        Http::fake(['api.mercadolibre.com/items/MLB1/relist' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->relist('MLB1', ['price' => 550, 'quantity' => 1, 'listing_type_id' => 'gold_special']);

        itemCoreAssertSent('POST', ['/items/MLB1/relist'], ['"price":550', '"listing_type_id":"gold_special"']);
    });

    it('validate: POST /items/validate', function () {
        Http::fake(['api.mercadolibre.com/items/validate' => Http::response([])]);

        $result = itemCoreMethods()->validate(['title' => 'Teacup', 'price' => 10]);

        itemCoreAssertSent('POST', ['/items/validate'], ['"title":"Teacup"']);
    });

    it('setStandardPrices: POST /items/{id}/prices/standard', function () {
        Http::fake(['api.mercadolibre.com/items/MLB1/prices/standard' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->setStandardPrices('MLB1', [['amount' => 400, 'currency_id' => 'BRL', 'conditions' => ['context_restrictions' => ['channel_marketplace']]]]);

        itemCoreAssertSent('POST', ['/items/MLB1/prices/standard'], ['"prices":[', '"amount":400', 'channel_marketplace']);
    });
});

describe('ItemMethods — kits virtuais', function () {
    it('searchKitComponents: POST com query + body default', function () {
        Http::fake(['api.mercadolibre.com/users/77/kits/components/search*' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->searchKitComponents(77, [], ['searchText' => 'whey', 'limit' => 2]);

        itemCoreAssertSent('POST', ['/users/77/kits/components/search', 'searchText=whey', 'limit=2'], ['"active_channels":["marketplace"]']);
    });

    it('searchKitComponents: body custom vai como está', function () {
        Http::fake(['api.mercadolibre.com/users/77/kits/components/search*' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->searchKitComponents(77, ['main_product_id' => 'MLBU1', 'active_channels' => ['marketplace']]);

        itemCoreAssertSent('POST', ['/users/77/kits/components/search'], ['"main_product_id":"MLBU1"']);
    });

    it('createKit: POST /items/kits', function () {
        Http::fake(['api.mercadolibre.com/items/kits' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->createKit(['family_name' => 'Kit', 'bundle' => ['type' => 'kit']]);

        itemCoreAssertSent('POST', ['/items/kits'], ['"family_name":"Kit"', '"type":"kit"']);
    });

    it('kitPricesConfiguration: GET bundle/prices_configuration', function () {
        Http::fake(['api.mercadolibre.com/items/MLB1/bundle/prices_configuration' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->kitPricesConfiguration('MLB1');

        itemCoreAssertSent('GET', ['/items/MLB1/bundle/prices_configuration']);
    });

    it('updateKitPricesConfiguration: PUT bundle/prices_configuration', function () {
        Http::fake(['api.mercadolibre.com/items/MLB1/bundle/prices_configuration' => Http::response(['ok' => true])]);

        $result = itemCoreMethods()->updateKitPricesConfiguration('MLB1', ['bundle' => ['components' => []]]);

        itemCoreAssertSent('PUT', ['/items/MLB1/bundle/prices_configuration'], ['"bundle"']);
    });
});

describe('ItemMethods — restrições de busca', function () {
    it('searchRestrictions: GET /users/{id}/items/search/restrictions', function () {
        Http::fake(['api.mercadolibre.com/users/77/items/search/restrictions' => Http::response(['aggregations_allowed' => false])]);

        $result = itemCoreMethods()->searchRestrictions(77);

        expect($result['aggregations_allowed'])->toBeFalse();

        itemCoreAssertSent('GET', ['/users/77/items/search/restrictions']);
    });
});

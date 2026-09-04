<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\TopPicks\TopPicksMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function topPicksMethods(): TopPicksMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );

    return new TopPicksMethods(HttpClientFactory::make($integration), $integration);
}

function topPicksAssertShopCall(string $verb, string $path, ?callable $extra = null): void
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

function topPicksFake(string $name, array $response = []): void
{
    Http::fake(["partner.shopeemobile.com/api/v2/top_picks/{$name}*" => Http::response(['response' => $response])]);
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

describe('TopPicksMethods', function () {
    it('getTopPicksList GET sem parametros de negocio', function () {
        topPicksFake('get_top_picks_list', ['collection_list' => [['top_picks_id' => 1]]]);

        expect(topPicksMethods()->getTopPicksList()['collection_list'])->toHaveCount(1);

        topPicksAssertShopCall('GET', '/api/v2/top_picks/get_top_picks_list');
    });

    it('addTopPicks POST com name + item_id_list + is_activated', function () {
        topPicksFake('add_top_picks', ['collection_list' => [['top_picks_id' => 2]]]);

        topPicksMethods()->addTopPicks(name: 'Mais vendidos', itemIdList: [1, 2, 3, 4], isActivated: true);

        topPicksAssertShopCall('POST', '/api/v2/top_picks/add_top_picks', fn ($req) => $req['name'] === 'Mais vendidos'
            && $req['item_id_list'] === [1, 2, 3, 4] && $req['is_activated'] === true);
    });

    it('updateTopPicks envia so os campos informados', function () {
        topPicksFake('update_top_picks', ['collection_list' => []]);

        topPicksMethods()->updateTopPicks(2, isActivated: false);

        topPicksAssertShopCall('POST', '/api/v2/top_picks/update_top_picks', fn ($req) => $req['top_picks_id'] === 2
            && $req['is_activated'] === false && ! isset($req['name']) && ! isset($req['item_id_list']));
    });

    it('deleteTopPicks POST com top_picks_id', function () {
        topPicksFake('delete_top_picks', ['top_picks_id' => 2]);

        topPicksMethods()->deleteTopPicks(2);

        topPicksAssertShopCall('POST', '/api/v2/top_picks/delete_top_picks', fn ($req) => $req['top_picks_id'] === 2);
    });
});

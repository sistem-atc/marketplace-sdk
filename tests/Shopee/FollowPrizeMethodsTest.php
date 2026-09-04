<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\FollowPrize\FollowPrizeMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function followPrizeMethods(): FollowPrizeMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );

    return new FollowPrizeMethods(HttpClientFactory::make($integration), $integration);
}

function followPrizeAssertShopCall(string $verb, string $path, ?callable $extra = null): void
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

function followPrizeFake(string $name, array $response = []): void
{
    Http::fake(["partner.shopeemobile.com/api/v2/follow_prize/{$name}*" => Http::response(['response' => $response])]);
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

describe('FollowPrizeMethods', function () {
    it('getFollowPrizeList GET com status + paginacao', function () {
        followPrizeFake('get_follow_prize_list', ['follow_prize_list' => [], 'more' => false]);

        followPrizeMethods()->getFollowPrizeList(status: 'ongoing', pageNo: 3, pageSize: 50);

        followPrizeAssertShopCall('GET', '/api/v2/follow_prize/get_follow_prize_list', fn ($req, $q) => $q['status'] === 'ongoing' && $q['page_no'] === '3' && $q['page_size'] === '50');
    });

    it('getFollowPrizeDetail GET com campaign_id', function () {
        followPrizeFake('get_follow_prize_detail', ['campaign_id' => 40, 'follow_prize_name' => 'Siga']);

        expect(followPrizeMethods()->getFollowPrizeDetail(40)['follow_prize_name'])->toBe('Siga');

        followPrizeAssertShopCall('GET', '/api/v2/follow_prize/get_follow_prize_detail', fn ($req, $q) => $q['campaign_id'] === '40');
    });

    it('addFollowPrize POST com payload', function () {
        followPrizeFake('add_follow_prize', ['campain_id' => 41]);

        $resp = followPrizeMethods()->addFollowPrize([
            'follow_prize_name' => 'Siga', 'start_time' => 1, 'end_time' => 2, 'usage_quantity' => 100,
            'min_spend' => 30.0, 'reward_type' => 2, 'percentage' => 10, 'max_price' => 5.0,
        ]);

        expect($resp['campain_id'])->toBe(41);
        followPrizeAssertShopCall('POST', '/api/v2/follow_prize/add_follow_prize', fn ($req) => $req['reward_type'] === 2 && $req['percentage'] === 10);
    });

    it('updateFollowPrize mescla campaign_id + campos', function () {
        followPrizeFake('update_follow_prize', ['campaign_id' => 41]);

        followPrizeMethods()->updateFollowPrize(41, ['usage_quantity' => 500]);

        followPrizeAssertShopCall('POST', '/api/v2/follow_prize/update_follow_prize', fn ($req) => $req['campaign_id'] === 41 && $req['usage_quantity'] === 500);
    });

    it('deleteFollowPrize / endFollowPrize POST com campaign_id', function () {
        followPrizeFake('delete_follow_prize', ['campaign_id' => 41]);
        followPrizeFake('end_follow_prize', ['campaign_id' => 41]);

        followPrizeMethods()->deleteFollowPrize(41);
        followPrizeMethods()->endFollowPrize(41);

        followPrizeAssertShopCall('POST', '/api/v2/follow_prize/delete_follow_prize', fn ($req) => $req['campaign_id'] === 41);
        followPrizeAssertShopCall('POST', '/api/v2/follow_prize/end_follow_prize', fn ($req) => $req['campaign_id'] === 41);
    });
});

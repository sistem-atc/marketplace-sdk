<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Webhook\WebhookMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function webhookExtrasIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999],
        active: true,
        expired: false,
    );
}

function webhookExtras(): WebhookMethods
{
    $integration = webhookExtrasIntegration();

    return new WebhookMethods(HttpClientFactory::make($integration), $integration);
}

function webhookExtrasAssertPublic(string $method, string $path, array $bodyContains = [], array $bodyMissing = []): void
{
    Http::assertSent(function ($req) use ($method, $path, $bodyContains, $bodyMissing) {
        $url = $req->url();
        $ok = $req->method() === $method
            && str_contains($url, $path)
            && str_contains($url, 'partner_id=2030136')
            && str_contains($url, 'timestamp=')
            && str_contains($url, 'sign=')
            && ! str_contains($url, 'access_token=')
            && ! str_contains($url, 'shop_id=');
        foreach ($bodyContains as $needle) {
            $ok = $ok && str_contains($req->body(), $needle);
        }
        foreach ($bodyMissing as $needle) {
            $ok = $ok && ! str_contains($req->body(), $needle);
        }

        return $ok;
    });
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
    Http::fake(['partner.shopeemobile.com/api/v2/push/*' => Http::response(['error' => '', 'response' => ['ok' => true]])]);
});

describe('WebhookMethods push extras', function () {
    it('setAppPushConfig manda so os campos informados', function () {
        webhookExtras()->setAppPushConfig(callbackUrl: 'https://host.test/webhooks/shopee', on: [3, 4], blockedShopIds: [111]);
        webhookExtrasAssertPublic('POST', '/api/v2/push/set_app_push_config', [
            '"callback_url":"https:\/\/host.test\/webhooks\/shopee"', '"set_push_config_on":[3,4]', '"blocked_shop_id_list":[111]',
        ], ['set_push_config_off']);
    });

    it('setAppPushConfig so com off', function () {
        webhookExtras()->setAppPushConfig(off: [5]);
        webhookExtrasAssertPublic('POST', '/api/v2/push/set_app_push_config', ['"set_push_config_off":[5]'], ['callback_url', 'set_push_config_on']);
    });

    it('getAppPushConfig', function () {
        expect(webhookExtras()->getAppPushConfig()['response']['ok'])->toBeTrue();
        webhookExtrasAssertPublic('GET', '/api/v2/push/get_app_push_config');
    });

    it('getLostPushMessage', function () {
        webhookExtras()->getLostPushMessage();
        webhookExtrasAssertPublic('GET', '/api/v2/push/get_lost_push_message');
    });

    it('confirmConsumedLostPushMessage', function () {
        webhookExtras()->confirmConsumedLostPushMessage(987654);
        webhookExtrasAssertPublic('POST', '/api/v2/push/confirm_consumed_lost_push_message', ['"last_message_id":987654']);
    });
});

<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Fulfillment\FulfillmentMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluFulfillmentIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-fulfillment',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-fulfillment'],
        active: true,
        expired: false,
    );
}

function magaluFulfillmentClient(): FulfillmentMethods
{
    $integration = magaluFulfillmentIntegration();

    return new FulfillmentMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluFulfillmentSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
{
    Http::assertSent(function (Request $req) use ($method, $url, $query, $body, $headers): bool {
        $parts = parse_url($req->url());
        $actualUrl = $parts['scheme'].'://'.$parts['host'].($parts['path'] ?? '');
        // parse_str() troca '.' por '_' (seller.id → seller_id); parse manual preserva a chave.
        $actualQuery = [];
        foreach (array_filter(explode('&', $parts['query'] ?? '')) as $pair) {
            [$k, $v] = array_pad(explode('=', $pair, 2), 2, '');
            $actualQuery[urldecode($k)] = urldecode($v);
        }

        if ($req->method() !== $method || $actualUrl !== $url) return false;
        if ($actualQuery != $query) return false;
        if (! $req->hasHeader('Authorization', 'Bearer jwt-fulfillment')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-fulfillment')) return false;
        foreach ($headers as $k => $v) {
            if (! $req->hasHeader($k, $v)) return false;
        }
        if ($body !== null && json_decode($req->body(), true) !== $body) return false;

        return true;
    });
}

beforeEach(function () {
    config([
        'marketplaces.magalu.api_base' => 'https://api.magalu.com',
        'marketplaces.magalu.services_base' => 'https://services.magalu.com',
        'marketplaces.magalu.token_url' => 'https://autoseg-idp.luizalabs.com/oauth/token',
    ]);
    Http::fake(fn (Request $req) => str_contains($req->url(), '/attachments/')
        ? Http::response('BIN', 200, ['Content-Type' => 'image/png'])
        : Http::response(['ok' => true, 'results' => []]));
});

describe('FulfillmentMethods', function () {
    it('searchSchedules: GET services /logistics/v1/schedules com page/size', function () {
        $resp = magaluFulfillmentClient()->searchSchedules(['date_start' => '2026-09-01', 'schedule_type' => 'collect'], page: 2, size: 20);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFulfillmentSent('GET', 'https://services.magalu.com/logistics/v1/schedules', ['date_start' => '2026-09-01', 'schedule_type' => 'collect', 'page' => '2', 'size' => '20'], null);
    });

    it('createSchedule: POST /logistics/v1/schedules', function () {
        $resp = magaluFulfillmentClient()->createSchedule('user-uuid', generateSuggestedProducts: true);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFulfillmentSent('POST', 'https://services.magalu.com/logistics/v1/schedules', [], ['created_by' => 'user-uuid', 'options' => ['generate_suggested_products' => true]]);
    });

    it('getSchedule: GET /logistics/v1/schedules/{id}', function () {
        $resp = magaluFulfillmentClient()->getSchedule('sc-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFulfillmentSent('GET', 'https://services.magalu.com/logistics/v1/schedules/sc-1', [], null);
    });

    it('deleteSchedule: DELETE /logistics/v1/schedules/{id}', function () {
        $resp = magaluFulfillmentClient()->deleteSchedule('sc-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFulfillmentSent('DELETE', 'https://services.magalu.com/logistics/v1/schedules/sc-1', [], null);
    });

    it('cancelSchedule: PUT schedules/{id}/cancel?reason=', function () {
        $resp = magaluFulfillmentClient()->cancelSchedule('sc-1', 'sem estoque');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFulfillmentSent('PUT', 'https://services.magalu.com/logistics/v1/schedules/sc-1/cancel', ['reason' => 'sem estoque'], null);
    });

    it('availableDates: GET schedules/{id}/dates/{type}', function () {
        $resp = magaluFulfillmentClient()->availableDates('sc-1', 'collect');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFulfillmentSent('GET', 'https://services.magalu.com/logistics/v1/schedules/sc-1/dates/collect', [], null);
    });

    it('updateDate: PATCH schedules/{id}/date', function () {
        $resp = magaluFulfillmentClient()->updateDate('sc-1', 100, '2026-09-10', '08:00-12:00', 'collect', 'truck');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFulfillmentSent('PATCH', 'https://services.magalu.com/logistics/v1/schedules/sc-1/date', [], ['branch' => ['id' => 100], 'date' => '2026-09-10', 'hours' => '08:00-12:00', 'schedule' => ['type' => 'collect'], 'transport' => ['type' => 'truck']]);
    });

    it('upsertItems: PUT schedules/{id}/items com lista', function () {
        $resp = magaluFulfillmentClient()->upsertItems('sc-1', [['sku' => 'A', 'quantity' => 2, 'cost_price' => 10.5]]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFulfillmentSent('PUT', 'https://services.magalu.com/logistics/v1/schedules/sc-1/items', [], [['sku' => 'A', 'quantity' => 2, 'cost_price' => 10.5]]);
    });

    it('deleteItem: DELETE schedules/{id}/item/{sku}', function () {
        $resp = magaluFulfillmentClient()->deleteItem('sc-1', 'A');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFulfillmentSent('DELETE', 'https://services.magalu.com/logistics/v1/schedules/sc-1/item/A', [], null);
    });

    it('updateVolume: PATCH schedules/{id}/volume', function () {
        $resp = magaluFulfillmentClient()->updateVolume('sc-1', 3, 'pallet');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFulfillmentSent('PATCH', 'https://services.magalu.com/logistics/v1/schedules/sc-1/volume', [], ['quantity' => 3, 'type' => 'pallet']);
    });

    it('finalizeSchedule: POST schedules/{id}/finalize', function () {
        $resp = magaluFulfillmentClient()->finalizeSchedule('sc-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFulfillmentSent('POST', 'https://services.magalu.com/logistics/v1/schedules/sc-1/finalize', [], null);
    });

    it('labels: POST schedules/{id}/labels', function () {
        $resp = magaluFulfillmentClient()->labels('sc-1', ['type' => 'volume', 'format' => 'pdf', 'quantity' => 2]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFulfillmentSent('POST', 'https://services.magalu.com/logistics/v1/schedules/sc-1/labels', [], ['type' => 'volume', 'format' => 'pdf', 'quantity' => 2]);
    });
});

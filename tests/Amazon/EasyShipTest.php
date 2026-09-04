<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\EasyShip;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function amazonEasyShipIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: [
            'client_id' => 'test-client',
            'client_secret' => 'test-secret',
            'marketplace_id' => 'A2Q3Y263D00KWC',
        ],
        active: true,
        expired: false,
    );
}

function amazonEasyShipEndpoint(): EasyShip
{
    return new EasyShip(new Client(amazonEasyShipIntegration()));
}

/**
 * Fake generico + assert de verbo, URL completa, header x-amz-access-token e
 * (quando houver) chaves do body JSON.
 */
function amazonEasyShipAssertSent(string $method, string $url, array $bodyContains = []): void
{
    Http::assertSent(function (Request $request) use ($method, $url, $bodyContains): bool {
        if ($request->method() !== $method || $request->url() !== $url) {
            return false;
        }
        if ($request->header('x-amz-access-token')[0] !== 'Atza|valid-token') {
            return false;
        }
        foreach ($bodyContains as $key => $value) {
            if (data_get($request->data(), $key) !== $value) {
                return false;
            }
        }

        return true;
    });
}

it('listHandoverSlots → POST /easyShip/2022-03-23/timeSlot', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonEasyShipEndpoint()->listHandoverSlots(['amazonOrderId' => '701-1', 'marketplaceId' => 'A2Q3Y263D00KWC']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonEasyShipAssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/easyShip/2022-03-23/timeSlot', ['amazonOrderId' => '701-1']);
});

it('getScheduledPackage → GET /easyShip/2022-03-23/package?amazonOrderId=701-1&marketplaceId=A2Q3Y263D00KWC', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonEasyShipEndpoint()->getScheduledPackage('701-1', 'A2Q3Y263D00KWC');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonEasyShipAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/easyShip/2022-03-23/package?amazonOrderId=701-1&marketplaceId=A2Q3Y263D00KWC');
});

it('createScheduledPackage → POST /easyShip/2022-03-23/package', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonEasyShipEndpoint()->createScheduledPackage(['amazonOrderId' => '701-1']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonEasyShipAssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/easyShip/2022-03-23/package', ['amazonOrderId' => '701-1']);
});

it('updateScheduledPackages → PATCH /easyShip/2022-03-23/package', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonEasyShipEndpoint()->updateScheduledPackages(['marketplaceId' => 'A2Q3Y263D00KWC']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonEasyShipAssertSent('PATCH', 'https://sellingpartnerapi-na.amazon.com/easyShip/2022-03-23/package', ['marketplaceId' => 'A2Q3Y263D00KWC']);
});

it('createScheduledPackageBulk → POST /easyShip/2022-03-23/packages/bulk', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonEasyShipEndpoint()->createScheduledPackageBulk(['marketplaceId' => 'A2Q3Y263D00KWC', 'orderScheduleDetailsList' => []]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonEasyShipAssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/easyShip/2022-03-23/packages/bulk', ['marketplaceId' => 'A2Q3Y263D00KWC']);
});

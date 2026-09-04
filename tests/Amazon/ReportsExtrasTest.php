<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Reports;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function reportsExtras(): Reports
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new Reports(new Client($integration));
}

/** Um único request enviado, com verbo + URL exata + token LWA. */
function reportsExtrasAssertSent(string $method, string $url, ?array $body = null, string $token = 'Atza|valid-token'): void
{
    Http::assertSent(function (Request $req) use ($method, $url, $body, $token): bool {
        if ($req->method() !== $method || $req->url() !== $url) {
            return false;
        }
        if ($req->header('x-amz-access-token')[0] !== $token) {
            return false;
        }
        if ($body !== null && $req->data() !== $body) {
            return false;
        }

        return true;
    });
}

const RS = 'https://sellingpartnerapi-na.amazon.com/reports/2021-06-30/schedules';

it('getReportSchedules GET /schedules?reportTypes=csv', function () {
    Http::fake([RS.'*' => Http::response(['reportSchedules' => []])]);
    reportsExtras()->getReportSchedules(['GET_MERCHANT_LISTINGS_ALL_DATA', 'GET_FLAT_FILE_ORDERS_DATA']);
    reportsExtrasAssertSent('GET', RS.'?reportTypes=GET_MERCHANT_LISTINGS_ALL_DATA%2CGET_FLAT_FILE_ORDERS_DATA');
});

it('createReportSchedule POST /schedules com reportType/marketplaceIds/period + options', function () {
    Http::fake([RS => Http::response(['reportScheduleId' => 'S1'])]);
    $resp = reportsExtras()->createReportSchedule('GET_MERCHANT_LISTINGS_ALL_DATA', ['A2Q3Y263D00KWC'], 'P1D', ['nextReportCreationTime' => '2026-09-05T00:00:00Z']);
    expect($resp['reportScheduleId'])->toBe('S1');
    reportsExtrasAssertSent('POST', RS, ['reportType' => 'GET_MERCHANT_LISTINGS_ALL_DATA', 'marketplaceIds' => ['A2Q3Y263D00KWC'], 'period' => 'P1D', 'nextReportCreationTime' => '2026-09-05T00:00:00Z']);
});

it('getReportSchedule GET /schedules/{id}', function () {
    Http::fake([RS.'/S1' => Http::response(['reportScheduleId' => 'S1', 'period' => 'P1D'])]);
    expect(reportsExtras()->getReportSchedule('S1')['period'])->toBe('P1D');
    reportsExtrasAssertSent('GET', RS.'/S1');
});

it('cancelReportSchedule DELETE /schedules/{id}', function () {
    Http::fake([RS.'/S1' => Http::response([], 200)]);
    reportsExtras()->cancelReportSchedule('S1');
    reportsExtrasAssertSent('DELETE', RS.'/S1');
});

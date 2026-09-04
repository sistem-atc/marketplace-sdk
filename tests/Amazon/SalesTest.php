<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Sales;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function sales(): Sales
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new Sales(new Client($integration));
}

/** Um único request enviado, com verbo + URL exata + token LWA. */
function salesAssertSent(string $method, string $url, ?array $body = null, string $token = 'Atza|valid-token'): void
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

it('getOrderMetrics GET /sales/v1/orderMetrics com marketplaceIds csv, interval e granularity', function () {
    Http::fake(['*/sales/v1/orderMetrics*' => Http::response(['payload' => [['interval' => 'x', 'orderCount' => 3]]])]);
    $resp = sales()->getOrderMetrics(['A2Q3Y263D00KWC'], '2026-08-01T00:00:00-03:00--2026-09-01T00:00:00-03:00', 'Day', ['buyerType' => 'All', 'sku' => 'SKU-1']);
    expect($resp['payload'][0]['orderCount'])->toBe(3);
    salesAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/sales/v1/orderMetrics?marketplaceIds=A2Q3Y263D00KWC&interval=2026-08-01T00%3A00%3A00-03%3A00--2026-09-01T00%3A00%3A00-03%3A00&granularity=Day&buyerType=All&sku=SKU-1');
});

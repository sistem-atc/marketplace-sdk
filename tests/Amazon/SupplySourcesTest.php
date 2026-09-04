<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\SupplySources;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function amazonSupplySourcesIntegration(): FakeIntegration
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

function amazonSupplySourcesEndpoint(): SupplySources
{
    return new SupplySources(new Client(amazonSupplySourcesIntegration()));
}

/**
 * Fake generico + assert de verbo, URL completa, header x-amz-access-token e
 * (quando houver) chaves do body JSON.
 */
function amazonSupplySourcesAssertSent(string $method, string $url, array $bodyContains = []): void
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

it('getSupplySources → GET /supplySources/2020-07-01/supplySources?nextPageToken=TOK&pageSize=10', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonSupplySourcesEndpoint()->getSupplySources(['nextPageToken' => 'TOK', 'pageSize' => 10]);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonSupplySourcesAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/supplySources/2020-07-01/supplySources?nextPageToken=TOK&pageSize=10');
});

it('createSupplySource → POST /supplySources/2020-07-01/supplySources', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonSupplySourcesEndpoint()->createSupplySource(['supplySourceCode' => 'LOJA-1']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonSupplySourcesAssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/supplySources/2020-07-01/supplySources', ['supplySourceCode' => 'LOJA-1']);
});

it('getSupplySource → GET /supplySources/2020-07-01/supplySources/SS-1', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonSupplySourcesEndpoint()->getSupplySource('SS-1');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonSupplySourcesAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/supplySources/2020-07-01/supplySources/SS-1');
});

it('updateSupplySource → PUT /supplySources/2020-07-01/supplySources/SS-1', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonSupplySourcesEndpoint()->updateSupplySource('SS-1', ['alias' => 'Loja 1']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonSupplySourcesAssertSent('PUT', 'https://sellingpartnerapi-na.amazon.com/supplySources/2020-07-01/supplySources/SS-1', ['alias' => 'Loja 1']);
});

it('archiveSupplySource → DELETE /supplySources/2020-07-01/supplySources/SS-1', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonSupplySourcesEndpoint()->archiveSupplySource('SS-1');

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonSupplySourcesAssertSent('DELETE', 'https://sellingpartnerapi-na.amazon.com/supplySources/2020-07-01/supplySources/SS-1');
});

it('updateSupplySourceStatus → PUT /supplySources/2020-07-01/supplySources/SS-1/status', function () {
    Http::fake(['*' => Http::response(['payload' => ['ok' => true]], 200)]);

    $resp = amazonSupplySourcesEndpoint()->updateSupplySourceStatus('SS-1', ['status' => 'Inactive']);

    expect($resp)->toBe(['payload' => ['ok' => true]]);
    amazonSupplySourcesAssertSent('PUT', 'https://sellingpartnerapi-na.amazon.com/supplySources/2020-07-01/supplySources/SS-1/status', ['status' => 'Inactive']);
});

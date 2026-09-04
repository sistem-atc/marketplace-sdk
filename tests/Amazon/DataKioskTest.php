<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\DataKiosk;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function dataKiosk(): DataKiosk
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new DataKiosk(new Client($integration));
}

/** Um único request enviado, com verbo + URL exata + token LWA. */
function dataKioskAssertSent(string $method, string $url, ?array $body = null, string $token = 'Atza|valid-token'): void
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

const DK = 'https://sellingpartnerapi-na.amazon.com/dataKiosk/2023-11-15';

it('getQueries GET /queries com processingStatuses csv', function () {
    Http::fake([DK.'/queries?*' => Http::response(['queries' => [], 'pagination' => []])]);
    dataKiosk()->getQueries(['processingStatuses' => ['DONE', 'IN_PROGRESS'], 'pageSize' => 5]);
    dataKioskAssertSent('GET', DK.'/queries?processingStatuses=DONE%2CIN_PROGRESS&pageSize=5');
});

it('createQuery POST /queries com graphql e paginationToken', function () {
    Http::fake([DK.'/queries' => Http::response(['queryId' => 'Q1'])]);
    expect(dataKiosk()->createQuery('query{analytics_salesAndTraffic_2024_04_24{}}', 'PT')['queryId'])->toBe('Q1');
    dataKioskAssertSent('POST', DK.'/queries', ['query' => 'query{analytics_salesAndTraffic_2024_04_24{}}', 'paginationToken' => 'PT']);
});

it('getQuery GET /queries/{id}', function () {
    Http::fake([DK.'/queries/Q1' => Http::response(['queryId' => 'Q1', 'processingStatus' => 'DONE', 'dataDocumentId' => 'D1'])]);
    expect(dataKiosk()->getQuery('Q1')['dataDocumentId'])->toBe('D1');
    dataKioskAssertSent('GET', DK.'/queries/Q1');
});

it('cancelQuery DELETE /queries/{id}', function () {
    Http::fake([DK.'/queries/Q1' => Http::response([], 204)]);
    dataKiosk()->cancelQuery('Q1');
    dataKioskAssertSent('DELETE', DK.'/queries/Q1');
});

it('getDocument GET /documents/{id} e downloadDocument baixa sem token', function () {
    Http::fake([DK.'/documents/D1' => Http::response(['documentId' => 'D1', 'documentUrl' => 'https://s3.example/d1']), 'https://s3.example/d1' => Http::response('{"a":1}', 200)]);
    $doc = dataKiosk()->getDocument('D1');
    expect(dataKiosk()->downloadDocument($doc['documentUrl']))->toBe('{"a":1}');
    dataKioskAssertSent('GET', DK.'/documents/D1');
    Http::assertSent(fn (Request $r) => $r->url() === 'https://s3.example/d1' && ! $r->hasHeader('x-amz-access-token'));
});

<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Feeds;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function feeds(): Feeds
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new Feeds(new Client($integration));
}

/** Um único request enviado, com verbo + URL exata + token LWA. */
function feedsAssertSent(string $method, string $url, ?array $body = null, string $token = 'Atza|valid-token'): void
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

const FD = 'https://sellingpartnerapi-na.amazon.com/feeds/2021-06-30';

it('getFeeds GET /feeds com filtros csv', function () {
    Http::fake([FD.'/feeds?*' => Http::response(['feeds' => [], 'nextToken' => null])]);
    feeds()->getFeeds(['feedTypes' => ['POST_INVENTORY_AVAILABILITY_DATA'], 'processingStatuses' => ['DONE', 'FATAL'], 'pageSize' => 10]);
    feedsAssertSent('GET', FD.'/feeds?feedTypes=POST_INVENTORY_AVAILABILITY_DATA&processingStatuses=DONE%2CFATAL&pageSize=10');
});

it('createFeed POST /feeds com feedType/marketplaceIds/inputFeedDocumentId', function () {
    Http::fake([FD.'/feeds' => Http::response(['feedId' => 'F1'])]);
    expect(feeds()->createFeed('JSON_LISTINGS_FEED', ['A2Q3Y263D00KWC'], 'DOC1', ['feedOptions' => ['k' => 'v']])['feedId'])->toBe('F1');
    feedsAssertSent('POST', FD.'/feeds', ['feedType' => 'JSON_LISTINGS_FEED', 'marketplaceIds' => ['A2Q3Y263D00KWC'], 'inputFeedDocumentId' => 'DOC1', 'feedOptions' => ['k' => 'v']]);
});

it('getFeed GET /feeds/{id}', function () {
    Http::fake([FD.'/feeds/F1' => Http::response(['feedId' => 'F1', 'processingStatus' => 'DONE', 'resultFeedDocumentId' => 'R1'])]);
    expect(feeds()->getFeed('F1')['resultFeedDocumentId'])->toBe('R1');
    feedsAssertSent('GET', FD.'/feeds/F1');
});

it('cancelFeed DELETE /feeds/{id}', function () {
    Http::fake([FD.'/feeds/F1' => Http::response([], 200)]);
    feeds()->cancelFeed('F1');
    feedsAssertSent('DELETE', FD.'/feeds/F1');
});

it('createFeedDocument POST /documents com contentType', function () {
    Http::fake([FD.'/documents' => Http::response(['feedDocumentId' => 'DOC1', 'url' => 'https://s3.example/presigned'])]);
    expect(feeds()->createFeedDocument('text/xml; charset=UTF-8')['url'])->toBe('https://s3.example/presigned');
    feedsAssertSent('POST', FD.'/documents', ['contentType' => 'text/xml; charset=UTF-8']);
});

it('uploadFeedDocument PUT binario na URL pre-assinada sem token SP-API', function () {
    Http::fake(['https://s3.example/presigned' => Http::response('', 200)]);
    expect(feeds()->uploadFeedDocument('https://s3.example/presigned', '<xml/>', 'text/xml; charset=UTF-8'))->toBeTrue();
    Http::assertSent(fn (Request $r) => $r->method() === 'PUT'
        && $r->url() === 'https://s3.example/presigned'
        && $r->body() === '<xml/>'
        && $r->header('Content-Type')[0] === 'text/xml; charset=UTF-8'
        && ! $r->hasHeader('x-amz-access-token'));
});

it('getFeedDocument GET /documents/{id} com enableContentEncodingUrlHeader=true', function () {
    Http::fake([FD.'/documents/R1*' => Http::response(['feedDocumentId' => 'R1', 'url' => 'https://s3.example/r', 'compressionAlgorithm' => 'GZIP'])]);
    expect(feeds()->getFeedDocument('R1', true)['compressionAlgorithm'])->toBe('GZIP');
    feedsAssertSent('GET', FD.'/documents/R1?enableContentEncodingUrlHeader=true');
});

it('downloadFeedDocument baixa e descomprime GZIP', function () {
    Http::fake(['https://s3.example/r' => Http::response(gzencode('{"ok":true}'), 200)]);
    expect(feeds()->downloadFeedDocument('https://s3.example/r', 'GZIP'))->toBe('{"ok":true}');
});

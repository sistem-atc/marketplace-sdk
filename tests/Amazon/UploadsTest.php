<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Uploads;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function uploadsIntegration(): FakeIntegration
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

function uploadsEndpoint(): Uploads
{
    return new Uploads(new Client(uploadsIntegration()));
}

it('createUploadDestinationForResource POSTs /uploadDestinations/{resource} with marketplaceIds + contentMD5 + contentType in the query', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/uploads/2020-11-01/*' => Http::response([
            'payload' => ['uploadDestinationId' => 'UD-1', 'url' => 'https://s3.example/put', 'headers' => []],
        ], 201),
    ]);

    $resp = uploadsEndpoint()->createUploadDestinationForResource(
        resource: 'aplus/2020-11-01/contentDocuments',
        marketplaceIds: ['A2Q3Y263D00KWC', 'ATVPDKIKX0DER'],
        contentMD5: 'rL0Y20zC+Fzt72VPzMSk2A==',
        query: ['contentType' => 'image/jpeg'],
    );

    expect(data_get($resp, 'payload.uploadDestinationId'))->toBe('UD-1');

    $expected = 'https://sellingpartnerapi-na.amazon.com/uploads/2020-11-01/uploadDestinations/aplus/2020-11-01/contentDocuments?'
        .http_build_query(['marketplaceIds' => 'A2Q3Y263D00KWC,ATVPDKIKX0DER', 'contentMD5' => 'rL0Y20zC+Fzt72VPzMSk2A==', 'contentType' => 'image/jpeg']);

    Http::assertSent(fn ($r) => $r->method() === 'POST'
        && $r->url() === $expected
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Exceptions\MagaluRequestException;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function makeMagaluIntegrationForSdk(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-fake',
        refreshToken: 'rt-fake',
        settings: [
            'client_id' => 'cli',
            'client_secret' => 'sec',
            'tenant_id' => 'tenant-uuid',
        ],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config([
        'marketplaces.magalu.api_base' => 'https://api.magalu.com',
        'marketplaces.magalu.token_url' => 'https://autoseg-idp.luizalabs.com/oauth/token',
    ]);
});

describe('MarketPlaces::Magalu()->invoices()->getFulfillmentSignedUrl', function () {
    it('chama o endpoint correto com Bearer + X-Tenant-Id headers', function () {
        Http::fake([
            'api.magalu.com/seller/v1/invoices/fulfillment*' => Http::response([
                'signed_url' => 'https://gcs.fake/zip',
                'expires_on' => '2026-05-07T18:00:00Z',
            ]),
        ]);

        $integration = makeMagaluIntegrationForSdk();
        $resp = MarketPlaces::Magalu()->invoices($integration)->getFulfillmentSignedUrl('2026-05-01', '2026-05-01');

        expect($resp['signed_url'])->toBe('https://gcs.fake/zip');

        Http::assertSent(fn ($req) => str_contains($req->url(), '/seller/v1/invoices/fulfillment')
            && str_contains($req->url(), 'start_date=2026-05-01')
            && str_contains($req->url(), 'end_date=2026-05-01')
            && $req->hasHeader('Authorization', 'Bearer jwt-fake')
            && $req->hasHeader('X-Tenant-Id', 'tenant-uuid'));
    });

    it('rejeita endDate anterior a startDate', function () {
        Http::fake();

        $integration = makeMagaluIntegrationForSdk();

        MarketPlaces::Magalu()->invoices($integration)->getFulfillmentSignedUrl('2026-05-10', '2026-05-01');
    })->throws(InvalidArgumentException::class, 'endDate');

    it('rejeita range maior que 30 dias (limite Magalu)', function () {
        Http::fake();

        $integration = makeMagaluIntegrationForSdk();

        MarketPlaces::Magalu()->invoices($integration)->getFulfillmentSignedUrl('2026-01-01', '2026-03-15');
    })->throws(InvalidArgumentException::class, '30 dias');

    it('rejeita formato invalido de data', function () {
        Http::fake();

        $integration = makeMagaluIntegrationForSdk();

        MarketPlaces::Magalu()->invoices($integration)->getFulfillmentSignedUrl('01/05/2026', '2026-05-01');
    })->throws(InvalidArgumentException::class, 'YYYY-MM-DD');
});

describe('MarketPlaces::Magalu()->invoices()->downloadFulfillmentZip', function () {
    it('faz GET sem auth na signed_url e retorna body cru', function () {
        $fakeBody = "PK\x03\x04\x14\x00\x00\x00...fake-zip-bytes";

        Http::fake([
            'gcs.fake/*' => Http::response($fakeBody, 200, ['Content-Type' => 'application/zip']),
        ]);

        $integration = makeMagaluIntegrationForSdk();
        $body = MarketPlaces::Magalu()->invoices($integration)->downloadFulfillmentZip('https://gcs.fake/zip-file');

        expect($body)->toBe($fakeBody);

        Http::assertSent(fn ($req) => $req->url() === 'https://gcs.fake/zip-file');
    });

    it('lanca MagaluRequestException quando download falha', function () {
        Http::fake([
            'gcs.fake/*' => Http::response('', 403),
        ]);

        $integration = makeMagaluIntegrationForSdk();
        MarketPlaces::Magalu()->invoices($integration)->downloadFulfillmentZip('https://gcs.fake/expired');
    })->throws(MagaluRequestException::class);
});

describe('MarketPlaces::Magalu()->orders()', function () {
    it('listOrders chama /seller/v1/orders com limit/offset', function () {
        Http::fake([
            'api.magalu.com/seller/v1/orders*' => Http::response([
                'results' => [['id' => '01', 'code' => 'ORD-1', 'status' => 'paid']],
                'meta' => ['page' => ['total' => 1]],
            ]),
        ]);

        $integration = makeMagaluIntegrationForSdk();
        $resp = MarketPlaces::Magalu()->orders($integration)->listOrders(limit: 10, offset: 5);

        expect($resp['results'])->toHaveCount(1);

        Http::assertSent(fn ($req) => str_contains($req->url(), 'limit=10')
            && str_contains($req->url(), '_offset=5'));
    });
});

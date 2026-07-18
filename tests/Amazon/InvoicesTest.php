<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function amazonInvoicesIntegration(): FakeIntegration
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

it('createExport POSTs to /tax/invoices/2024-06-19/invoices/exports and returns the payload', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/tax/invoices/2024-06-19/invoices/exports' => Http::response([
            'export' => ['exportId' => 'EXP-123'],
        ], 200),
    ]);

    $resp = MarketPlaces::Amazon()->invoices(amazonInvoicesIntegration())
        ->createExport('A2Q3Y263D00KWC', '2024-06-01T00:00:00Z', '2024-06-30T23:59:59Z');

    expect(data_get($resp, 'export.exportId'))->toBe('EXP-123');

    Http::assertSent(fn ($request) => $request->method() === 'POST'
        && str_contains($request->url(), '/tax/invoices/2024-06-19/invoices/exports')
        && $request['marketplaceId'] === 'A2Q3Y263D00KWC'
        && $request['dateStart'] === '2024-06-01T00:00:00Z');
});

it('getExport GETs the export status by id', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/tax/invoices/2024-06-19/invoices/exports/EXP-123' => Http::response([
            'export' => ['status' => 'DONE', 'documentIds' => ['DOC-1']],
        ], 200),
    ]);

    $resp = MarketPlaces::Amazon()->invoices(amazonInvoicesIntegration())->getExport('EXP-123');

    expect($resp->export->status)->toBe('DONE')
        ->and($resp->export->documentIds)->toBe(['DOC-1']);
});

it('getDocument GETs the document metadata with the presigned url', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/tax/invoices/2024-06-19/invoices/documents/DOC-1' => Http::response([
            'invoicesDocumentUrl' => 'https://s3.example/doc.zip',
        ], 200),
    ]);

    $resp = MarketPlaces::Amazon()->invoices(amazonInvoicesIntegration())->getDocument('DOC-1');

    expect($resp->invoicesDocumentUrl)->toBe('https://s3.example/doc.zip');
});

it('downloadDocument fetches the raw binary from the presigned url', function () {
    Http::fake([
        'https://s3.example/doc.zip' => Http::response('PK-binary-zip-bytes', 200),
    ]);

    $body = MarketPlaces::Amazon()->invoices(amazonInvoicesIntegration())
        ->downloadDocument('https://s3.example/doc.zip');

    expect($body)->toBe('PK-binary-zip-bytes');
});

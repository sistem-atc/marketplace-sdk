<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
    // Os endpoints tax/invoices usam o access token LWA direto — NAO precisam de
    // RDT (confirmado contra a API real + doc oficial). Path correto: o segmento
    // "invoices" ja' esta no base tax/invoices, entao NAO se repete no sub-path.
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

it('createExport POSTs to /tax/invoices/2024-06-19/exports and returns the payload', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/tax/invoices/2024-06-19/exports' => Http::response([
            'exportId' => 'EXP-123',
        ], 202),
    ]);

    $resp = MarketPlaces::Amazon()->invoices(amazonInvoicesIntegration())
        ->createExport('A2Q3Y263D00KWC', '2024-06-01', '2024-06-30');

    expect(data_get($resp, 'exportId'))->toBe('EXP-123');

    Http::assertSent(fn ($request) => $request->method() === 'POST'
        && str_contains($request->url(), '/tax/invoices/2024-06-19/exports')
        && ! str_contains($request->url(), '/invoices/exports')
        && $request['marketplaceId'] === 'A2Q3Y263D00KWC'
        && $request['dateStart'] === '2024-06-01'
        && $request['dateEnd'] === '2024-06-30');
});

it('getExport GETs the export status by id (export.status + invoicesDocumentIds)', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/tax/invoices/2024-06-19/exports/EXP-123' => Http::response([
            'export' => ['status' => 'DONE', 'invoicesDocumentIds' => ['DOC-1']],
        ], 200),
    ]);

    $resp = MarketPlaces::Amazon()->invoices(amazonInvoicesIntegration())->getExport('EXP-123');

    expect($resp->export->status)->toBe('DONE')
        ->and($resp->export->invoicesDocumentIds)->toBe(['DOC-1']);
});

it('getDocument GETs /documents/{id} and unwraps invoicesDocument', function () {
    Http::fake([
        'https://sellingpartnerapi-na.amazon.com/tax/invoices/2024-06-19/documents/DOC-1' => Http::response([
            'invoicesDocument' => [
                'invoicesDocumentUrl' => 'https://s3.example/doc.zip',
                'invoicesDocumentId' => 'DOC-1',
            ],
        ], 200),
    ]);

    $resp = MarketPlaces::Amazon()->invoices(amazonInvoicesIntegration())->getDocument('DOC-1');

    expect($resp->invoicesDocumentUrl)->toBe('https://s3.example/doc.zip')
        ->and($resp->invoicesDocumentId)->toBe('DOC-1');
});

it('downloadDocument fetches the raw binary from the presigned url', function () {
    Http::fake([
        'https://s3.example/doc.zip' => Http::response('PK-binary-zip-bytes', 200),
    ]);

    $body = MarketPlaces::Amazon()->invoices(amazonInvoicesIntegration())
        ->downloadDocument('https://s3.example/doc.zip');

    expect($body)->toBe('PK-binary-zip-bytes');
});

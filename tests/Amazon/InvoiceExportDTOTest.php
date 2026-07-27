<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Amazon\DTO\Response\Invoice\Export;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Invoice\ExportResponse;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Invoice\InvoiceDocument;

it('ExportResponse desembrulha export{status,invoicesDocumentIds} (camelCase, wrapped)', function () {
    $resp = ExportResponse::fromArray(['export' => ['exportId' => 'EXP-1', 'status' => 'DONE', 'invoicesDocumentIds' => ['DOC-1', 'DOC-2']]]);

    expect($resp->export)->toBeInstanceOf(Export::class)
        ->and($resp->export->status)->toBe('DONE')
        ->and($resp->export->invoicesDocumentIds)->toBe(['DOC-1', 'DOC-2'])
        ->and($resp->toArray())->toEqual(['export' => ['exportId' => 'EXP-1', 'status' => 'DONE', 'invoicesDocumentIds' => ['DOC-1', 'DOC-2']]]);
});

it('InvoiceDocument tipa invoicesDocumentUrl + invoicesDocumentId (camelCase no roundtrip)', function () {
    $d = InvoiceDocument::fromArray(['invoicesDocumentUrl' => 'https://s3.example/doc.zip', 'invoicesDocumentId' => 'DOC-1']);

    expect($d->invoicesDocumentUrl)->toBe('https://s3.example/doc.zip')
        ->and($d->invoicesDocumentId)->toBe('DOC-1')
        ->and($d->toArray())->toEqual(['invoicesDocumentUrl' => 'https://s3.example/doc.zip', 'invoicesDocumentId' => 'DOC-1']);
});

<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Amazon\DTO\Response\Report\Report;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Report\ReportDocument;

it('Report tipa o status/documentId (camelCase, flat — sem wrapper report)', function () {
    $r = Report::fromArray([
        'reportId' => '123',
        'reportType' => 'GET_MERCHANT_LISTINGS_ALL_DATA',
        'processingStatus' => 'DONE',
        'reportDocumentId' => 'doc-9',
        'marketplaceIds' => ['A2Q3Y263D00KWC'],
        'createdTime' => '2026-07-10T12:00:00Z',
    ]);

    expect($r->processingStatus)->toBe('DONE')
        ->and($r->reportDocumentId)->toBe('doc-9')
        ->and($r->marketplaceIds)->toBe(['A2Q3Y263D00KWC']);
});

it('ReportDocument tipa url + compressionAlgorithm', function () {
    $d = ReportDocument::fromArray([
        'reportDocumentId' => 'doc-9',
        'url' => 'https://s3.amazonaws.com/x?sig=y',
        'compressionAlgorithm' => 'GZIP',
    ]);

    expect($d->url)->toBe('https://s3.amazonaws.com/x?sig=y')
        ->and($d->compressionAlgorithm)->toBe('GZIP');
});

it('roundtrip lossless (omit-null)', function () {
    $payload = ['reportId' => '1', 'processingStatus' => 'IN_PROGRESS'];
    expect(Report::fromArray($payload)->toArray())->toEqual($payload);
});

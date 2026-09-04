<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\AplusContent;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function aplusContentIntegration(): FakeIntegration
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

function aplusContentEndpoint(): AplusContent
{
    return new AplusContent(new Client(aplusContentIntegration()));
}

function aplusContentAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const APLUS_BASE = 'https://sellingpartnerapi-na.amazon.com/aplus/2020-11-01';
const APLUS_MP = 'A2Q3Y263D00KWC';

it('searchContentDocuments GETs /contentDocuments?marketplaceId=&pageToken=', function () {
    Http::fake([APLUS_BASE.'/*' => Http::response(['contentMetadataRecords' => [], 'nextPageToken' => null])]);

    $resp = aplusContentEndpoint()->searchContentDocuments(APLUS_MP, ['pageToken' => 'p1']);

    expect($resp['contentMetadataRecords'])->toBe([]);
    aplusContentAssertSent('GET', APLUS_BASE.'/contentDocuments?marketplaceId='.APLUS_MP.'&pageToken=p1');
});

it('createContentDocument POSTs /contentDocuments?marketplaceId= with the body', function () {
    Http::fake([APLUS_BASE.'/*' => Http::response(['contentReferenceKey' => 'CRK-1'])]);
    $body = ['contentDocument' => ['name' => 'Whey', 'contentType' => 'EBC', 'locale' => 'pt-BR', 'contentModuleList' => []]];

    $resp = aplusContentEndpoint()->createContentDocument(APLUS_MP, $body);

    expect($resp['contentReferenceKey'])->toBe('CRK-1');
    aplusContentAssertSent('POST', APLUS_BASE.'/contentDocuments?marketplaceId='.APLUS_MP, $body);
});

it('getContentDocument GETs /contentDocuments/{key} with marketplaceId + includedDataSet csv', function () {
    Http::fake([APLUS_BASE.'/*' => Http::response(['contentRecord' => ['contentReferenceKey' => 'CRK-1']])]);

    $resp = aplusContentEndpoint()->getContentDocument('CRK-1', APLUS_MP, ['CONTENTS', 'METADATA']);

    expect($resp['contentRecord']['contentReferenceKey'])->toBe('CRK-1');
    aplusContentAssertSent('GET', APLUS_BASE.'/contentDocuments/CRK-1?marketplaceId='.APLUS_MP.'&includedDataSet=CONTENTS%2CMETADATA');
});

it('updateContentDocument POSTs /contentDocuments/{key}?marketplaceId= with the body', function () {
    Http::fake([APLUS_BASE.'/*' => Http::response(['contentReferenceKey' => 'CRK-1'])]);
    $body = ['contentDocument' => ['name' => 'Whey v2']];

    aplusContentEndpoint()->updateContentDocument('CRK-1', APLUS_MP, $body);

    aplusContentAssertSent('POST', APLUS_BASE.'/contentDocuments/CRK-1?marketplaceId='.APLUS_MP, $body);
});

it('listContentDocumentAsinRelations GETs /contentDocuments/{key}/asins with extras', function () {
    Http::fake([APLUS_BASE.'/*' => Http::response(['asinMetadataSet' => []])]);

    aplusContentEndpoint()->listContentDocumentAsinRelations('CRK-1', APLUS_MP, ['includedDataSet' => 'METADATA', 'asinSet' => 'B001,B002']);

    aplusContentAssertSent('GET', APLUS_BASE.'/contentDocuments/CRK-1/asins?marketplaceId='.APLUS_MP.'&includedDataSet=METADATA&asinSet=B001%2CB002');
});

it('postContentDocumentAsinRelations POSTs /contentDocuments/{key}/asins?marketplaceId= with the body', function () {
    Http::fake([APLUS_BASE.'/*' => Http::response([], 200)]);
    $body = ['asinSet' => ['B001', 'B002']];

    aplusContentEndpoint()->postContentDocumentAsinRelations('CRK-1', APLUS_MP, $body);

    aplusContentAssertSent('POST', APLUS_BASE.'/contentDocuments/CRK-1/asins?marketplaceId='.APLUS_MP, $body);
});

it('validateContentDocumentAsinRelations POSTs /contentAsinValidations with marketplaceId + asinSet csv and the body', function () {
    Http::fake([APLUS_BASE.'/*' => Http::response(['warnings' => []])]);
    $body = ['contentDocument' => ['name' => 'Whey']];

    aplusContentEndpoint()->validateContentDocumentAsinRelations(APLUS_MP, $body, ['B001', 'B002']);

    aplusContentAssertSent('POST', APLUS_BASE.'/contentAsinValidations?marketplaceId='.APLUS_MP.'&asinSet=B001%2CB002', $body);
});

it('validateContentDocumentAsinRelations omits asinSet when empty', function () {
    Http::fake([APLUS_BASE.'/*' => Http::response(['warnings' => []])]);

    aplusContentEndpoint()->validateContentDocumentAsinRelations(APLUS_MP, ['contentDocument' => []]);

    aplusContentAssertSent('POST', APLUS_BASE.'/contentAsinValidations?marketplaceId='.APLUS_MP);
});

it('searchContentPublishRecords GETs /contentPublishRecords?marketplaceId=&asin=', function () {
    Http::fake([APLUS_BASE.'/*' => Http::response(['publishRecordList' => []])]);

    $resp = aplusContentEndpoint()->searchContentPublishRecords(APLUS_MP, 'B001', ['pageToken' => 'p1']);

    expect($resp['publishRecordList'])->toBe([]);
    aplusContentAssertSent('GET', APLUS_BASE.'/contentPublishRecords?marketplaceId='.APLUS_MP.'&asin=B001&pageToken=p1');
});

it('postContentDocumentApprovalSubmission POSTs /contentDocuments/{key}/approvalSubmissions?marketplaceId=', function () {
    Http::fake([APLUS_BASE.'/*' => Http::response([], 200)]);

    aplusContentEndpoint()->postContentDocumentApprovalSubmission('CRK-1', APLUS_MP);

    aplusContentAssertSent('POST', APLUS_BASE.'/contentDocuments/CRK-1/approvalSubmissions?marketplaceId='.APLUS_MP);
});

it('postContentDocumentSuspendSubmission POSTs /contentDocuments/{key}/suspendSubmissions?marketplaceId=', function () {
    Http::fake([APLUS_BASE.'/*' => Http::response([], 200)]);

    aplusContentEndpoint()->postContentDocumentSuspendSubmission('CRK 1', APLUS_MP);

    aplusContentAssertSent('POST', APLUS_BASE.'/contentDocuments/CRK%201/suspendSubmissions?marketplaceId='.APLUS_MP);
});

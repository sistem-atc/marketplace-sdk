<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Pack\PackMethods;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function packMethodsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function packMethods(): PackMethods
{
    $integration = packMethodsIntegration();

    return new PackMethods(HttpClientFactory::make($integration), $integration);
}

beforeEach(function () {
    config([
        'marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.access_token_ttl_seconds' => 21600,
        'mercadolivre.default_site_id' => 'MLB',
    ]);
    Http::preventStrayRequests();
});

describe('PackMethods::get', function () {
    it('faz GET /packs/{id} com Bearer', function () {
        Http::fake(['api.mercadolibre.com/packs/2000006181551917' => Http::response([
            'id' => 2000006181551917,
            'status' => 'released',
            'shipment' => ['id' => 43729529445],
            'orders' => [['id' => 2000009047722568], ['id' => 2000009047707726]],
        ])]);

        $out = packMethods()->get(2000006181551917);

        expect($out['orders'])->toHaveCount(2);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/packs/2000006181551917'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });
});

describe('PackMethods notas informativas', function () {
    it('notes faz GET /packs/{id}/notes com X-Public e access_token na query', function () {
        Http::fake(['api.mercadolibre.com/packs/20000154314645307/notes*' => Http::response([['id' => 'n1']])]);

        packMethods()->notes(20000154314645307);

        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && str_starts_with($req->url(), 'https://api.mercadolibre.com/packs/20000154314645307/notes?')
            && str_contains($req->url(), 'access_token=ml-bearer')
            && $req->hasHeader('X-Public', 'true')
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('note faz GET /packs/{id}/notes/{noteId}', function () {
        Http::fake(['api.mercadolibre.com/packs/20000154314645307/notes/681bace17c69893ae6558c52*' => Http::response(['id' => '681bace17c69893ae6558c52'])]);

        $out = packMethods()->note(20000154314645307, '681bace17c69893ae6558c52');

        expect($out['id'])->toBe('681bace17c69893ae6558c52');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && str_starts_with($req->url(), 'https://api.mercadolibre.com/packs/20000154314645307/notes/681bace17c69893ae6558c52?')
            && $req->hasHeader('X-Public', 'true'));
    });

    it('createNote faz POST com body {note} e X-Public', function () {
        Http::fake(['api.mercadolibre.com/packs/20000154314645307/notes*' => Http::response(['note' => ['id' => 'n2', 'note' => 'from postman']])]);

        $out = packMethods()->createNote(20000154314645307, 'from postman');

        expect($out['note']['id'])->toBe('n2');
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && str_starts_with($req->url(), 'https://api.mercadolibre.com/packs/20000154314645307/notes?')
            && $req->data() === ['note' => 'from postman']
            && $req->hasHeader('X-Public', 'true')
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('updateNote faz PUT /packs/{id}/notes/{noteId} com body {note}', function () {
        Http::fake(['api.mercadolibre.com/packs/20000154314645307/notes/n2*' => Http::response(['id' => 'n2', 'note' => 'editada'])]);

        packMethods()->updateNote(20000154314645307, 'n2', 'editada');

        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && str_starts_with($req->url(), 'https://api.mercadolibre.com/packs/20000154314645307/notes/n2?')
            && $req->data() === ['note' => 'editada']
            && $req->hasHeader('X-Public', 'true'));
    });
});

describe('PackMethods fiscal_documents', function () {
    it('fiscalDocuments faz GET /packs/{id}/fiscal_documents', function () {
        Http::fake(['api.mercadolibre.com/packs/2000000089077943/fiscal_documents' => Http::response([
            'pack_id' => 2000000089077943,
            'fiscal_documents' => [['id' => 'fc76', 'file_type' => 'application/xml', 'filename' => 'factura.xml']],
        ])]);

        $out = packMethods()->fiscalDocuments(2000000089077943);

        expect($out['fiscal_documents'][0]['filename'])->toBe('factura.xml');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/packs/2000000089077943/fiscal_documents'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('downloadFiscalDocument devolve o corpo bruto', function () {
        Http::fake(['api.mercadolibre.com/packs/2000000089077943/fiscal_documents/415460047_a96d8dea-38cd-4402-938e-80a1c134fc5d' => Http::response('<?xml version="1.0"?><nfeProc/>')]);

        $body = packMethods()->downloadFiscalDocument(2000000089077943, '415460047_a96d8dea-38cd-4402-938e-80a1c134fc5d');

        expect($body)->toContain('<nfeProc/>');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/packs/2000000089077943/fiscal_documents/415460047_a96d8dea-38cd-4402-938e-80a1c134fc5d'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('downloadFiscalDocument estoura exception em 404', function () {
        Http::fake(['api.mercadolibre.com/packs/1/fiscal_documents/x' => Http::response(['message' => 'not found'], 404)]);

        packMethods()->downloadFiscalDocument(1, 'x');
    })->throws(MercadoLivreRequestException::class);

    it('attachFiscalDocuments faz POST multipart com fiscal_document repetido (pdf + xml)', function () {
        Http::fake(['api.mercadolibre.com/packs/2000000089077943/fiscal_documents' => Http::response(['ids' => ['id-pdf', 'id-xml']])]);

        $out = packMethods()->attachFiscalDocuments(2000000089077943, [
            ['contents' => '%PDF-1.4 fake', 'filename' => 'nota_fiscal.pdf'],
            ['contents' => '<?xml version="1.0" encoding="UTF-8"?><nfeProc/>', 'filename' => 'nota_fiscal.xml'],
        ]);

        expect($out['ids'])->toHaveCount(2);
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/packs/2000000089077943/fiscal_documents'
            && $req->isMultipart()
            && $req->hasFile('fiscal_document', '%PDF-1.4 fake', 'nota_fiscal.pdf')
            && $req->hasFile('fiscal_document', '<?xml version="1.0" encoding="UTF-8"?><nfeProc/>', 'nota_fiscal.xml')
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('attachFiscalDocuments recusa lista vazia ou > 2 arquivos', function () {
        Http::fake();

        expect(fn () => packMethods()->attachFiscalDocuments(1, []))->toThrow(InvalidArgumentException::class);
        expect(fn () => packMethods()->attachFiscalDocuments(1, [
            ['contents' => 'a', 'filename' => 'a.pdf'],
            ['contents' => 'b', 'filename' => 'b.xml'],
            ['contents' => 'c', 'filename' => 'c.xml'],
        ]))->toThrow(InvalidArgumentException::class);
        Http::assertNothingSent();
    });

    it('deleteFiscalDocuments faz DELETE /packs/{id}/fiscal_documents', function () {
        Http::fake(['api.mercadolibre.com/packs/2000000089077943/fiscal_documents' => Http::response(['message' => 'deleted'])]);

        $out = packMethods()->deleteFiscalDocuments(2000000089077943);

        expect($out['message'])->toBe('deleted');
        Http::assertSent(fn ($req) => $req->method() === 'DELETE'
            && $req->url() === 'https://api.mercadolibre.com/packs/2000000089077943/fiscal_documents'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });
});

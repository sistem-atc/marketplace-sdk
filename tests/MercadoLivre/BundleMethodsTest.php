<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Bundle\BundleMethods;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function bundleTestIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function bundleTestMethods(): BundleMethods
{
    $integration = bundleTestIntegration();

    return new BundleMethods(HttpClientFactory::make($integration), $integration);
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

describe('BundleMethods', function () {
    it('validateUser: GET /soe/bundles/users/validate', function () {
        Http::fake(['api.mercadolibre.com/soe/bundles/users/validate' => Http::response(['active' => true])]);

        expect(bundleTestMethods()->validateUser()['active'])->toBeTrue();
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/soe/bundles/users/validate'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('create: POST /soe/bundles com name + volumes como string', function () {
        Http::fake(['api.mercadolibre.com/soe/bundles' => Http::response(['id' => 2073, 'status' => 'OPENED', 'hash' => 'abcd'])]);

        expect(bundleTestMethods()->create('GRUPO 7', [123, '234'])['id'])->toBe(2073);
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/soe/bundles'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer')
            && $req['name'] === 'GRUPO 7'
            && $req['volumes'] === ['123', '234']);
    });

    it('create: sem volumes nao manda a chave', function () {
        Http::fake(['api.mercadolibre.com/soe/bundles' => Http::response(['id' => 1])]);

        bundleTestMethods()->create('X');
        Http::assertSent(fn ($req) => ! array_key_exists('volumes', $req->data()));
    });

    it('list: GET /soe/bundles', function () {
        Http::fake(['api.mercadolibre.com/soe/bundles' => Http::response([['id' => 2073]])]);

        expect(bundleTestMethods()->list()[0]['id'])->toBe(2073);
        Http::assertSent(fn ($req) => $req->method() === 'GET' && $req->url() === 'https://api.mercadolibre.com/soe/bundles');
    });

    it('search: GET /soe/bundles/search?bundle_reference=', function () {
        Http::fake(['api.mercadolibre.com/soe/bundles/search*' => Http::response([['id' => 1]])]);

        bundleTestMethods()->search('ABCD1234');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/soe/bundles/search?bundle_reference=ABCD1234'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('summary: GET /soe/bundles/{id}/summary', function () {
        Http::fake(['api.mercadolibre.com/soe/bundles/12345/summary' => Http::response(['id' => 12345])]);

        expect(bundleTestMethods()->summary(12345)['id'])->toBe(12345);
        Http::assertSent(fn ($req) => $req->url() === 'https://api.mercadolibre.com/soe/bundles/12345/summary');
    });

    it('update: PUT /soe/bundles/{id} so com campos informados', function () {
        Http::fake(['api.mercadolibre.com/soe/bundles/789' => Http::response(['id' => 789, 'status' => 'CLOSED'])]);

        bundleTestMethods()->update(789, status: 'CLOSED');
        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === 'https://api.mercadolibre.com/soe/bundles/789'
            && $req['status'] === 'CLOSED'
            && ! array_key_exists('name', $req->data()));
    });

    it('volumes / searchVolumes: GET /soe/bundles/{id}/volumes[/search]', function () {
        Http::fake(['api.mercadolibre.com/soe/bundles/12345/volumes*' => Http::response([['id' => 1]])]);

        bundleTestMethods()->volumes(12345);
        bundleTestMethods()->searchVolumes(12345, '451235132');

        Http::assertSent(fn ($req) => $req->method() === 'GET' && $req->url() === 'https://api.mercadolibre.com/soe/bundles/12345/volumes');
        Http::assertSent(fn ($req) => $req->method() === 'GET' && $req->url() === 'https://api.mercadolibre.com/soe/bundles/12345/volumes/search?volume_reference=451235132');
    });

    it('addVolumes: PUT /soe/bundles/{id}/volumes com volumes + hash', function () {
        Http::fake(['api.mercadolibre.com/soe/bundles/12345/volumes' => Http::response('', 200)]);

        bundleTestMethods()->addVolumes(12345, [123456789, 987654321], 'abcd1234');
        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === 'https://api.mercadolibre.com/soe/bundles/12345/volumes'
            && $req['volumes'] === ['123456789', '987654321']
            && $req['hash'] === 'abcd1234');
    });

    it('removeVolumes: DELETE /soe/bundles/{id}/volumes com body JSON', function () {
        Http::fake(['api.mercadolibre.com/soe/bundles/12345/volumes' => Http::response('', 200)]);

        bundleTestMethods()->removeVolumes(12345, ['123456789'], 'abcd1234');
        Http::assertSent(fn ($req) => $req->method() === 'DELETE'
            && $req->url() === 'https://api.mercadolibre.com/soe/bundles/12345/volumes'
            && $req['volumes'] === ['123456789']
            && $req['hash'] === 'abcd1234'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('downloadFile: GET /soe/bundles/{id}/file devolve corpo cru', function () {
        Http::fake(['api.mercadolibre.com/soe/bundles/789/file' => Http::response("id,shipment\n1,2", 200, ['Content-Type' => 'text/csv'])]);

        expect(bundleTestMethods()->downloadFile(789))->toStartWith('id,shipment');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/soe/bundles/789/file'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('downloadFile: erro HTTP lanca MercadoLivreRequestException', function () {
        Http::fake(['api.mercadolibre.com/soe/bundles/789/file' => Http::response('', 404)]);

        bundleTestMethods()->downloadFile(789);
    })->throws(MercadoLivreRequestException::class);

    it('label: POST /soe/bundles/label com bundle_id e format opcional', function () {
        Http::fake(['api.mercadolibre.com/soe/bundles/label' => Http::response(['file' => 'YmFzZTY0', 'content_type' => 'text/plain', 'file_name' => 'ETIQUETA_789'])]);

        expect(bundleTestMethods()->label(789, 'zpl')['file_name'])->toBe('ETIQUETA_789');
        bundleTestMethods()->label(789);

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/soe/bundles/label'
            && $req['bundle_id'] === 789
            && ($req->data()['format'] ?? null) === 'zpl');
        Http::assertSent(fn ($req) => $req['bundle_id'] === 789 && ! array_key_exists('format', $req->data()));
    });
});

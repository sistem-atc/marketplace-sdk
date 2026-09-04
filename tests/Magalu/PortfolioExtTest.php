<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Product\PortfolioMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluPortfolioExtIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-portfolioext',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-portfolioext'],
        active: true,
        expired: false,
    );
}

function magaluPortfolioExtClient(): PortfolioMethods
{
    $integration = magaluPortfolioExtIntegration();

    return new PortfolioMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluPortfolioExtSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
{
    Http::assertSent(function (Request $req) use ($method, $url, $query, $body, $headers): bool {
        $parts = parse_url($req->url());
        $actualUrl = $parts['scheme'].'://'.$parts['host'].($parts['path'] ?? '');
        // parse_str() troca '.' por '_' (seller.id → seller_id); parse manual preserva a chave.
        $actualQuery = [];
        foreach (array_filter(explode('&', $parts['query'] ?? '')) as $pair) {
            [$k, $v] = array_pad(explode('=', $pair, 2), 2, '');
            $actualQuery[urldecode($k)] = urldecode($v);
        }

        if ($req->method() !== $method || $actualUrl !== $url) return false;
        if ($actualQuery != $query) return false;
        if (! $req->hasHeader('Authorization', 'Bearer jwt-portfolioext')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-portfolioext')) return false;
        foreach ($headers as $k => $v) {
            if (! $req->hasHeader($k, $v)) return false;
        }
        if ($body !== null && json_decode($req->body(), true) !== $body) return false;

        return true;
    });
}

beforeEach(function () {
    config([
        'marketplaces.magalu.api_base' => 'https://api.magalu.com',
        'marketplaces.magalu.services_base' => 'https://services.magalu.com',
        'marketplaces.magalu.token_url' => 'https://autoseg-idp.luizalabs.com/oauth/token',
    ]);
    Http::fake(fn (Request $req) => str_contains($req->url(), '/attachments/')
        ? Http::response('BIN', 200, ['Content-Type' => 'image/png'])
        : Http::response(['ok' => true, 'results' => []]));
});

describe('PortfolioMethods', function () {
    it('searchSkus: GET portfolios/skus com status/group_id/_sort', function () {
        $resp = magaluPortfolioExtClient()->searchSkus(['status' => 'PUBLISHED', 'group_id' => 'g1'], limit: 10, offset: 0, sort: 'updated_at:desc');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('GET', 'https://api.magalu.com/seller/v1/portfolios/skus', ['status' => 'PUBLISHED', 'group_id' => 'g1', '_limit' => '10', '_offset' => '0', '_sort' => 'updated_at:desc'], null);
    });

    it('getSku: GET portfolios/skus/{sku}', function () {
        $resp = magaluPortfolioExtClient()->getSku('SKU 1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('GET', 'https://api.magalu.com/seller/v1/portfolios/skus/SKU%201', [], null);
    });

    it('createSku: POST portfolios/skus', function () {
        $resp = magaluPortfolioExtClient()->createSku(['sku' => 'A', 'title' => 'T', 'active' => true]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('POST', 'https://api.magalu.com/seller/v1/portfolios/skus', [], ['sku' => 'A', 'title' => 'T', 'active' => true]);
    });

    it('replaceSku: PUT portfolios/skus/{sku}', function () {
        $resp = magaluPortfolioExtClient()->replaceSku('A', ['title' => 'T2']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('PUT', 'https://api.magalu.com/seller/v1/portfolios/skus/A', [], ['title' => 'T2']);
    });

    it('patchSku: PATCH portfolios/skus/{sku}', function () {
        $resp = magaluPortfolioExtClient()->patchSku('A', ['brand' => 'B']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('PATCH', 'https://api.magalu.com/seller/v1/portfolios/skus/A', [], ['brand' => 'B']);
    });

    it('skuValidationInfo: GET portfolios/skus/{sku}/validation-info', function () {
        $resp = magaluPortfolioExtClient()->skuValidationInfo('A');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('GET', 'https://api.magalu.com/seller/v1/portfolios/skus/A/validation-info', [], null);
    });

    it('createPrice: POST portfolios/prices/{sku} em centavos', function () {
        $resp = magaluPortfolioExtClient()->createPrice('A', 'ch-1', listPrice: 9999, price: 9980);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('POST', 'https://api.magalu.com/seller/v1/portfolios/prices/A', [], ['channel' => ['id' => 'ch-1'], 'currency' => 'BRL', 'list_price' => 9999, 'price' => 9980, 'normalizer' => 100]);
    });

    it('patchPrice: PATCH portfolios/prices/{sku}', function () {
        $resp = magaluPortfolioExtClient()->patchPrice('A', ['channel' => ['id' => 'ch-1'], 'price' => 9000]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('PATCH', 'https://api.magalu.com/seller/v1/portfolios/prices/A', [], ['channel' => ['id' => 'ch-1'], 'price' => 9000]);
    });

    it('createStock: POST portfolios/stocks/{sku} com branch', function () {
        $resp = magaluPortfolioExtClient()->createStock('A', 'ch-1', 5, branchId: 'br-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('POST', 'https://api.magalu.com/seller/v1/portfolios/stocks/A', [], ['channel' => ['id' => 'ch-1'], 'quantity' => 5, 'type' => 'AVAILABLE', 'branch' => ['id' => 'br-1']]);
    });

    it('patchStock: PATCH portfolios/stocks/{sku}', function () {
        $resp = magaluPortfolioExtClient()->patchStock('A', 'ch-1', 0);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('PATCH', 'https://api.magalu.com/seller/v1/portfolios/stocks/A', [], ['channel' => ['id' => 'ch-1'], 'quantity' => 0, 'type' => 'AVAILABLE']);
    });

    it('listVideos: GET portfolios/medias/videos', function () {
        $resp = magaluPortfolioExtClient()->listVideos(['status' => 'published'], limit: 10, offset: 0);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('GET', 'https://api.magalu.com/seller/v1/portfolios/medias/videos', ['status' => 'published', '_limit' => '10', '_offset' => '0'], null);
    });

    it('listVideosBySku: GET portfolios/medias/{sku}/videos', function () {
        $resp = magaluPortfolioExtClient()->listVideosBySku('A');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('GET', 'https://api.magalu.com/seller/v1/portfolios/medias/A/videos', ['_limit' => '10', '_offset' => '0'], null);
    });

    it('getVideo: GET portfolios/medias/{sku}/videos/{id}', function () {
        $resp = magaluPortfolioExtClient()->getVideo('A', 'v-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('GET', 'https://api.magalu.com/seller/v1/portfolios/medias/A/videos/v-1', [], null);
    });

    it('createVideo: POST portfolios/medias/{sku}/videos', function () {
        $resp = magaluPortfolioExtClient()->createVideo('A', 'Titulo');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('POST', 'https://api.magalu.com/seller/v1/portfolios/medias/A/videos', [], ['title' => 'Titulo', 'file_type' => 'MP4']);
    });

    it('createVideoBatch: POST portfolios/medias/videos com lista de sku', function () {
        $resp = magaluPortfolioExtClient()->createVideoBatch(['A', 'B'], 'Titulo', fileType: 'HLS');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('POST', 'https://api.magalu.com/seller/v1/portfolios/medias/videos', [], ['title' => 'Titulo', 'file_type' => 'HLS', 'sku' => ['A', 'B']]);
    });

    it('markVideoUploaded: PATCH medias/{sku}/videos/{id} status=upload_complete', function () {
        $resp = magaluPortfolioExtClient()->markVideoUploaded('A', 'v-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('PATCH', 'https://api.magalu.com/seller/v1/portfolios/medias/A/videos/v-1', [], ['status' => 'upload_complete']);
    });

    it('confirmVideoUpload: POST portfolios/videos/{id}/confirm-upload', function () {
        $resp = magaluPortfolioExtClient()->confirmVideoUpload('v-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPortfolioExtSent('POST', 'https://api.magalu.com/seller/v1/portfolios/videos/v-1/confirm-upload', [], null);
    });
});

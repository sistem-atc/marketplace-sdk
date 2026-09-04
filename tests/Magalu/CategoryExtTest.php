<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Product\CategoryMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluCategoryExtIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-categoryext',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-categoryext'],
        active: true,
        expired: false,
    );
}

function magaluCategoryExtClient(): CategoryMethods
{
    $integration = magaluCategoryExtIntegration();

    return new CategoryMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluCategoryExtSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-categoryext')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-categoryext')) return false;
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

describe('CategoryMethods', function () {
    it('listCategories: GET portfolios/categories (plural) com filtros', function () {
        $resp = magaluCategoryExtClient()->listCategories(['name' => 'Whey'], limit: 10, offset: 0, sort: 'name:asc');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCategoryExtSent('GET', 'https://api.magalu.com/seller/v1/portfolios/categories', ['name' => 'Whey', '_limit' => '10', '_offset' => '0', '_sort' => 'name:asc'], null);
    });

    it('listHierarchy: GET portfolios/categories/hierarchy com root_only', function () {
        $resp = magaluCategoryExtClient()->listHierarchy(['root_only' => 'true']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCategoryExtSent('GET', 'https://api.magalu.com/seller/v1/portfolios/categories/hierarchy', ['root_only' => 'true', '_limit' => '50', '_offset' => '0'], null);
    });

    it('listAttributes: GET categories/{id}/attributes com required', function () {
        $resp = magaluCategoryExtClient()->listAttributes('cat-1', required: 'required');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCategoryExtSent('GET', 'https://api.magalu.com/seller/v1/portfolios/categories/cat-1/attributes', ['_limit' => '50', '_offset' => '0', 'required' => 'required'], null);
    });

    it('listDatasheet: GET categories/{id}/datasheet', function () {
        $resp = magaluCategoryExtClient()->listDatasheet('cat-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCategoryExtSent('GET', 'https://api.magalu.com/seller/v1/portfolios/categories/cat-1/datasheet', ['_limit' => '50', '_offset' => '0'], null);
    });

    it('channelAttributes: GET /channel/v1/portfolios/attributes', function () {
        $resp = magaluCategoryExtClient()->channelAttributes(['name' => 'Cor', 'active' => 'true']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCategoryExtSent('GET', 'https://api.magalu.com/channel/v1/portfolios/attributes', ['name' => 'Cor', 'active' => 'true', '_limit' => '50', '_offset' => '0'], null);
    });

    it('channelCategoryAttributes: GET /channel/v1/portfolios/categories/{id}/attributes', function () {
        $resp = magaluCategoryExtClient()->channelCategoryAttributes('cat-1', required: 'optional');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCategoryExtSent('GET', 'https://api.magalu.com/channel/v1/portfolios/categories/cat-1/attributes', ['required' => 'optional'], null);
    });

    it('channelCategoryDatasheet: GET /channel/v1/portfolios/categories/{id}/datasheet', function () {
        $resp = magaluCategoryExtClient()->channelCategoryDatasheet('cat-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCategoryExtSent('GET', 'https://api.magalu.com/channel/v1/portfolios/categories/cat-1/datasheet', [], null);
    });
});

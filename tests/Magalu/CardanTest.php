<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Cardan\CardanMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluCardanIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-cardan',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-cardan'],
        active: true,
        expired: false,
    );
}

function magaluCardanClient(): CardanMethods
{
    $integration = magaluCardanIntegration();

    return new CardanMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluCardanSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-cardan')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-cardan')) return false;
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

describe('CardanMethods', function () {
    it('listManufacturers: GET vehicles/manufacturers', function () {
        $resp = magaluCardanClient()->listManufacturers(limit: 10, offset: 0, sort: 'name:asc');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCardanSent('GET', 'https://api.magalu.com/seller/v1/portfolios/vehicles/manufacturers', ['_limit' => '10', '_offset' => '0', '_sort' => 'name:asc'], null);
    });

    it('listModels: GET manufacturers/{id}/models', function () {
        $resp = magaluCardanClient()->listModels('m-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCardanSent('GET', 'https://api.magalu.com/seller/v1/portfolios/vehicles/manufacturers/m-1/models', ['_limit' => '50', '_offset' => '0'], null);
    });

    it('listYears: GET models/{id}/years', function () {
        $resp = magaluCardanClient()->listYears('m-1', 'mod-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCardanSent('GET', 'https://api.magalu.com/seller/v1/portfolios/vehicles/manufacturers/m-1/models/mod-1/years', ['_limit' => '50', '_offset' => '0'], null);
    });

    it('listVersions: GET years/{id}/versions', function () {
        $resp = magaluCardanClient()->listVersions('m-1', 'mod-1', 'y-2020');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCardanSent('GET', 'https://api.magalu.com/seller/v1/portfolios/vehicles/manufacturers/m-1/models/mod-1/years/y-2020/versions', ['_limit' => '50', '_offset' => '0'], null);
    });

    it('createCompatibilities: POST vehicles/compatibilities', function () {
        $resp = magaluCardanClient()->createCompatibilities('SKU1', ['v-1', 'v-2'], partNumber: 'PN-9');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCardanSent('POST', 'https://api.magalu.com/seller/v1/portfolios/vehicles/compatibilities', [], ['sku' => 'SKU1', 'vehicle_version_ids' => ['v-1', 'v-2'], 'part_number' => 'PN-9']);
    });

    it('upsertCompatibilities: PUT vehicles/compatibilities', function () {
        $resp = magaluCardanClient()->upsertCompatibilities('SKU1', ['v-3']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCardanSent('PUT', 'https://api.magalu.com/seller/v1/portfolios/vehicles/compatibilities', [], ['sku' => 'SKU1', 'vehicle_version_ids' => ['v-3']]);
    });

    it('deleteCompatibilities: DELETE vehicles/compatibilities com body {sku}', function () {
        $resp = magaluCardanClient()->deleteCompatibilities('SKU1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCardanSent('DELETE', 'https://api.magalu.com/seller/v1/portfolios/vehicles/compatibilities', [], ['sku' => 'SKU1']);
    });
});

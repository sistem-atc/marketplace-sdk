<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Seller\SellerMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluSellerIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-seller',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-seller'],
        active: true,
        expired: false,
    );
}

function magaluSellerClient(): SellerMethods
{
    $integration = magaluSellerIntegration();

    return new SellerMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluSellerSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-seller')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-seller')) return false;
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

describe('SellerMethods', function () {
    it('me: GET /seller/v1/portfolios/me', function () {
        $resp = magaluSellerClient()->me();

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSellerSent('GET', 'https://api.magalu.com/seller/v1/portfolios/me', [], null);
    });

    it('myWarehouses: GET /seller/v1/portfolios/me/warehouses', function () {
        $resp = magaluSellerClient()->myWarehouses();

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSellerSent('GET', 'https://api.magalu.com/seller/v1/portfolios/me/warehouses', [], null);
    });

    it('productScores: GET products/scores com X-Channel-Id e limit/offset', function () {
        $resp = magaluSellerClient()->productScores('ch-1', limit: 10, offset: 20);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSellerSent('GET', 'https://api.magalu.com/seller/v1/portfolios/products/scores', ['limit' => '10', 'offset' => '20'], null, ['X-Channel-Id' => 'ch-1']);
    });

    it('listInventories: GET services /logistics/v1/inventories com sku__in', function () {
        $resp = magaluSellerClient()->listInventories(['A', 'B'], limit: 10);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSellerSent('GET', 'https://services.magalu.com/logistics/v1/inventories', ['_limit' => '10', '_offset' => '0', 'sku__in' => 'A,B'], null);
    });

    it('requestInventoryReport: POST /logistics/v1/inventories/reports', function () {
        $resp = magaluSellerClient()->requestInventoryReport('org-1', 'magalu', subType: 'kardex_fulfillment', fileExtension: 'xlsx', parameters: ['x' => 1]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSellerSent('POST', 'https://services.magalu.com/logistics/v1/inventories/reports', [], ['type' => 'stock_logistics', 'sub_type' => 'kardex_fulfillment', 'org' => 'org-1', 'channel' => 'magalu', 'file_extension' => 'xlsx', 'parameters' => ['x' => 1]]);
    });

    it('requestInventoryReportBatch: POST /logistics/v1/inventories/reports/batch', function () {
        $resp = magaluSellerClient()->requestInventoryReportBatch([['org' => 'org-1', 'channel' => 'magalu']]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSellerSent('POST', 'https://services.magalu.com/logistics/v1/inventories/reports/batch', [], ['reports' => [['type' => 'stock_logistics', 'sub_type' => 'inventory_kardex', 'org' => 'org-1', 'channel' => 'magalu', 'file_extension' => 'csv']]]);
    });

    it('getInventoryReport: GET /logistics/v1/inventories/reports/{id}', function () {
        $resp = magaluSellerClient()->getInventoryReport('rep-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSellerSent('GET', 'https://services.magalu.com/logistics/v1/inventories/reports/rep-1', [], null);
    });
});

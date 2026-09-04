<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Logistics\LogisticsMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluLogisticsExtIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-logisticsext',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-logisticsext'],
        active: true,
        expired: false,
    );
}

function magaluLogisticsExtClient(): LogisticsMethods
{
    $integration = magaluLogisticsExtIntegration();

    return new LogisticsMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluLogisticsExtSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-logisticsext')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-logisticsext')) return false;
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

describe('LogisticsMethods', function () {
    it('requestShippingLabels: POST logistics/shipping-labels com channel + deliveries + label', function () {
        $resp = magaluLogisticsExtClient()->requestShippingLabels(['d-1', 'd-2'], 'ch-1', format: 'pdf', type: 'shipping');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluLogisticsExtSent('POST', 'https://api.magalu.com/seller/v1/logistics/shipping-labels', [], ['channel' => ['id' => 'ch-1'], 'deliveries' => [['id' => 'd-1'], ['id' => 'd-2']], 'label' => ['format' => 'pdf', 'type' => 'shipping']]);
    });

    it('listWarehouses: GET /seller/v1/logistics/warehouses', function () {
        $resp = magaluLogisticsExtClient()->listWarehouses(limit: 10, offset: 20);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluLogisticsExtSent('GET', 'https://api.magalu.com/seller/v1/logistics/warehouses', ['_limit' => '10', '_offset' => '20'], null);
    });
});

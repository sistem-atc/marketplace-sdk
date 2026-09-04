<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluOrderExtIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-orderext',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-orderext'],
        active: true,
        expired: false,
    );
}

function magaluOrderExtClient(): OrderMethods
{
    $integration = magaluOrderExtIntegration();

    return new OrderMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluOrderExtSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-orderext')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-orderext')) return false;
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

describe('OrderMethods', function () {
    it('search: GET /seller/v1/orders com filtros, _limit/_offset/_sort', function () {
        $resp = magaluOrderExtClient()->search(['status' => 'approved', 'purchased_at__gte' => '2026-01-01T00:00:00+00:00'], limit: 50, offset: 100, sort: 'purchased_at:desc');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluOrderExtSent('GET', 'https://api.magalu.com/seller/v1/orders', ['status' => 'approved', 'purchased_at__gte' => '2026-01-01T00:00:00+00:00', '_limit' => '50', '_offset' => '100', '_sort' => 'purchased_at:desc'], null);
    });

    it('getOrderRaw: GET /seller/v1/orders/{code} com updated_at__ge', function () {
        $resp = magaluOrderExtClient()->getOrderRaw('ORD-1', updatedAtGe: '2026-01-01T00:00:00+00:00');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluOrderExtSent('GET', 'https://api.magalu.com/seller/v1/orders/ORD-1', ['updated_at__ge' => '2026-01-01T00:00:00+00:00'], null);
    });
});

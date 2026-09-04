<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Delivery\DeliveryMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluDeliveryExtIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-deliveryext',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-deliveryext'],
        active: true,
        expired: false,
    );
}

function magaluDeliveryExtClient(): DeliveryMethods
{
    $integration = magaluDeliveryExtIntegration();

    return new DeliveryMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluDeliveryExtSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-deliveryext')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-deliveryext')) return false;
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

describe('DeliveryMethods', function () {
    it('search: GET /seller/v1/deliveries com filtros', function () {
        $resp = magaluDeliveryExtClient()->search(['status' => 'invoiced', 'code' => 'ORD-1'], limit: 20, offset: 0, sort: 'purchased_at:asc');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluDeliveryExtSent('GET', 'https://api.magalu.com/seller/v1/deliveries', ['status' => 'invoiced', 'code' => 'ORD-1', '_limit' => '20', '_offset' => '0', '_sort' => 'purchased_at:asc'], null);
    });

    it('getDeliveryRaw: GET /seller/v1/deliveries/{id}', function () {
        $resp = magaluDeliveryExtClient()->getDeliveryRaw('d-1', updatedAtGe: '2026-01-01T00:00:00Z');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluDeliveryExtSent('GET', 'https://api.magalu.com/seller/v1/deliveries/d-1', ['updated_at__ge' => '2026-01-01T00:00:00Z'], null);
    });

    it('listHistories: GET deliveries/{id}/histories', function () {
        $resp = magaluDeliveryExtClient()->listHistories('d-1', ['created_at__ge' => '2026-01-01T00:00:00Z'], limit: 10, offset: 0, sort: 'created_at:desc');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluDeliveryExtSent('GET', 'https://api.magalu.com/seller/v1/deliveries/d-1/histories', ['created_at__ge' => '2026-01-01T00:00:00Z', '_limit' => '10', '_offset' => '0', '_sort' => 'created_at:desc'], null);
    });

    it('registerShipping: POST deliveries/{id}/shippings', function () {
        $resp = magaluDeliveryExtClient()->registerShipping('d-1', ['carrier' => ['name' => 'Correios'], 'channel' => ['id' => 'ch-1'], 'protocol' => 'BR1']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluDeliveryExtSent('POST', 'https://api.magalu.com/seller/v1/deliveries/d-1/shippings', [], ['carrier' => ['name' => 'Correios'], 'channel' => ['id' => 'ch-1'], 'protocol' => 'BR1']);
    });

    it('finish: POST deliveries/{id}/finishing', function () {
        $resp = magaluDeliveryExtClient()->finish('d-1', 'ch-1', deliveredAt: '2026-09-01T10:00:00Z', channelExtras: ['k' => 'v']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluDeliveryExtSent('POST', 'https://api.magalu.com/seller/v1/deliveries/d-1/finishing', [], ['channel' => ['id' => 'ch-1', 'extras' => ['k' => 'v']], 'delivered_at' => '2026-09-01T10:00:00Z']);
    });
});

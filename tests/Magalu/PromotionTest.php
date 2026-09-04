<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Promotion\PromotionMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluPromotionIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-promotion',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-promotion'],
        active: true,
        expired: false,
    );
}

function magaluPromotionClient(): PromotionMethods
{
    $integration = magaluPromotionIntegration();

    return new PromotionMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluPromotionSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-promotion')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-promotion')) return false;
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

describe('PromotionMethods', function () {
    it('list: GET /seller/v1/promotions com filtros e paginacao', function () {
        $resp = magaluPromotionClient()->list(['type' => 'percentage_discount', 'origin' => 'channel'], limit: 20, offset: 40, sort: 'created_at:asc');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPromotionSent('GET', 'https://api.magalu.com/seller/v1/promotions', ['type' => 'percentage_discount', 'origin' => 'channel', '_limit' => '20', '_offset' => '40', '_sort' => 'created_at:asc'], null);
    });

    it('get: GET /seller/v1/promotions/{id}', function () {
        $resp = magaluPromotionClient()->get('promo-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPromotionSent('GET', 'https://api.magalu.com/seller/v1/promotions/promo-1', [], null);
    });

    it('apply: POST /seller/v1/promotions/{id}/apply', function () {
        $resp = magaluPromotionClient()->apply('promo-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPromotionSent('POST', 'https://api.magalu.com/seller/v1/promotions/promo-1/apply', [], null);
    });

    it('listSkus: GET /seller/v1/promotions/{id}/skus', function () {
        $resp = magaluPromotionClient()->listSkus('promo-1', limit: 5, offset: 10, sort: 'created_at:desc');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPromotionSent('GET', 'https://api.magalu.com/seller/v1/promotions/promo-1/skus', ['_limit' => '5', '_offset' => '10', '_sort' => 'created_at:desc'], null);
    });

    it('getSku: GET /seller/v1/promotions/{id}/skus/{sku}', function () {
        $resp = magaluPromotionClient()->getSku('promo-1', 'SKU 1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPromotionSent('GET', 'https://api.magalu.com/seller/v1/promotions/promo-1/skus/SKU%201', [], null);
    });

    it('updateSku: PATCH com preco promocional e limite', function () {
        $resp = magaluPromotionClient()->updateSku('promo-1', 'SKU1', channelId: 'ch-1', promotionalPrice: 4990, inventoryLimit: 10);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPromotionSent('PATCH', 'https://api.magalu.com/seller/v1/promotions/promo-1/skus/SKU1', [], ['channel' => ['id' => 'ch-1'], 'price' => ['promotional' => ['value' => 4990, 'currency' => 'BRL', 'normalizer' => 100]], 'inventory' => ['limit' => 10]]);
    });

    it('removeSku: DELETE /seller/v1/promotions/{id}/skus/{sku}', function () {
        $resp = magaluPromotionClient()->removeSku('promo-1', 'SKU1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPromotionSent('DELETE', 'https://api.magalu.com/seller/v1/promotions/promo-1/skus/SKU1', [], null);
    });

    it('subscribe: POST subscriptions com channel e limit', function () {
        $resp = magaluPromotionClient()->subscribe('promo-1', channelId: 'ch-1', limit: 100);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPromotionSent('POST', 'https://api.magalu.com/seller/v1/promotions/promo-1/subscriptions', [], ['channel' => ['id' => 'ch-1'], 'limit' => 100]);
    });

    it('unsubscribe: DELETE subscriptions', function () {
        $resp = magaluPromotionClient()->unsubscribe('promo-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPromotionSent('DELETE', 'https://api.magalu.com/seller/v1/promotions/promo-1/subscriptions', [], null);
    });
});

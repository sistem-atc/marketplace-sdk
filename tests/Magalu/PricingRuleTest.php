<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\PricingRule\PricingRuleMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluPricingRuleIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-pricingrule',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-pricingrule'],
        active: true,
        expired: false,
    );
}

function magaluPricingRuleClient(): PricingRuleMethods
{
    $integration = magaluPricingRuleIntegration();

    return new PricingRuleMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluPricingRuleSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-pricingrule')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-pricingrule')) return false;
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

describe('PricingRuleMethods', function () {
    it('active: GET /seller/v1/pricing-rules/active com channel_id e sku', function () {
        $resp = magaluPricingRuleClient()->active('ch-1', 'SKU1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPricingRuleSent('GET', 'https://api.magalu.com/seller/v1/pricing-rules/active', ['channel_id' => 'ch-1', 'sku' => 'SKU1'], null);
    });

    it('create: POST /seller/v1/pricing-rules', function () {
        $resp = magaluPricingRuleClient()->create(channelId: 'ch-1', configType: 'SKU', ruleType: 'TO_WIN', minPrice: 50000, sku: 'SKU1', adjustment: ['type' => 'PERCENTAGE', 'value' => 500, 'normalize' => 100]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPricingRuleSent('POST', 'https://api.magalu.com/seller/v1/pricing-rules', [], ['channel' => ['id' => 'ch-1'], 'config_type' => 'SKU', 'rule_type' => 'TO_WIN', 'min_price' => 50000, 'sku' => 'SKU1', 'adjustment' => ['type' => 'PERCENTAGE', 'value' => 500, 'normalize' => 100]]);
    });

    it('update: PATCH /seller/v1/pricing-rules', function () {
        $resp = magaluPricingRuleClient()->update(['channel' => ['id' => 'ch-1'], 'min_price' => 55000, 'sku' => 'SKU1']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPricingRuleSent('PATCH', 'https://api.magalu.com/seller/v1/pricing-rules', [], ['channel' => ['id' => 'ch-1'], 'min_price' => 55000, 'sku' => 'SKU1']);
    });

    it('deleteCatalogRule: DELETE catalog/channel_id/{id}', function () {
        $resp = magaluPricingRuleClient()->deleteCatalogRule('ch-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPricingRuleSent('DELETE', 'https://api.magalu.com/seller/v1/pricing-rules/catalog/channel_id/ch-1', [], null);
    });

    it('deleteSkuRule: DELETE channel_id/{id}/sku/{sku}', function () {
        $resp = magaluPricingRuleClient()->deleteSkuRule('ch-1', 'SKU1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluPricingRuleSent('DELETE', 'https://api.magalu.com/seller/v1/pricing-rules/channel_id/ch-1/sku/SKU1', [], null);
    });
});

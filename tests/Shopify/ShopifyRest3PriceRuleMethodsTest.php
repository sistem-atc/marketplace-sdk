<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\Endpoints\Discount\PriceRuleMethods;

function shopifyRest3PriceRuleMethods(): SistemAtc\Marketplaces\Shopify\Endpoints\Discount\PriceRuleMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shpat_fake',
        refreshToken: null,
        settings: ['shop_domain' => 'test-store.myshopify.com'],
        active: true,
        expired: false,
    );

    return new SistemAtc\Marketplaces\Shopify\Endpoints\Discount\PriceRuleMethods(HttpClientFactory::make($integration), $integration);
}

/** Afirma verbo + URL completa + header do token (+ body, quando informado). */
function shopifyRest3PriceRuleSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(function (Request $r) use ($method, $url, $body): bool {
        return $r->method() === $method
            && $r->url() === $url
            && $r->hasHeader('X-Shopify-Access-Token', 'shpat_fake')
            && ($body === null || $r->data() === $body);
    });
}

beforeEach(function () {
    config(['marketplaces.shopify.api_version' => '2024-04']);
    Http::preventStrayRequests();
});

$base = 'https://test-store.myshopify.com/admin/api/2024-04';

describe('Shopify PriceRuleMethods (REST chunk 3)', function () use ($base) {
    it('list -> GET price_rules.json', function () use ($base) {
        Http::fake(['*' => Http::response(['price_rules' => []])]);
        shopifyRest3PriceRuleMethods()->list(['limit' => 10]);
        shopifyRest3PriceRuleSent('GET', "$base/price_rules.json?limit=10");
    });

    it('count -> GET price_rules/count.json', function () use ($base) {
        Http::fake(['*' => Http::response(['count' => 1])]);
        expect(shopifyRest3PriceRuleMethods()->count())->toBe(1);
        shopifyRest3PriceRuleSent('GET', "$base/price_rules/count.json");
    });

    it('get -> GET price_rules/{id}.json', function () use ($base) {
        Http::fake(['*' => Http::response(['price_rule' => ['id' => 3]])]);
        shopifyRest3PriceRuleMethods()->get(3);
        shopifyRest3PriceRuleSent('GET', "$base/price_rules/3.json");
    });

    it('create -> POST price_rules.json embrulhado', function () use ($base) {
        Http::fake(['*' => Http::response(['price_rule' => ['id' => 3]], 201)]);
        shopifyRest3PriceRuleMethods()->create(['title' => 'DEZ10', 'value_type' => 'percentage', 'value' => '-10.0']);
        shopifyRest3PriceRuleSent('POST', "$base/price_rules.json", ['price_rule' => ['title' => 'DEZ10', 'value_type' => 'percentage', 'value' => '-10.0']]);
    });

    it('update -> PUT price_rules/{id}.json embrulhado', function () use ($base) {
        Http::fake(['*' => Http::response(['price_rule' => ['id' => 3]])]);
        shopifyRest3PriceRuleMethods()->update(3, ['title' => 'DEZ15']);
        shopifyRest3PriceRuleSent('PUT', "$base/price_rules/3.json", ['price_rule' => ['title' => 'DEZ15']]);
    });

    it('delete -> DELETE price_rules/{id}.json', function () use ($base) {
        Http::fake(['*' => Http::response([])]);
        shopifyRest3PriceRuleMethods()->delete(3);
        shopifyRest3PriceRuleSent('DELETE', "$base/price_rules/3.json");
    });

    it('each segue o Link header', function () use ($base) {
        Http::fake(['*' => Http::sequence()
            ->push(['price_rules' => [['id' => 1]]], 200, ['Link' => "<$base/price_rules.json?limit=250&page_info=P2>; rel=\"next\""])
            ->push(['price_rules' => [['id' => 2]]], 200, [])]);
        expect(array_column(iterator_to_array(shopifyRest3PriceRuleMethods()->each()), 'id'))->toBe([1, 2]);
    });
});

<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\Endpoints\Product\ResourceFeedbackMethods;

function shopifyRest3ResourceFeedbackMethods(): SistemAtc\Marketplaces\Shopify\Endpoints\Product\ResourceFeedbackMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shpat_fake',
        refreshToken: null,
        settings: ['shop_domain' => 'test-store.myshopify.com'],
        active: true,
        expired: false,
    );

    return new SistemAtc\Marketplaces\Shopify\Endpoints\Product\ResourceFeedbackMethods(HttpClientFactory::make($integration), $integration);
}

/** Afirma verbo + URL completa + header do token (+ body, quando informado). */
function shopifyRest3ResourceFeedbackSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify ResourceFeedbackMethods (REST chunk 3)', function () use ($base) {
    $fb = ['state' => 'success', 'feedback_generated_at' => '2026-09-04T00:00:00Z', 'resource_updated_at' => '2026-09-04T00:00:00Z'];

    it('list -> GET resource_feedback.json', function () use ($base) {
        Http::fake(['*' => Http::response(['resource_feedback' => []])]);
        shopifyRest3ResourceFeedbackMethods()->list();
        shopifyRest3ResourceFeedbackSent('GET', "$base/resource_feedback.json");
    });

    it('create -> POST resource_feedback.json embrulhado', function () use ($base, $fb) {
        Http::fake(['*' => Http::response(['resource_feedback' => $fb], 202)]);
        shopifyRest3ResourceFeedbackMethods()->create($fb);
        shopifyRest3ResourceFeedbackSent('POST', "$base/resource_feedback.json", ['resource_feedback' => $fb]);
    });

    it('listForProduct -> GET products/{id}/resource_feedback.json', function () use ($base) {
        Http::fake(['*' => Http::response(['resource_feedback' => []])]);
        shopifyRest3ResourceFeedbackMethods()->listForProduct(10);
        shopifyRest3ResourceFeedbackSent('GET', "$base/products/10/resource_feedback.json");
    });

    it('createForProduct -> POST products/{id}/resource_feedback.json embrulhado', function () use ($base, $fb) {
        Http::fake(['*' => Http::response(['resource_feedback' => $fb], 202)]);
        shopifyRest3ResourceFeedbackMethods()->createForProduct(10, $fb);
        shopifyRest3ResourceFeedbackSent('POST', "$base/products/10/resource_feedback.json", ['resource_feedback' => $fb]);
    });
});

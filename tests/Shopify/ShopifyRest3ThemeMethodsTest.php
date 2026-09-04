<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\Endpoints\OnlineStore\ThemeMethods;

function shopifyRest3ThemeMethods(): SistemAtc\Marketplaces\Shopify\Endpoints\OnlineStore\ThemeMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shpat_fake',
        refreshToken: null,
        settings: ['shop_domain' => 'test-store.myshopify.com'],
        active: true,
        expired: false,
    );

    return new SistemAtc\Marketplaces\Shopify\Endpoints\OnlineStore\ThemeMethods(HttpClientFactory::make($integration), $integration);
}

/** Afirma verbo + URL completa + header do token (+ body, quando informado). */
function shopifyRest3ThemeSent(string $method, string $url, ?array $body = null): void
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

describe('Shopify ThemeMethods (REST chunk 3)', function () use ($base) {
    it('themes: list / get / create / update / delete', function () use ($base) {
        Http::fake(['*' => Http::response(['themes' => [], 'theme' => ['id' => 1]])]);
        $m = shopifyRest3ThemeMethods();
        $m->list();
        $m->get(1);
        $m->create(['name' => 'Novo', 'src' => 'https://x/y.zip']);
        $m->update(1, ['role' => 'main']);
        $m->delete(1);
        shopifyRest3ThemeSent('GET', "$base/themes.json");
        shopifyRest3ThemeSent('GET', "$base/themes/1.json");
        shopifyRest3ThemeSent('POST', "$base/themes.json", ['theme' => ['name' => 'Novo', 'src' => 'https://x/y.zip']]);
        shopifyRest3ThemeSent('PUT', "$base/themes/1.json", ['theme' => ['role' => 'main']]);
        shopifyRest3ThemeSent('DELETE', "$base/themes/1.json");
    });

    it('redirects: list / count / get / create / update / delete', function () use ($base) {
        Http::fake(['*' => Http::response(['redirects' => [], 'redirect' => ['id' => 2], 'count' => 5])]);
        $m = shopifyRest3ThemeMethods();
        $m->listRedirects(['path' => '/a']);
        expect($m->countRedirects())->toBe(5);
        $m->getRedirect(2);
        $m->createRedirect(['path' => '/a', 'target' => '/b']);
        $m->updateRedirect(2, ['target' => '/c']);
        $m->deleteRedirect(2);
        shopifyRest3ThemeSent('GET', "$base/redirects.json?path=%2Fa");
        shopifyRest3ThemeSent('GET', "$base/redirects/count.json");
        shopifyRest3ThemeSent('GET', "$base/redirects/2.json");
        shopifyRest3ThemeSent('POST', "$base/redirects.json", ['redirect' => ['path' => '/a', 'target' => '/b']]);
        shopifyRest3ThemeSent('PUT', "$base/redirects/2.json", ['redirect' => ['target' => '/c']]);
        shopifyRest3ThemeSent('DELETE', "$base/redirects/2.json");
    });

    it('script tags: list / count / get / create / update / delete', function () use ($base) {
        Http::fake(['*' => Http::response(['script_tags' => [], 'script_tag' => ['id' => 3], 'count' => 1])]);
        $m = shopifyRest3ThemeMethods();
        $m->listScriptTags();
        expect($m->countScriptTags())->toBe(1);
        $m->getScriptTag(3);
        $m->createScriptTag(['event' => 'onload', 'src' => 'https://x/app.js']);
        $m->updateScriptTag(3, ['src' => 'https://x/app2.js']);
        $m->deleteScriptTag(3);
        shopifyRest3ThemeSent('GET', "$base/script_tags.json");
        shopifyRest3ThemeSent('GET', "$base/script_tags/count.json");
        shopifyRest3ThemeSent('GET', "$base/script_tags/3.json");
        shopifyRest3ThemeSent('POST', "$base/script_tags.json", ['script_tag' => ['event' => 'onload', 'src' => 'https://x/app.js']]);
        shopifyRest3ThemeSent('PUT', "$base/script_tags/3.json", ['script_tag' => ['src' => 'https://x/app2.js']]);
        shopifyRest3ThemeSent('DELETE', "$base/script_tags/3.json");
    });

    it('eachRedirect / eachScriptTag seguem o Link header', function () use ($base) {
        Http::fake(['*' => Http::sequence()
            ->push(['redirects' => [['id' => 1]]], 200, ['Link' => "<$base/redirects.json?limit=250&page_info=R2>; rel=\"next\""])
            ->push(['redirects' => [['id' => 2]]], 200, [])
            ->push(['script_tags' => [['id' => 3]]], 200, [])]);
        $m = shopifyRest3ThemeMethods();
        expect(array_column(iterator_to_array($m->eachRedirect()), 'id'))->toBe([1, 2])
            ->and(array_column(iterator_to_array($m->eachScriptTag()), 'id'))->toBe([3]);
    });
});

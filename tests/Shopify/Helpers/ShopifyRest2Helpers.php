<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

if (! function_exists('shopifyRest2Integration')) {
    function shopifyRest2Integration(): FakeIntegration
    {
        return new FakeIntegration(
            accessToken: 'shpat_x',
            refreshToken: null,
            settings: ['shop_domain' => 'loja-teste.myshopify.com'],
            active: true,
            expired: false,
        );
    }

    function shopifyRest2Url(string $pathWithQuery): string
    {
        return 'https://loja-teste.myshopify.com/admin/api/2024-04/'.ltrim($pathWithQuery, '/');
    }

    /**
     * @template T of BaseMethods
     *
     * @param  class-string<T>  $class
     * @return T
     */
    function shopifyRest2Make(string $class): BaseMethods
    {
        config(['marketplaces.shopify.api_version' => '2024-04']);
        $integration = shopifyRest2Integration();

        return new $class(HttpClientFactory::make($integration), $integration);
    }

    /**
     * Fake + chamada + assert de verbo, URL completa (com query), header do
     * token e body JSON (null = nao confere body).
     */
    function shopifyRest2Call(callable $call, string $method, string $pathWithQuery, ?array $body = null, array $response = ['ok' => true]): void
    {
        Http::fake(['*' => Http::response($response, 200)]);

        expect($call())->toBe($response);

        $expectedUrl = urldecode(shopifyRest2Url($pathWithQuery));

        Http::assertSent(function (Request $request) use ($method, $expectedUrl, $body) {
            $sent = urldecode($request->url());
            expect($request->method())->toBe($method)
                ->and($sent)->toBe($expectedUrl)
                ->and($request->hasHeader('X-Shopify-Access-Token', 'shpat_x'))->toBeTrue();
            if ($body !== null) {
                expect($request->data())->toBe($body);
            }

            return true;
        });
    }
}

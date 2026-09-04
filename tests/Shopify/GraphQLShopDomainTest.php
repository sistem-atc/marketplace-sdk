<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\ShopQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\ShopMutations;

// ARQUIVO GERADO junto com as classes de dominio; documentos esperados vem do schema 2026-07.

if (! function_exists('shopifyGqlDomainClient')) {
    function shopifyGqlDomainClient(): GraphQLClient
    {
        $integration = new FakeIntegration(
            accessToken: 'shpat_x',
            refreshToken: null,
            settings: ['shop_domain' => 'loja-teste.myshopify.com'],
            active: true,
            expired: false,
        );

        return GraphQLClient::forIntegration($integration);
    }
}

beforeEach(function () {
    config(['marketplaces.shopify.graphql_api_version' => '2026-07']);
    Http::preventStrayRequests();
});

describe('Shopify GraphQL — dominio Shop', function () {
    it('Queries::shopPayPaymentRequestReceipts monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['shopPayPaymentRequestReceipts' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new ShopQueries(shopifyGqlDomainClient()))->shopPayPaymentRequestReceipts($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query shopPayPaymentRequestReceipts($first: Int, $after: String) { shopPayPaymentRequestReceipts(first: $first, after: $after) { edges { node { createdAt sourceIdentifier token } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::shopLocales monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['shopLocales' => ['ok' => true]]], 200)]);
        $args = json_decode('{"published": true}', true);

        $result = (new ShopQueries(shopifyGqlDomainClient()))->shopLocales($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query shopLocales($published: Boolean) { shopLocales(published: $published) { locale name primary published } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::shopLocaleDisable monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['shopLocaleDisable' => ['ok' => true]]], 200)]);
        $args = json_decode('{"locale": "abc"}', true);

        $result = (new ShopMutations(shopifyGqlDomainClient()))->shopLocaleDisable($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation shopLocaleDisable($locale: String!) { shopLocaleDisable(locale: $locale) { locale userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::shopLocaleEnable monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['shopLocaleEnable' => ['ok' => true]]], 200)]);
        $args = json_decode('{"locale": "abc", "marketWebPresenceIds": ["gid://shopify/Thing/1"]}', true);

        $result = (new ShopMutations(shopifyGqlDomainClient()))->shopLocaleEnable($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation shopLocaleEnable($locale: String!, $marketWebPresenceIds: [ID!]) { shopLocaleEnable(locale: $locale, marketWebPresenceIds: $marketWebPresenceIds) { shopLocale { locale name primary published } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\PointOfSaleQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\PointOfSaleMutations;

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

describe('Shopify GraphQL — dominio PointOfSale', function () {
    it('Queries::cashDrawers monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['cashDrawers' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new PointOfSaleQueries(shopifyGqlDomainClient()))->cashDrawers($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query cashDrawers($first: Int, $after: String) { cashDrawers(first: $first, after: $after) { edges { node { id name } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::cashDrawer monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['cashDrawer' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new PointOfSaleQueries(shopifyGqlDomainClient()))->cashDrawer($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query cashDrawer($id: ID!) { cashDrawer(id: $id) { id name } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::cashDrawerCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['cashDrawerCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"locationId": "gid://shopify/Thing/1", "name": "abc"}', true);

        $result = (new PointOfSaleMutations(shopifyGqlDomainClient()))->cashDrawerCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation cashDrawerCreate($locationId: ID!, $name: String!) { cashDrawerCreate(locationId: $locationId, name: $name) { cashDrawer { id name } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::cashDrawerFindOrCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['cashDrawerFindOrCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"locationId": "gid://shopify/Thing/1", "name": "abc"}', true);

        $result = (new PointOfSaleMutations(shopifyGqlDomainClient()))->cashDrawerFindOrCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation cashDrawerFindOrCreate($locationId: ID!, $name: String!) { cashDrawerFindOrCreate(locationId: $locationId, name: $name) { cashDrawer { id name } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

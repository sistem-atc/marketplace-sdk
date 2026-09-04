<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\UrlRedirectQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\UrlRedirectMutations;

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

describe('Shopify GraphQL — dominio UrlRedirect', function () {
    it('Queries::urlRedirectSavedSearches monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['urlRedirectSavedSearches' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new UrlRedirectQueries(shopifyGqlDomainClient()))->urlRedirectSavedSearches($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query urlRedirectSavedSearches($first: Int, $after: String) { urlRedirectSavedSearches(first: $first, after: $after) { edges { node { id legacyResourceId name query resourceType searchTerms } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::urlRedirect monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['urlRedirect' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new UrlRedirectQueries(shopifyGqlDomainClient()))->urlRedirect($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query urlRedirect($id: ID!) { urlRedirect(id: $id) { id path target } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::urlRedirectBulkDeleteByIds monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['urlRedirectBulkDeleteByIds' => ['ok' => true]]], 200)]);
        $args = json_decode('{"ids": ["gid://shopify/Thing/1"]}', true);

        $result = (new UrlRedirectMutations(shopifyGqlDomainClient()))->urlRedirectBulkDeleteByIds($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation urlRedirectBulkDeleteByIds($ids: [ID!]!) { urlRedirectBulkDeleteByIds(ids: $ids) { job { done id } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::urlRedirectBulkDeleteAll monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['urlRedirectBulkDeleteAll' => ['ok' => true]]], 200)]);
        $args = json_decode('{}', true);

        $result = (new UrlRedirectMutations(shopifyGqlDomainClient()))->urlRedirectBulkDeleteAll($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation urlRedirectBulkDeleteAll { urlRedirectBulkDeleteAll { job { done id } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

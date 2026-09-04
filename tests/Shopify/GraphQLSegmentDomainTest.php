<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\SegmentQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\SegmentMutations;

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

describe('Shopify GraphQL — dominio Segment', function () {
    it('Queries::segmentFilterSuggestions monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['segmentFilterSuggestions' => ['ok' => true]]], 200)]);
        $args = json_decode('{"search": "abc", "first": 5}', true);

        $result = (new SegmentQueries(shopifyGqlDomainClient()))->segmentFilterSuggestions($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query segmentFilterSuggestions($search: String!, $first: Int!) { segmentFilterSuggestions(search: $search, first: $first) { edges { node { localizedName multiValue queryName } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::segment monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['segment' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new SegmentQueries(shopifyGqlDomainClient()))->segment($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query segment($id: ID!) { segment(id: $id) { creationDate id lastEditDate name query } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::segmentMigrations monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['segmentMigrations' => ['ok' => true]]], 200)]);
        $args = json_decode('{"savedSearchId": "gid://shopify/Thing/1", "first": 5}', true);

        $result = (new SegmentQueries(shopifyGqlDomainClient()))->segmentMigrations($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query segmentMigrations($savedSearchId: ID, $first: Int) { segmentMigrations(savedSearchId: $savedSearchId, first: $first) { edges { node { id savedSearchId segmentId } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::segmentCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['segmentCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"name": "abc", "query": "abc"}', true);

        $result = (new SegmentMutations(shopifyGqlDomainClient()))->segmentCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation segmentCreate($name: String!, $query: String!) { segmentCreate(name: $name, query: $query) { segment { creationDate id lastEditDate name query } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::segmentDelete monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['segmentDelete' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new SegmentMutations(shopifyGqlDomainClient()))->segmentDelete($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation segmentDelete($id: ID!) { segmentDelete(id: $id) { deletedSegmentId userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

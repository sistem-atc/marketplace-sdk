<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\BulkOperationQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\BulkOperationMutations;

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

describe('Shopify GraphQL — dominio BulkOperation', function () {
    it('Queries::bulkOperations monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['bulkOperations' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new BulkOperationQueries(shopifyGqlDomainClient()))->bulkOperations($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query bulkOperations($first: Int, $after: String) { bulkOperations(first: $first, after: $after) { edges { node { completedAt createdAt errorCode fileSize id objectCount partialDataUrl query rootObjectCount status type url } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::bulkOperation monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['bulkOperation' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new BulkOperationQueries(shopifyGqlDomainClient()))->bulkOperation($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query bulkOperation($id: ID!) { bulkOperation(id: $id) { completedAt createdAt errorCode fileSize id objectCount partialDataUrl query rootObjectCount status type url } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::currentBulkOperation monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['currentBulkOperation' => ['ok' => true]]], 200)]);
        $args = json_decode('{"type": "UNKNOWN"}', true);

        $result = (new BulkOperationQueries(shopifyGqlDomainClient()))->currentBulkOperation($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query currentBulkOperation($type: BulkOperationType) { currentBulkOperation(type: $type) { completedAt createdAt errorCode fileSize id objectCount partialDataUrl query rootObjectCount status type url } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::bulkOperationCancel monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['bulkOperationCancel' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new BulkOperationMutations(shopifyGqlDomainClient()))->bulkOperationCancel($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation bulkOperationCancel($id: ID!) { bulkOperationCancel(id: $id) { bulkOperation { completedAt createdAt errorCode fileSize id objectCount partialDataUrl query rootObjectCount status type url } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::bulkOperationRunMutation monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['bulkOperationRunMutation' => ['ok' => true]]], 200)]);
        $args = json_decode('{"mutation": "abc", "stagedUploadPath": "abc"}', true);

        $result = (new BulkOperationMutations(shopifyGqlDomainClient()))->bulkOperationRunMutation($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation bulkOperationRunMutation($mutation: String!, $stagedUploadPath: String!) { bulkOperationRunMutation(mutation: $mutation, stagedUploadPath: $stagedUploadPath) { bulkOperation { completedAt createdAt errorCode fileSize id objectCount partialDataUrl query rootObjectCount status type url } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

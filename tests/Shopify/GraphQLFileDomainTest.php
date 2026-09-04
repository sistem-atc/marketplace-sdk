<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\FileQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\FileMutations;

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

describe('Shopify GraphQL — dominio File', function () {
    it('Queries::fileSavedSearches monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['fileSavedSearches' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new FileQueries(shopifyGqlDomainClient()))->fileSavedSearches($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query fileSavedSearches($first: Int, $after: String) { fileSavedSearches(first: $first, after: $after) { edges { node { id legacyResourceId name query resourceType searchTerms } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::files monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['files' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new FileQueries(shopifyGqlDomainClient()))->files($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query files($first: Int, $after: String) { files(first: $first, after: $after) { edges { node { alt createdAt fileStatus id updatedAt } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::fileAcknowledgeUpdateFailed monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['fileAcknowledgeUpdateFailed' => ['ok' => true]]], 200)]);
        $args = json_decode('{"fileIds": ["gid://shopify/Thing/1"]}', true);

        $result = (new FileMutations(shopifyGqlDomainClient()))->fileAcknowledgeUpdateFailed($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation fileAcknowledgeUpdateFailed($fileIds: [ID!]!) { fileAcknowledgeUpdateFailed(fileIds: $fileIds) { files { alt createdAt fileStatus id updatedAt } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::fileCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['fileCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"files": [{}]}', true);

        $result = (new FileMutations(shopifyGqlDomainClient()))->fileCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation fileCreate($files: [FileCreateInput!]!) { fileCreate(files: $files) { files { alt createdAt fileStatus id updatedAt } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::stagedUploadTargetGenerate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['stagedUploadTargetGenerate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new FileMutations(shopifyGqlDomainClient()))->stagedUploadTargetGenerate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation stagedUploadTargetGenerate($input: StagedUploadTargetGenerateInput!) { stagedUploadTargetGenerate(input: $input) { parameters { name value } url userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

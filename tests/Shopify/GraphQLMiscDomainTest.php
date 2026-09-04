<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\MiscQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\MiscMutations;

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

describe('Shopify GraphQL — dominio Misc', function () {
    it('Queries::channels monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['channels' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new MiscQueries(shopifyGqlDomainClient()))->channels($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query channels($first: Int, $after: String) { channels(first: $first, after: $after) { edges { node { accountId accountName activeRegions handle hasCollection id name overviewPath specificationHandle supportsFuturePublishing } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::businessEntity monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['businessEntity' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new MiscQueries(shopifyGqlDomainClient()))->businessEntity($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query businessEntity($id: ID) { businessEntity(id: $id) { archived companyName displayName id primary } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::deletionEvents monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['deletionEvents' => ['ok' => true]]], 200)]);
        $args = json_decode('{"subjectTypes": ["UNKNOWN"], "first": 5}', true);

        $result = (new MiscQueries(shopifyGqlDomainClient()))->deletionEvents($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query deletionEvents($subjectTypes: [DeletionEventSubjectType!], $first: Int) { deletionEvents(subjectTypes: $subjectTypes, first: $first) { edges { node { occurredAt subjectId subjectType } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::bulkProductResourceFeedbackCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['bulkProductResourceFeedbackCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"feedbackInput": [{}]}', true);

        $result = (new MiscMutations(shopifyGqlDomainClient()))->bulkProductResourceFeedbackCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation bulkProductResourceFeedbackCreate($feedbackInput: [ProductResourceFeedbackInput!]!) { bulkProductResourceFeedbackCreate(feedbackInput: $feedbackInput) { feedback { feedbackGeneratedAt messages productId productUpdatedAt state } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::serverPixelCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['serverPixelCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{}', true);

        $result = (new MiscMutations(shopifyGqlDomainClient()))->serverPixelCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation serverPixelCreate { serverPixelCreate { serverPixel { id status webhookEndpointAddress } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

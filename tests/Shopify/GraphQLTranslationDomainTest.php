<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\TranslationQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\TranslationMutations;

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

describe('Shopify GraphQL — dominio Translation', function () {
    it('Queries::translatableResources monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['translatableResources' => ['ok' => true]]], 200)]);
        $args = json_decode('{"resourceType": "UNKNOWN", "first": 5}', true);

        $result = (new TranslationQueries(shopifyGqlDomainClient()))->translatableResources($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query translatableResources($resourceType: TranslatableResourceType!, $first: Int) { translatableResources(resourceType: $resourceType, first: $first) { edges { node { resourceId } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::translatableResource monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['translatableResource' => ['ok' => true]]], 200)]);
        $args = json_decode('{"resourceId": "gid://shopify/Thing/1"}', true);

        $result = (new TranslationQueries(shopifyGqlDomainClient()))->translatableResource($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query translatableResource($resourceId: ID!) { translatableResource(resourceId: $resourceId) { resourceId } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::translationsRegister monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['translationsRegister' => ['ok' => true]]], 200)]);
        $args = json_decode('{"resourceId": "gid://shopify/Thing/1", "translations": [{}]}', true);

        $result = (new TranslationMutations(shopifyGqlDomainClient()))->translationsRegister($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation translationsRegister($resourceId: ID!, $translations: [TranslationInput!]!) { translationsRegister(resourceId: $resourceId, translations: $translations) { translations { key locale outdated updatedAt value } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::translationsRemove monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['translationsRemove' => ['ok' => true]]], 200)]);
        $args = json_decode('{"resourceId": "gid://shopify/Thing/1", "translationKeys": ["abc"]}', true);

        $result = (new TranslationMutations(shopifyGqlDomainClient()))->translationsRemove($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation translationsRemove($resourceId: ID!, $translationKeys: [String!]!) { translationsRemove(resourceId: $resourceId, translationKeys: $translationKeys) { translations { key locale outdated updatedAt value } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

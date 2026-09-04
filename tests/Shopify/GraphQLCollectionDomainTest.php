<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\CollectionQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\CollectionMutations;

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

describe('Shopify GraphQL — dominio Collection', function () {
    it('Queries::collectionConditionsSources monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['collectionConditionsSources' => ['ok' => true]]], 200)]);
        $args = json_decode('{"appId": "gid://shopify/Thing/1", "first": 5}', true);

        $result = (new CollectionQueries(shopifyGqlDomainClient()))->collectionConditionsSources($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query collectionConditionsSources($appId: ID!, $first: Int) { collectionConditionsSources(appId: $appId, first: $first) { edges { node { description id shareable targetType title } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::collection monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['collection' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new CollectionQueries(shopifyGqlDomainClient()))->collection($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query collection($id: ID!) { collection(id: $id) { description descriptionHtml handle hasProduct id legacyResourceId publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication sortOrder storefrontId templateSuffix title updatedAt } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::collectionByHandle monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['collectionByHandle' => ['ok' => true]]], 200)]);
        $args = json_decode('{"handle": "abc"}', true);

        $result = (new CollectionQueries(shopifyGqlDomainClient()))->collectionByHandle($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query collectionByHandle($handle: String!) { collectionByHandle(handle: $handle) { description descriptionHtml handle hasProduct id legacyResourceId publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication sortOrder storefrontId templateSuffix title updatedAt } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::collectionAddProducts monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['collectionAddProducts' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1", "productIds": ["gid://shopify/Thing/1"]}', true);

        $result = (new CollectionMutations(shopifyGqlDomainClient()))->collectionAddProducts($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation collectionAddProducts($id: ID!, $productIds: [ID!]!) { collectionAddProducts(id: $id, productIds: $productIds) { collection { description descriptionHtml handle hasProduct id legacyResourceId publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication sortOrder storefrontId templateSuffix title updatedAt } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::collectionAddProductsV2 monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['collectionAddProductsV2' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1", "productIds": ["gid://shopify/Thing/1"]}', true);

        $result = (new CollectionMutations(shopifyGqlDomainClient()))->collectionAddProductsV2($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation collectionAddProductsV2($id: ID!, $productIds: [ID!]!) { collectionAddProductsV2(id: $id, productIds: $productIds) { job { done id } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::collectionPublish monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['collectionPublish' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new CollectionMutations(shopifyGqlDomainClient()))->collectionPublish($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation collectionPublish($input: CollectionPublishInput!) { collectionPublish(input: $input) { collection { description descriptionHtml handle hasProduct id legacyResourceId publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication sortOrder storefrontId templateSuffix title updatedAt } collectionPublications { isPublished publishDate } shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

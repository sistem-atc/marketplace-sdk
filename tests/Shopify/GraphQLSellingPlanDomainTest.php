<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\SellingPlanQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\SellingPlanMutations;

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

describe('Shopify GraphQL — dominio SellingPlan', function () {
    it('Queries::sellingPlanGroups monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['sellingPlanGroups' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new SellingPlanQueries(shopifyGqlDomainClient()))->sellingPlanGroups($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query sellingPlanGroups($first: Int, $after: String) { sellingPlanGroups(first: $first, after: $after) { edges { node { appId appliesToProduct appliesToProductVariant appliesToProductVariants createdAt description id merchantCode name options position summary } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::sellingPlanGroup monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['sellingPlanGroup' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new SellingPlanQueries(shopifyGqlDomainClient()))->sellingPlanGroup($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query sellingPlanGroup($id: ID!) { sellingPlanGroup(id: $id) { appId appliesToProduct appliesToProductVariant appliesToProductVariants createdAt description id merchantCode name options position summary } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::sellingPlanGroupAddProductVariants monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['sellingPlanGroupAddProductVariants' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1", "productVariantIds": ["gid://shopify/Thing/1"]}', true);

        $result = (new SellingPlanMutations(shopifyGqlDomainClient()))->sellingPlanGroupAddProductVariants($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation sellingPlanGroupAddProductVariants($id: ID!, $productVariantIds: [ID!]!) { sellingPlanGroupAddProductVariants(id: $id, productVariantIds: $productVariantIds) { sellingPlanGroup { appId appliesToProduct appliesToProductVariant appliesToProductVariants createdAt description id merchantCode name options position summary } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::sellingPlanGroupAddProducts monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['sellingPlanGroupAddProducts' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1", "productIds": ["gid://shopify/Thing/1"]}', true);

        $result = (new SellingPlanMutations(shopifyGqlDomainClient()))->sellingPlanGroupAddProducts($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation sellingPlanGroupAddProducts($id: ID!, $productIds: [ID!]!) { sellingPlanGroupAddProducts(id: $id, productIds: $productIds) { sellingPlanGroup { appId appliesToProduct appliesToProductVariant appliesToProductVariants createdAt description id merchantCode name options position summary } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

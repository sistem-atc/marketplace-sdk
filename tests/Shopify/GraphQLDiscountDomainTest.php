<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\DiscountQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\DiscountMutations;

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

describe('Shopify GraphQL — dominio Discount', function () {
    it('Queries::automaticDiscountNodes monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['automaticDiscountNodes' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new DiscountQueries(shopifyGqlDomainClient()))->automaticDiscountNodes($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query automaticDiscountNodes($first: Int, $after: String) { automaticDiscountNodes(first: $first, after: $after) { edges { node { id } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::automaticDiscount monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['automaticDiscount' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new DiscountQueries(shopifyGqlDomainClient()))->automaticDiscount($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query automaticDiscount($id: ID!) { automaticDiscount(id: $id) { __typename } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::automaticDiscountNode monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['automaticDiscountNode' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new DiscountQueries(shopifyGqlDomainClient()))->automaticDiscountNode($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query automaticDiscountNode($id: ID!) { automaticDiscountNode(id: $id) { id } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::discountAutomaticActivate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['discountAutomaticActivate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new DiscountMutations(shopifyGqlDomainClient()))->discountAutomaticActivate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation discountAutomaticActivate($id: ID!) { discountAutomaticActivate(id: $id) { automaticDiscountNode { id } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::discountAutomaticAppCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['discountAutomaticAppCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"automaticAppDiscount": {}}', true);

        $result = (new DiscountMutations(shopifyGqlDomainClient()))->discountAutomaticAppCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation discountAutomaticAppCreate($automaticAppDiscount: DiscountAutomaticAppInput!) { discountAutomaticAppCreate(automaticAppDiscount: $automaticAppDiscount) { automaticAppDiscount { appliesOnOneTimePurchase appliesOnSubscription asyncUsageCount createdAt discountClass discountClasses discountId endsAt recurringCycleLimit startsAt status tags title updatedAt } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

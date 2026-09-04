<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\DeliveryQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\DeliveryMutations;

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

describe('Shopify GraphQL — dominio Delivery', function () {
    it('Queries::carrierServices monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['carrierServices' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new DeliveryQueries(shopifyGqlDomainClient()))->carrierServices($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query carrierServices($first: Int, $after: String) { carrierServices(first: $first, after: $after) { edges { node { active callbackUrl formattedName id name supportsServiceDiscovery } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::carrierService monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['carrierService' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new DeliveryQueries(shopifyGqlDomainClient()))->carrierService($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query carrierService($id: ID!) { carrierService(id: $id) { active callbackUrl formattedName id name supportsServiceDiscovery } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::carrierServiceCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['carrierServiceCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new DeliveryMutations(shopifyGqlDomainClient()))->carrierServiceCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation carrierServiceCreate($input: DeliveryCarrierServiceCreateInput!) { carrierServiceCreate(input: $input) { carrierService { active callbackUrl formattedName id name supportsServiceDiscovery } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::deliverySettingUpdate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['deliverySettingUpdate' => ['ok' => true]]], 200)]);
        $args = json_decode('{}', true);

        $result = (new DeliveryMutations(shopifyGqlDomainClient()))->deliverySettingUpdate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation deliverySettingUpdate { deliverySettingUpdate { userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::deliveryShippingOriginAssign monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['deliveryShippingOriginAssign' => ['ok' => true]]], 200)]);
        $args = json_decode('{"locationId": "gid://shopify/Thing/1"}', true);

        $result = (new DeliveryMutations(shopifyGqlDomainClient()))->deliveryShippingOriginAssign($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation deliveryShippingOriginAssign($locationId: ID!) { deliveryShippingOriginAssign(locationId: $locationId) { userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

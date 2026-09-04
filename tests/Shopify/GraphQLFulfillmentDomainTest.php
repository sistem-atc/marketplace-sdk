<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\FulfillmentQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\FulfillmentMutations;

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

describe('Shopify GraphQL — dominio Fulfillment', function () {
    it('Queries::assignedFulfillmentOrders monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['assignedFulfillmentOrders' => ['ok' => true]]], 200)]);
        $args = json_decode('{"assignmentStatus": "UNKNOWN", "locationIds": ["gid://shopify/Thing/1"]}', true);

        $result = (new FulfillmentQueries(shopifyGqlDomainClient()))->assignedFulfillmentOrders($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query assignedFulfillmentOrders($assignmentStatus: FulfillmentOrderAssignmentStatus, $locationIds: [ID!]) { assignedFulfillmentOrders(assignmentStatus: $assignmentStatus, locationIds: $locationIds) { edges { node { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::fulfillment monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['fulfillment' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new FulfillmentQueries(shopifyGqlDomainClient()))->fulfillment($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query fulfillment($id: ID!) { fulfillment(id: $id) { createdAt deliveredAt displayStatus estimatedDeliveryAt id inTransitAt legacyResourceId name requiresShipping status totalQuantity updatedAt } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::fulfillmentCancel monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['fulfillmentCancel' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new FulfillmentMutations(shopifyGqlDomainClient()))->fulfillmentCancel($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation fulfillmentCancel($id: ID!) { fulfillmentCancel(id: $id) { fulfillment { createdAt deliveredAt displayStatus estimatedDeliveryAt id inTransitAt legacyResourceId name requiresShipping status totalQuantity updatedAt } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::fulfillmentConstraintRuleCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['fulfillmentConstraintRuleCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"functionHandle": "abc", "deliveryMethodTypes": ["UNKNOWN"]}', true);

        $result = (new FulfillmentMutations(shopifyGqlDomainClient()))->fulfillmentConstraintRuleCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation fulfillmentConstraintRuleCreate($functionHandle: String, $deliveryMethodTypes: [DeliveryMethodType!]!) { fulfillmentConstraintRuleCreate(functionHandle: $functionHandle, deliveryMethodTypes: $deliveryMethodTypes) { fulfillmentConstraintRule { deliveryMethodTypes id } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::fulfillmentCreateV2 monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['fulfillmentCreateV2' => ['ok' => true]]], 200)]);
        $args = json_decode('{"fulfillment": {}, "message": "abc"}', true);

        $result = (new FulfillmentMutations(shopifyGqlDomainClient()))->fulfillmentCreateV2($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation fulfillmentCreateV2($fulfillment: FulfillmentV2Input!, $message: String) { fulfillmentCreateV2(fulfillment: $fulfillment, message: $message) { fulfillment { createdAt deliveredAt displayStatus estimatedDeliveryAt id inTransitAt legacyResourceId name requiresShipping status totalQuantity updatedAt } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

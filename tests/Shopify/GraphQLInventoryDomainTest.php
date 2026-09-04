<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\InventoryQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\InventoryMutations;

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

describe('Shopify GraphQL — dominio Inventory', function () {
    it('Queries::inventoryItems monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['inventoryItems' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new InventoryQueries(shopifyGqlDomainClient()))->inventoryItems($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query inventoryItems($first: Int, $after: String) { inventoryItems(first: $first, after: $after) { edges { node { countryCodeOfOrigin createdAt duplicateSkuCount harmonizedSystemCode id inventoryHistoryUrl legacyResourceId provinceCodeOfOrigin requiresShipping sku tracked updatedAt } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::inventoryItem monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['inventoryItem' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new InventoryQueries(shopifyGqlDomainClient()))->inventoryItem($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query inventoryItem($id: ID!) { inventoryItem(id: $id) { countryCodeOfOrigin createdAt duplicateSkuCount harmonizedSystemCode id inventoryHistoryUrl legacyResourceId provinceCodeOfOrigin requiresShipping sku tracked updatedAt } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::inventoryActivate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['inventoryActivate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"inventoryItemId": "gid://shopify/Thing/1", "locationId": "gid://shopify/Thing/1"}', true);

        $result = (new InventoryMutations(shopifyGqlDomainClient()))->inventoryActivate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation inventoryActivate($inventoryItemId: ID!, $locationId: ID!) { inventoryActivate(inventoryItemId: $inventoryItemId, locationId: $locationId) { inventoryLevel { canDeactivate createdAt deactivationAlert id isActive updatedAt } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::inventoryAdjustQuantities monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['inventoryAdjustQuantities' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new InventoryMutations(shopifyGqlDomainClient()))->inventoryAdjustQuantities($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation inventoryAdjustQuantities($input: InventoryAdjustQuantitiesInput!) { inventoryAdjustQuantities(input: $input) { inventoryAdjustmentGroup { createdAt id reason referenceDocumentUri } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::inventorySetOnHandQuantities monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['inventorySetOnHandQuantities' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new InventoryMutations(shopifyGqlDomainClient()))->inventorySetOnHandQuantities($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation inventorySetOnHandQuantities($input: InventorySetOnHandQuantitiesInput!) { inventorySetOnHandQuantities(input: $input) { inventoryAdjustmentGroup { createdAt id reason referenceDocumentUri } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

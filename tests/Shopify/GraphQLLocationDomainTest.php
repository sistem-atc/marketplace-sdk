<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\LocationQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\LocationMutations;

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

describe('Shopify GraphQL — dominio Location', function () {
    it('Queries::locations monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['locations' => ['ok' => true]]], 200)]);
        $args = json_decode('{"includeLegacy": true, "includeInactive": true}', true);

        $result = (new LocationQueries(shopifyGqlDomainClient()))->locations($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query locations($includeLegacy: Boolean, $includeInactive: Boolean) { locations(includeLegacy: $includeLegacy, includeInactive: $includeInactive) { edges { node { activatable addressVerified createdAt deactivatable deactivatedAt deletable fulfillsOnlineOrders hasActiveInventory hasUnfulfilledOrders id isActive isFulfillmentService isPrimary legacyResourceId name shipsInventory updatedAt } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::location monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['location' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new LocationQueries(shopifyGqlDomainClient()))->location($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query location($id: ID) { location(id: $id) { activatable addressVerified createdAt deactivatable deactivatedAt deletable fulfillsOnlineOrders hasActiveInventory hasUnfulfilledOrders id isActive isFulfillmentService isPrimary legacyResourceId name shipsInventory updatedAt } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::locationsAvailableForDeliveryProfiles monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['locationsAvailableForDeliveryProfiles' => ['ok' => true]]], 200)]);
        $args = json_decode('{}', true);

        $result = (new LocationQueries(shopifyGqlDomainClient()))->locationsAvailableForDeliveryProfiles($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query locationsAvailableForDeliveryProfiles { locationsAvailableForDeliveryProfiles { activatable addressVerified createdAt deactivatable deactivatedAt deletable fulfillsOnlineOrders hasActiveInventory hasUnfulfilledOrders id isActive isFulfillmentService isPrimary legacyResourceId name shipsInventory updatedAt } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::locationActivate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['locationActivate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"locationId": "gid://shopify/Thing/1"}', true);

        $result = (new LocationMutations(shopifyGqlDomainClient()))->locationActivate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation locationActivate($locationId: ID!) { locationActivate(locationId: $locationId) { location { activatable addressVerified createdAt deactivatable deactivatedAt deletable fulfillsOnlineOrders hasActiveInventory hasUnfulfilledOrders id isActive isFulfillmentService isPrimary legacyResourceId name shipsInventory updatedAt } locationActivateUserErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::locationAdd monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['locationAdd' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new LocationMutations(shopifyGqlDomainClient()))->locationAdd($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation locationAdd($input: LocationAddInput!) { locationAdd(input: $input) { location { activatable addressVerified createdAt deactivatable deactivatedAt deletable fulfillsOnlineOrders hasActiveInventory hasUnfulfilledOrders id isActive isFulfillmentService isPrimary legacyResourceId name shipsInventory updatedAt } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

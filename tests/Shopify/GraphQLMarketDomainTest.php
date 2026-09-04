<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\MarketQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\MarketMutations;

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

describe('Shopify GraphQL — dominio Market', function () {
    it('Queries::marketLocalizableResources monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['marketLocalizableResources' => ['ok' => true]]], 200)]);
        $args = json_decode('{"resourceType": "UNKNOWN", "first": 5}', true);

        $result = (new MarketQueries(shopifyGqlDomainClient()))->marketLocalizableResources($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query marketLocalizableResources($resourceType: MarketLocalizableResourceType!, $first: Int) { marketLocalizableResources(resourceType: $resourceType, first: $first) { edges { node { resourceId } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::market monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['market' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new MarketQueries(shopifyGqlDomainClient()))->market($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query market($id: ID!) { market(id: $id) { assignedCustomization enabled handle id name primary status type } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::marketByGeography monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['marketByGeography' => ['ok' => true]]], 200)]);
        $args = json_decode('{"countryCode": "UNKNOWN"}', true);

        $result = (new MarketQueries(shopifyGqlDomainClient()))->marketByGeography($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query marketByGeography($countryCode: CountryCode!) { marketByGeography(countryCode: $countryCode) { assignedCustomization enabled handle id name primary status type } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::backupRegionUpdate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['backupRegionUpdate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"region": {}}', true);

        $result = (new MarketMutations(shopifyGqlDomainClient()))->backupRegionUpdate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation backupRegionUpdate($region: BackupRegionUpdateInput) { backupRegionUpdate(region: $region) { backupRegion { id name } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::marketCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['marketCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new MarketMutations(shopifyGqlDomainClient()))->marketCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation marketCreate($input: MarketCreateInput!) { marketCreate(input: $input) { market { assignedCustomization enabled handle id name primary status type } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::marketCurrencySettingsUpdate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['marketCurrencySettingsUpdate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"marketId": "gid://shopify/Thing/1", "input": {}}', true);

        $result = (new MarketMutations(shopifyGqlDomainClient()))->marketCurrencySettingsUpdate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation marketCurrencySettingsUpdate($marketId: ID!, $input: MarketCurrencySettingsUpdateInput!) { marketCurrencySettingsUpdate(marketId: $marketId, input: $input) { market { assignedCustomization enabled handle id name primary status type } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\MarketingQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\MarketingMutations;

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

describe('Shopify GraphQL — dominio Marketing', function () {
    it('Queries::marketingActivities monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['marketingActivities' => ['ok' => true]]], 200)]);
        $args = json_decode('{"marketingActivityIds": ["gid://shopify/Thing/1"], "remoteIds": ["abc"]}', true);

        $result = (new MarketingQueries(shopifyGqlDomainClient()))->marketingActivities($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query marketingActivities($marketingActivityIds: [ID!], $remoteIds: [String!]) { marketingActivities(marketingActivityIds: $marketingActivityIds, remoteIds: $remoteIds) { edges { node { activityListUrl createdAt formData hierarchyLevel id inMainWorkflowVersion isExternal marketingChannel marketingChannelType parentActivityId parentRemoteId sourceAndMedium status statusBadgeType statusBadgeTypeV2 statusLabel statusTransitionedAt tactic targetStatus title updatedAt urlParameterValue } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::marketingActivity monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['marketingActivity' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new MarketingQueries(shopifyGqlDomainClient()))->marketingActivity($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query marketingActivity($id: ID!) { marketingActivity(id: $id) { activityListUrl createdAt formData hierarchyLevel id inMainWorkflowVersion isExternal marketingChannel marketingChannelType parentActivityId parentRemoteId sourceAndMedium status statusBadgeType statusBadgeTypeV2 statusLabel statusTransitionedAt tactic targetStatus title updatedAt urlParameterValue } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::marketingActivityCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['marketingActivityCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new MarketingMutations(shopifyGqlDomainClient()))->marketingActivityCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation marketingActivityCreate($input: MarketingActivityCreateInput!) { marketingActivityCreate(input: $input) { marketingActivity { activityListUrl createdAt formData hierarchyLevel id inMainWorkflowVersion isExternal marketingChannel marketingChannelType parentActivityId parentRemoteId sourceAndMedium status statusBadgeType statusBadgeTypeV2 statusLabel statusTransitionedAt tactic targetStatus title updatedAt urlParameterValue } redirectPath userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::marketingActivitiesDeleteAllExternal monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['marketingActivitiesDeleteAllExternal' => ['ok' => true]]], 200)]);
        $args = json_decode('{}', true);

        $result = (new MarketingMutations(shopifyGqlDomainClient()))->marketingActivitiesDeleteAllExternal($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation marketingActivitiesDeleteAllExternal { marketingActivitiesDeleteAllExternal { job { done id } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::marketingActivityCreateExternal monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['marketingActivityCreateExternal' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new MarketingMutations(shopifyGqlDomainClient()))->marketingActivityCreateExternal($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation marketingActivityCreateExternal($input: MarketingActivityCreateExternalInput!) { marketingActivityCreateExternal(input: $input) { marketingActivity { activityListUrl createdAt formData hierarchyLevel id inMainWorkflowVersion isExternal marketingChannel marketingChannelType parentActivityId parentRemoteId sourceAndMedium status statusBadgeType statusBadgeTypeV2 statusLabel statusTransitionedAt tactic targetStatus title updatedAt urlParameterValue } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

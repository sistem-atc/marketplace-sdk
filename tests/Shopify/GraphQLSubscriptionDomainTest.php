<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\SubscriptionQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\SubscriptionMutations;

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

describe('Shopify GraphQL — dominio Subscription', function () {
    it('Queries::subscriptionBillingAttempts monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['subscriptionBillingAttempts' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new SubscriptionQueries(shopifyGqlDomainClient()))->subscriptionBillingAttempts($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query subscriptionBillingAttempts($first: Int, $after: String) { subscriptionBillingAttempts(first: $first, after: $after) { edges { node { completedAt createdAt errorCode errorMessage id idempotencyKey nextActionUrl originTime paymentGroupId paymentSessionId ready respectInventoryPolicy } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::subscriptionBillingAttempt monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['subscriptionBillingAttempt' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new SubscriptionQueries(shopifyGqlDomainClient()))->subscriptionBillingAttempt($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query subscriptionBillingAttempt($id: ID!) { subscriptionBillingAttempt(id: $id) { completedAt createdAt errorCode errorMessage id idempotencyKey nextActionUrl originTime paymentGroupId paymentSessionId ready respectInventoryPolicy } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::subscriptionDraft monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['subscriptionDraft' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new SubscriptionQueries(shopifyGqlDomainClient()))->subscriptionDraft($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query subscriptionDraft($id: ID!) { subscriptionDraft(id: $id) { currencyCode id nextBillingDate note status } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::subscriptionBillingAttemptCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['subscriptionBillingAttemptCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"subscriptionContractId": "gid://shopify/Thing/1", "subscriptionBillingAttemptInput": {}}', true);

        $result = (new SubscriptionMutations(shopifyGqlDomainClient()))->subscriptionBillingAttemptCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation subscriptionBillingAttemptCreate($subscriptionContractId: ID!, $subscriptionBillingAttemptInput: SubscriptionBillingAttemptInput!) { subscriptionBillingAttemptCreate(subscriptionContractId: $subscriptionContractId, subscriptionBillingAttemptInput: $subscriptionBillingAttemptInput) { subscriptionBillingAttempt { completedAt createdAt errorCode errorMessage id idempotencyKey nextActionUrl originTime paymentGroupId paymentSessionId ready respectInventoryPolicy } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::subscriptionBillingCycleBulkCharge monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['subscriptionBillingCycleBulkCharge' => ['ok' => true]]], 200)]);
        $args = json_decode('{"billingAttemptExpectedDateRange": {}, "filters": {}}', true);

        $result = (new SubscriptionMutations(shopifyGqlDomainClient()))->subscriptionBillingCycleBulkCharge($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation subscriptionBillingCycleBulkCharge($billingAttemptExpectedDateRange: SubscriptionBillingCyclesDateRangeSelector!, $filters: SubscriptionBillingCycleBulkFilters) { subscriptionBillingCycleBulkCharge(billingAttemptExpectedDateRange: $billingAttemptExpectedDateRange, filters: $filters) { job { done id } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::subscriptionContractCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['subscriptionContractCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new SubscriptionMutations(shopifyGqlDomainClient()))->subscriptionContractCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation subscriptionContractCreate($input: SubscriptionContractCreateInput!) { subscriptionContractCreate(input: $input) { draft { currencyCode id nextBillingDate note status } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

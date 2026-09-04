<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\CheckoutQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\CheckoutMutations;

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

describe('Shopify GraphQL — dominio Checkout', function () {
    it('Queries::abandonedCheckouts monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['abandonedCheckouts' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new CheckoutQueries(shopifyGqlDomainClient()))->abandonedCheckouts($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query abandonedCheckouts($first: Int, $after: String) { abandonedCheckouts(first: $first, after: $after) { edges { node { abandonedCheckoutUrl completedAt createdAt defaultCursor discountCodes id lineItemsQuantity name note taxesIncluded updatedAt } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::abandonedCheckoutsCount monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['abandonedCheckoutsCount' => ['ok' => true]]], 200)]);
        $args = json_decode('{"query": "abc", "savedSearchId": "gid://shopify/Thing/1"}', true);

        $result = (new CheckoutQueries(shopifyGqlDomainClient()))->abandonedCheckoutsCount($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query abandonedCheckoutsCount($query: String, $savedSearchId: ID) { abandonedCheckoutsCount(query: $query, savedSearchId: $savedSearchId) { count precision } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::checkoutBranding monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['checkoutBranding' => ['ok' => true]]], 200)]);
        $args = json_decode('{"checkoutProfileId": "gid://shopify/Thing/1"}', true);

        $result = (new CheckoutQueries(shopifyGqlDomainClient()))->checkoutBranding($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query checkoutBranding($checkoutProfileId: ID!) { checkoutBranding(checkoutProfileId: $checkoutProfileId) { __typename } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::abandonmentEmailStateUpdate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['abandonmentEmailStateUpdate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1", "emailState": "UNKNOWN"}', true);

        $result = (new CheckoutMutations(shopifyGqlDomainClient()))->abandonmentEmailStateUpdate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation abandonmentEmailStateUpdate($id: ID!, $emailState: AbandonmentEmailState!) { abandonmentEmailStateUpdate(id: $id, emailState: $emailState) { abandonment { abandonmentType cartUrl createdAt customerHasNoDraftOrderSinceAbandonment customerHasNoOrderSinceAbandonment daysSinceLastAbandonmentEmail emailSentAt emailState hoursSinceLastAbandonedCheckout id inventoryAvailable isFromCustomStorefront isFromOnlineStore isFromShopApp isFromShopPay isMostSignificantAbandonment lastBrowseAbandonmentDate lastCartAbandonmentDate lastCheckoutAbandonmentDate mostRecentStep visitStartedAt } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::abandonmentUpdateActivitiesDeliveryStatuses monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['abandonmentUpdateActivitiesDeliveryStatuses' => ['ok' => true]]], 200)]);
        $args = json_decode('{"abandonmentId": "gid://shopify/Thing/1", "marketingActivityId": "gid://shopify/Thing/1"}', true);

        $result = (new CheckoutMutations(shopifyGqlDomainClient()))->abandonmentUpdateActivitiesDeliveryStatuses($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation abandonmentUpdateActivitiesDeliveryStatuses($abandonmentId: ID!, $marketingActivityId: ID!) { abandonmentUpdateActivitiesDeliveryStatuses(abandonmentId: $abandonmentId, marketingActivityId: $marketingActivityId) { abandonment { abandonmentType cartUrl createdAt customerHasNoDraftOrderSinceAbandonment customerHasNoOrderSinceAbandonment daysSinceLastAbandonmentEmail emailSentAt emailState hoursSinceLastAbandonedCheckout id inventoryAvailable isFromCustomStorefront isFromOnlineStore isFromShopApp isFromShopPay isMostSignificantAbandonment lastBrowseAbandonmentDate lastCartAbandonmentDate lastCheckoutAbandonmentDate mostRecentStep visitStartedAt } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::checkoutBrandingUpsert monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['checkoutBrandingUpsert' => ['ok' => true]]], 200)]);
        $args = json_decode('{"checkoutProfileId": "gid://shopify/Thing/1", "checkoutBrandingInput": {}}', true);

        $result = (new CheckoutMutations(shopifyGqlDomainClient()))->checkoutBrandingUpsert($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation checkoutBrandingUpsert($checkoutProfileId: ID!, $checkoutBrandingInput: CheckoutBrandingInput) { checkoutBrandingUpsert(checkoutProfileId: $checkoutProfileId, checkoutBrandingInput: $checkoutBrandingInput) { userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

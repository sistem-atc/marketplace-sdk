<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\DraftOrderQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\DraftOrderMutations;

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

describe('Shopify GraphQL — dominio DraftOrder', function () {
    it('Queries::draftOrderSavedSearches monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['draftOrderSavedSearches' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new DraftOrderQueries(shopifyGqlDomainClient()))->draftOrderSavedSearches($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query draftOrderSavedSearches($first: Int, $after: String) { draftOrderSavedSearches(first: $first, after: $after) { edges { node { id legacyResourceId name query resourceType searchTerms } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::draftOrder monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['draftOrder' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new DraftOrderQueries(shopifyGqlDomainClient()))->draftOrder($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query draftOrder($id: ID!) { draftOrder(id: $id) { acceptAutomaticDiscounts allVariantPricesOverridden allowDiscountCodesInCheckout anyVariantPricesOverridden billingAddressMatchesShippingAddress completedAt createdAt currencyCode defaultCursor discountCodes email hasTimelineComment id invoiceEmailTemplateSubject invoiceSentAt invoiceUrl legacyResourceId marketName marketRegionCountryCode name note2 phone poNumber presentmentCurrencyCode ready reserveInventoryUntil status subtotalPrice tags taxExempt taxesIncluded totalPrice totalQuantityOfLineItems totalShippingPrice totalTax totalWeight transformerFingerprint updatedAt visibleToCustomer } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::draftOrderBulkAddTags monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['draftOrderBulkAddTags' => ['ok' => true]]], 200)]);
        $args = json_decode('{"search": "abc", "savedSearchId": "gid://shopify/Thing/1"}', true);

        $result = (new DraftOrderMutations(shopifyGqlDomainClient()))->draftOrderBulkAddTags($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation draftOrderBulkAddTags($search: String, $savedSearchId: ID) { draftOrderBulkAddTags(search: $search, savedSearchId: $savedSearchId) { job { done id } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::draftOrderBulkDelete monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['draftOrderBulkDelete' => ['ok' => true]]], 200)]);
        $args = json_decode('{"search": "abc", "savedSearchId": "gid://shopify/Thing/1"}', true);

        $result = (new DraftOrderMutations(shopifyGqlDomainClient()))->draftOrderBulkDelete($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation draftOrderBulkDelete($search: String, $savedSearchId: ID) { draftOrderBulkDelete(search: $search, savedSearchId: $savedSearchId) { job { done id } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

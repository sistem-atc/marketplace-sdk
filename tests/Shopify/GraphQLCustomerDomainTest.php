<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\CustomerQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\CustomerMutations;

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

describe('Shopify GraphQL — dominio Customer', function () {
    it('Queries::customerAccountPages monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['customerAccountPages' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new CustomerQueries(shopifyGqlDomainClient()))->customerAccountPages($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query customerAccountPages($first: Int, $after: String) { customerAccountPages(first: $first, after: $after) { edges { node { defaultCursor handle id title } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::customer monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['customer' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new CustomerQueries(shopifyGqlDomainClient()))->customer($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query customer($id: ID!) { customer(id: $id) { canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::customerSavedSearches monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['customerSavedSearches' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new CustomerQueries(shopifyGqlDomainClient()))->customerSavedSearches($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query customerSavedSearches($first: Int, $after: String) { customerSavedSearches(first: $first, after: $after) { edges { node { id legacyResourceId name query resourceType searchTerms } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::customerAddTaxExemptions monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['customerAddTaxExemptions' => ['ok' => true]]], 200)]);
        $args = json_decode('{"customerId": "gid://shopify/Thing/1", "taxExemptions": ["UNKNOWN"]}', true);

        $result = (new CustomerMutations(shopifyGqlDomainClient()))->customerAddTaxExemptions($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation customerAddTaxExemptions($customerId: ID!, $taxExemptions: [TaxExemption!]!) { customerAddTaxExemptions(customerId: $customerId, taxExemptions: $taxExemptions) { customer { canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::customerAddressCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['customerAddressCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"customerId": "gid://shopify/Thing/1", "address": {}}', true);

        $result = (new CustomerMutations(shopifyGqlDomainClient()))->customerAddressCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation customerAddressCreate($customerId: ID!, $address: MailingAddressInput!) { customerAddressCreate(customerId: $customerId, address: $address) { address { address1 address2 city company coordinatesValidated country countryCode countryCodeV2 firstName formatted formattedArea id lastName latitude longitude name phone province provinceCode timeZone validationResultSummary zip } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

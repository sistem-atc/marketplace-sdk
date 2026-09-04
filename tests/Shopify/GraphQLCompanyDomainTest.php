<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\CompanyQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\CompanyMutations;

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

describe('Shopify GraphQL — dominio Company', function () {
    it('Queries::companies monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['companies' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new CompanyQueries(shopifyGqlDomainClient()))->companies($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query companies($first: Int, $after: String) { companies(first: $first, after: $after) { edges { node { contactCount createdAt customerSince defaultCursor externalId hasTimelineComment id lifetimeDuration name note updatedAt } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::companiesCount monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['companiesCount' => ['ok' => true]]], 200)]);
        $args = json_decode('{"limit": 5}', true);

        $result = (new CompanyQueries(shopifyGqlDomainClient()))->companiesCount($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query companiesCount($limit: Int) { companiesCount(limit: $limit) { count precision } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::companiesDelete monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['companiesDelete' => ['ok' => true]]], 200)]);
        $args = json_decode('{"companyIds": ["gid://shopify/Thing/1"]}', true);

        $result = (new CompanyMutations(shopifyGqlDomainClient()))->companiesDelete($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation companiesDelete($companyIds: [ID!]!) { companiesDelete(companyIds: $companyIds) { deletedCompanyIds userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::companyAddressDelete monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['companyAddressDelete' => ['ok' => true]]], 200)]);
        $args = json_decode('{"addressId": "gid://shopify/Thing/1"}', true);

        $result = (new CompanyMutations(shopifyGqlDomainClient()))->companyAddressDelete($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation companyAddressDelete($addressId: ID!) { companyAddressDelete(addressId: $addressId) { deletedAddressId userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::companyLocationAssignTaxExemptions monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['companyLocationAssignTaxExemptions' => ['ok' => true]]], 200)]);
        $args = json_decode('{"companyLocationId": "gid://shopify/Thing/1", "taxExemptions": ["UNKNOWN"]}', true);

        $result = (new CompanyMutations(shopifyGqlDomainClient()))->companyLocationAssignTaxExemptions($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation companyLocationAssignTaxExemptions($companyLocationId: ID!, $taxExemptions: [TaxExemption!]!) { companyLocationAssignTaxExemptions(companyLocationId: $companyLocationId, taxExemptions: $taxExemptions) { companyLocation { createdAt currency defaultCursor externalId hasTimelineComment id inCatalog locale name note orderCount phone taxExemptions taxRegistrationId updatedAt } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

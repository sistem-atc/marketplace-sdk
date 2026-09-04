<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\PaymentQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\PaymentMutations;

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

describe('Shopify GraphQL — dominio Payment', function () {
    it('Queries::disputes monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['disputes' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new PaymentQueries(shopifyGqlDomainClient()))->disputes($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query disputes($first: Int, $after: String) { disputes(first: $first, after: $after) { edges { node { evidenceDueBy evidenceSentOn finalizedOn id initiatedAt legacyResourceId status type } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::dispute monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['dispute' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new PaymentQueries(shopifyGqlDomainClient()))->dispute($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query dispute($id: ID!) { dispute(id: $id) { evidenceDueBy evidenceSentOn finalizedOn id initiatedAt legacyResourceId status type } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::disputeEvidenceUpdate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['disputeEvidenceUpdate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1", "input": {}}', true);

        $result = (new PaymentMutations(shopifyGqlDomainClient()))->disputeEvidenceUpdate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation disputeEvidenceUpdate($id: ID!, $input: ShopifyPaymentsDisputeEvidenceUpdateInput!) { disputeEvidenceUpdate(id: $id, input: $input) { disputeEvidence { accessActivityLog cancellationPolicyDisclosure cancellationRebuttal customerEmailAddress customerFirstName customerLastName customerPurchaseIp id productDescription refundPolicyDisclosure refundRefusalExplanation submitted uncategorizedText } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::paymentCustomizationActivation monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['paymentCustomizationActivation' => ['ok' => true]]], 200)]);
        $args = json_decode('{"ids": ["gid://shopify/Thing/1"], "enabled": true}', true);

        $result = (new PaymentMutations(shopifyGqlDomainClient()))->paymentCustomizationActivation($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation paymentCustomizationActivation($ids: [ID!]!, $enabled: Boolean!) { paymentCustomizationActivation(ids: $ids, enabled: $enabled) { ids userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

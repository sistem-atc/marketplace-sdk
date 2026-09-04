<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\ReturnQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\ReturnMutations;

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

describe('Shopify GraphQL — dominio Return', function () {
    it('Queries::returnReasonDefinitions monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['returnReasonDefinitions' => ['ok' => true]]], 200)]);
        $args = json_decode('{"ids": ["gid://shopify/Thing/1"], "handles": ["abc"]}', true);

        $result = (new ReturnQueries(shopifyGqlDomainClient()))->returnReasonDefinitions($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query returnReasonDefinitions($ids: [ID!], $handles: [String!]) { returnReasonDefinitions(ids: $ids, handles: $handles) { edges { node { deleted handle id name } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::return monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['return' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new ReturnQueries(shopifyGqlDomainClient()))->return($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query return($id: ID!) { return(id: $id) { closedAt createdAt id name requestApprovedAt status totalQuantity } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::removeFromReturn monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['removeFromReturn' => ['ok' => true]]], 200)]);
        $args = json_decode('{"returnId": "gid://shopify/Thing/1", "returnLineItems": [{}]}', true);

        $result = (new ReturnMutations(shopifyGqlDomainClient()))->removeFromReturn($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation removeFromReturn($returnId: ID!, $returnLineItems: [ReturnLineItemRemoveFromReturnInput!]) { removeFromReturn(returnId: $returnId, returnLineItems: $returnLineItems) { return { closedAt createdAt id name requestApprovedAt status totalQuantity } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::returnApproveRequest monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['returnApproveRequest' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new ReturnMutations(shopifyGqlDomainClient()))->returnApproveRequest($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation returnApproveRequest($input: ReturnApproveRequestInput!) { returnApproveRequest(input: $input) { return { closedAt createdAt id name requestApprovedAt status totalQuantity } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::returnLineItemRemoveFromReturn monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['returnLineItemRemoveFromReturn' => ['ok' => true]]], 200)]);
        $args = json_decode('{"returnId": "gid://shopify/Thing/1", "returnLineItems": [{}]}', true);

        $result = (new ReturnMutations(shopifyGqlDomainClient()))->returnLineItemRemoveFromReturn($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation returnLineItemRemoveFromReturn($returnId: ID!, $returnLineItems: [ReturnLineItemRemoveFromReturnInput!]!) { returnLineItemRemoveFromReturn(returnId: $returnId, returnLineItems: $returnLineItems) { return { closedAt createdAt id name requestApprovedAt status totalQuantity } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\GiftCardQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\GiftCardMutations;

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

describe('Shopify GraphQL — dominio GiftCard', function () {
    it('Queries::giftCards monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['giftCards' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new GiftCardQueries(shopifyGqlDomainClient()))->giftCards($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query giftCards($first: Int, $after: String) { giftCards(first: $first, after: $after) { edges { node { createdAt crossCurrencyRedemptionStrategy deactivatedAt enabled expiresOn id isRedeemable lastCharacters maskedCode note templateSuffix updatedAt } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::giftCard monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['giftCard' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new GiftCardQueries(shopifyGqlDomainClient()))->giftCard($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query giftCard($id: ID!) { giftCard(id: $id) { createdAt crossCurrencyRedemptionStrategy deactivatedAt enabled expiresOn id isRedeemable lastCharacters maskedCode note templateSuffix updatedAt } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::giftCardCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['giftCardCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new GiftCardMutations(shopifyGqlDomainClient()))->giftCardCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation giftCardCreate($input: GiftCardCreateInput!) { giftCardCreate(input: $input) { giftCard { createdAt crossCurrencyRedemptionStrategy deactivatedAt enabled expiresOn id isRedeemable lastCharacters maskedCode note templateSuffix updatedAt } giftCardCode userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::giftCardCredit monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['giftCardCredit' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1", "creditInput": {}}', true);

        $result = (new GiftCardMutations(shopifyGqlDomainClient()))->giftCardCredit($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation giftCardCredit($id: ID!, $creditInput: GiftCardCreditInput!) { giftCardCredit(id: $id, creditInput: $creditInput) { giftCardCreditTransaction { id note processedAt } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

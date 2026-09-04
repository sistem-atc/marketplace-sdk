<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\StoreCreditQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\StoreCreditMutations;

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

describe('Shopify GraphQL — dominio StoreCredit', function () {
    it('Queries::storeCreditAccount monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['storeCreditAccount' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new StoreCreditQueries(shopifyGqlDomainClient()))->storeCreditAccount($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query storeCreditAccount($id: ID!) { storeCreditAccount(id: $id) { id } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::storeCreditConfiguration monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['storeCreditConfiguration' => ['ok' => true]]], 200)]);
        $args = json_decode('{}', true);

        $result = (new StoreCreditQueries(shopifyGqlDomainClient()))->storeCreditConfiguration($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query storeCreditConfiguration { storeCreditConfiguration { storeCreditEnabled } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::storeCreditAccountCredit monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['storeCreditAccountCredit' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1", "creditInput": {}}', true);

        $result = (new StoreCreditMutations(shopifyGqlDomainClient()))->storeCreditAccountCredit($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation storeCreditAccountCredit($id: ID!, $creditInput: StoreCreditAccountCreditInput!) { storeCreditAccountCredit(id: $id, creditInput: $creditInput) { storeCreditAccountTransaction { createdAt event expiresAt id } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::storeCreditAccountDebit monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['storeCreditAccountDebit' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1", "debitInput": {}}', true);

        $result = (new StoreCreditMutations(shopifyGqlDomainClient()))->storeCreditAccountDebit($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation storeCreditAccountDebit($id: ID!, $debitInput: StoreCreditAccountDebitInput!) { storeCreditAccountDebit(id: $id, debitInput: $debitInput) { storeCreditAccountTransaction { createdAt event id } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

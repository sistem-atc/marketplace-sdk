<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\AppQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\AppMutations;

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

describe('Shopify GraphQL — dominio App', function () {
    it('Queries::appDiscountTypesNodes monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['appDiscountTypesNodes' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new AppQueries(shopifyGqlDomainClient()))->appDiscountTypesNodes($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query appDiscountTypesNodes($first: Int, $after: String) { appDiscountTypesNodes(first: $first, after: $after) { edges { node { appKey description discountClass discountClasses functionId targetType title } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::app monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['app' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new AppQueries(shopifyGqlDomainClient()))->app($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query app($id: ID) { app(id: $id) { apiKey appStoreAppUrl appStoreDeveloperUrl description developerName developerType developerUrl embedded features handle id installUrl isPostPurchaseAppInUse launchUrl previouslyInstalled pricingDetails pricingDetailsSummary privacyPolicyUrl publicCategory published shopifyDeveloped title uninstallMessage uninstallUrl webhookApiVersion } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::appPurchaseOneTimeCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['appPurchaseOneTimeCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"name": "abc", "price": {}}', true);

        $result = (new AppMutations(shopifyGqlDomainClient()))->appPurchaseOneTimeCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation appPurchaseOneTimeCreate($name: String!, $price: MoneyInput!) { appPurchaseOneTimeCreate(name: $name, price: $price) { appPurchaseOneTime { createdAt id name status test } confirmationUrl userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::appUninstall monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['appUninstall' => ['ok' => true]]], 200)]);
        $args = json_decode('{}', true);

        $result = (new AppMutations(shopifyGqlDomainClient()))->appUninstall($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation appUninstall { appUninstall { app { apiKey appStoreAppUrl appStoreDeveloperUrl description developerName developerType developerUrl embedded features handle id installUrl isPostPurchaseAppInUse launchUrl previouslyInstalled pricingDetails pricingDetailsSummary privacyPolicyUrl publicCategory published shopifyDeveloped title uninstallMessage uninstallUrl webhookApiVersion } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

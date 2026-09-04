<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\MetafieldQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\MetafieldMutations;

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

describe('Shopify GraphQL — dominio Metafield', function () {
    it('Queries::metafieldDefinitions monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['metafieldDefinitions' => ['ok' => true]]], 200)]);
        $args = json_decode('{"key": "abc", "namespace": "abc"}', true);

        $result = (new MetafieldQueries(shopifyGqlDomainClient()))->metafieldDefinitions($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query metafieldDefinitions($key: String, $namespace: String) { metafieldDefinitions(key: $key, namespace: $namespace) { edges { node { description id key metafieldsCount name namespace ownerType pinnedPosition useAsCollectionCondition validationStatus } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::metafieldDefinition monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['metafieldDefinition' => ['ok' => true]]], 200)]);
        $args = json_decode('{"identifier": {}}', true);

        $result = (new MetafieldQueries(shopifyGqlDomainClient()))->metafieldDefinition($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query metafieldDefinition($identifier: MetafieldDefinitionIdentifierInput) { metafieldDefinition(identifier: $identifier) { description id key metafieldsCount name namespace ownerType pinnedPosition useAsCollectionCondition validationStatus } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::metafieldDefinitionCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['metafieldDefinitionCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"definition": {}}', true);

        $result = (new MetafieldMutations(shopifyGqlDomainClient()))->metafieldDefinitionCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation metafieldDefinitionCreate($definition: MetafieldDefinitionInput!) { metafieldDefinitionCreate(definition: $definition) { createdDefinition { description id key metafieldsCount name namespace ownerType pinnedPosition useAsCollectionCondition validationStatus } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::metafieldDefinitionDelete monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['metafieldDefinitionDelete' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1", "identifier": {}}', true);

        $result = (new MetafieldMutations(shopifyGqlDomainClient()))->metafieldDefinitionDelete($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation metafieldDefinitionDelete($id: ID, $identifier: MetafieldDefinitionIdentifierInput) { metafieldDefinitionDelete(id: $id, identifier: $identifier) { deletedDefinition { key namespace ownerType } deletedDefinitionId userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

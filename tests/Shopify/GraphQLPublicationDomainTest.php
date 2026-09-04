<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\PublicationQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\PublicationMutations;

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

describe('Shopify GraphQL — dominio Publication', function () {
    it('Queries::publications monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['publications' => ['ok' => true]]], 200)]);
        $args = json_decode('{"catalogType": "UNKNOWN", "first": 5}', true);

        $result = (new PublicationQueries(shopifyGqlDomainClient()))->publications($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query publications($catalogType: CatalogType, $first: Int) { publications(catalogType: $catalogType, first: $first) { edges { node { autoPublish hasCollection id name supportsFuturePublishing } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::publication monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['publication' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new PublicationQueries(shopifyGqlDomainClient()))->publication($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query publication($id: ID!) { publication(id: $id) { autoPublish hasCollection id name supportsFuturePublishing } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::publicationCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['publicationCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new PublicationMutations(shopifyGqlDomainClient()))->publicationCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation publicationCreate($input: PublicationCreateInput!) { publicationCreate(input: $input) { publication { autoPublish hasCollection id name supportsFuturePublishing } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::publicationDelete monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['publicationDelete' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new PublicationMutations(shopifyGqlDomainClient()))->publicationDelete($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation publicationDelete($id: ID!) { publicationDelete(id: $id) { deletedId userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::publishablePublishToCurrentChannel monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['publishablePublishToCurrentChannel' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new PublicationMutations(shopifyGqlDomainClient()))->publishablePublishToCurrentChannel($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation publishablePublishToCurrentChannel($id: ID!) { publishablePublishToCurrentChannel(id: $id) { publishable { publishedOnCurrentChannel publishedOnCurrentPublication } shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

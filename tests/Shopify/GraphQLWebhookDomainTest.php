<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\WebhookQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\WebhookMutations;

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

describe('Shopify GraphQL — dominio Webhook', function () {
    it('Queries::webhookSubscriptions monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['webhookSubscriptions' => ['ok' => true]]], 200)]);
        $args = json_decode('{"uri": "abc", "format": "UNKNOWN"}', true);

        $result = (new WebhookQueries(shopifyGqlDomainClient()))->webhookSubscriptions($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query webhookSubscriptions($uri: String, $format: WebhookSubscriptionFormat) { webhookSubscriptions(uri: $uri, format: $format) { edges { node { callbackUrl createdAt filter format id includeFields legacyResourceId metafieldNamespaces name topic updatedAt uri } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::webhookSubscription monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['webhookSubscription' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new WebhookQueries(shopifyGqlDomainClient()))->webhookSubscription($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query webhookSubscription($id: ID!) { webhookSubscription(id: $id) { callbackUrl createdAt filter format id includeFields legacyResourceId metafieldNamespaces name topic updatedAt uri } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::eventBridgeWebhookSubscriptionCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['eventBridgeWebhookSubscriptionCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"topic": "UNKNOWN", "webhookSubscription": {}}', true);

        $result = (new WebhookMutations(shopifyGqlDomainClient()))->eventBridgeWebhookSubscriptionCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation eventBridgeWebhookSubscriptionCreate($topic: WebhookSubscriptionTopic!, $webhookSubscription: EventBridgeWebhookSubscriptionInput!) { eventBridgeWebhookSubscriptionCreate(topic: $topic, webhookSubscription: $webhookSubscription) { webhookSubscription { callbackUrl createdAt filter format id includeFields legacyResourceId metafieldNamespaces name topic updatedAt uri } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::eventBridgeWebhookSubscriptionUpdate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['eventBridgeWebhookSubscriptionUpdate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1", "webhookSubscription": {}}', true);

        $result = (new WebhookMutations(shopifyGqlDomainClient()))->eventBridgeWebhookSubscriptionUpdate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation eventBridgeWebhookSubscriptionUpdate($id: ID!, $webhookSubscription: EventBridgeWebhookSubscriptionInput!) { eventBridgeWebhookSubscriptionUpdate(id: $id, webhookSubscription: $webhookSubscription) { webhookSubscription { callbackUrl createdAt filter format id includeFields legacyResourceId metafieldNamespaces name topic updatedAt uri } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::pubSubWebhookSubscriptionCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['pubSubWebhookSubscriptionCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"topic": "UNKNOWN", "webhookSubscription": {}}', true);

        $result = (new WebhookMutations(shopifyGqlDomainClient()))->pubSubWebhookSubscriptionCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation pubSubWebhookSubscriptionCreate($topic: WebhookSubscriptionTopic!, $webhookSubscription: PubSubWebhookSubscriptionInput!) { pubSubWebhookSubscriptionCreate(topic: $topic, webhookSubscription: $webhookSubscription) { webhookSubscription { callbackUrl createdAt filter format id includeFields legacyResourceId metafieldNamespaces name topic updatedAt uri } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

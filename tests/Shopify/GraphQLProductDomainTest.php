<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\ProductQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\ProductMutations;

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

describe('Shopify GraphQL — dominio Product', function () {
    it('Queries::productFeeds monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['productFeeds' => ['ok' => true]]], 200)]);
        $args = json_decode('{"first": 5, "after": "abc"}', true);

        $result = (new ProductQueries(shopifyGqlDomainClient()))->productFeeds($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query productFeeds($first: Int, $after: String) { productFeeds(first: $first, after: $after) { edges { node { channelId country id language status } } pageInfo { hasNextPage endCursor } } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::product monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['product' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new ProductQueries(shopifyGqlDomainClient()))->product($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query product($id: ID!) { product(id: $id) { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } }'
                && $body['variables'] === $args;
        });
    });
    it('Queries::productByHandle monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['productByHandle' => ['ok' => true]]], 200)]);
        $args = json_decode('{"handle": "abc"}', true);

        $result = (new ProductQueries(shopifyGqlDomainClient()))->productByHandle($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query productByHandle($handle: String!) { productByHandle(handle: $handle) { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::productBundleCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['productBundleCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new ProductMutations(shopifyGqlDomainClient()))->productBundleCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation productBundleCreate($input: ProductBundleCreateInput!) { productBundleCreate(input: $input) { productBundleOperation { id status } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::productBundleUpdate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['productBundleUpdate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new ProductMutations(shopifyGqlDomainClient()))->productBundleUpdate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation productBundleUpdate($input: ProductBundleUpdateInput!) { productBundleUpdate(input: $input) { productBundleOperation { id status } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::productChangeStatus monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['productChangeStatus' => ['ok' => true]]], 200)]);
        $args = json_decode('{"productId": "gid://shopify/Thing/1", "status": "UNKNOWN"}', true);

        $result = (new ProductMutations(shopifyGqlDomainClient()))->productChangeStatus($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation productChangeStatus($productId: ID!, $status: ProductStatus!) { productChangeStatus(productId: $productId, status: $status) { product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

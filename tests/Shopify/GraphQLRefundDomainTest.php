<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Shopify\GraphQL\Queries\RefundQueries;
use SistemAtc\Marketplaces\Shopify\GraphQL\Mutations\RefundMutations;

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

describe('Shopify GraphQL — dominio Refund', function () {
    it('Queries::refund monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['refund' => ['ok' => true]]], 200)]);
        $args = json_decode('{"id": "gid://shopify/Thing/1"}', true);

        $result = (new RefundQueries(shopifyGqlDomainClient()))->refund($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'query refund($id: ID!) { refund(id: $id) { createdAt id legacyResourceId note processedAt updatedAt } }'
                && $body['variables'] === $args;
        });
    });
    it('Mutations::refundCreate monta o documento com variaveis tipadas e selection default', function () {
        Http::fake(['*/graphql.json' => Http::response(['data' => ['refundCreate' => ['ok' => true]]], 200)]);
        $args = json_decode('{"input": {}}', true);

        $result = (new RefundMutations(shopifyGqlDomainClient()))->refundCreate($args);

        expect($result)->toBe(['ok' => true]);
        Http::assertSent(function (Request $request) use ($args) {
            $body = json_decode($request->body(), true);

            return $request->method() === 'POST'
                && $request->url() === 'https://loja-teste.myshopify.com/admin/api/2026-07/graphql.json'
                && $request->hasHeader('X-Shopify-Access-Token', 'shpat_x')
                && $body['query'] === 'mutation refundCreate($input: RefundInput!) { refundCreate(input: $input) { order { billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt } refund { createdAt id legacyResourceId note processedAt updatedAt } userErrors { field message } } }'
                && $body['variables'] === $args;
        });
    });
});

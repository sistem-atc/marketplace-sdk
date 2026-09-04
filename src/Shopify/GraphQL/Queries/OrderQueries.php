<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Order.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class OrderQueries extends BaseOperations
{
    /**
     * The `order` query retrieves an [order](https://shopify.dev/docs/api/admin-graphql/latest/objects/order) by its ID. This query provides access to comprehensive order information such as customer detail
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function order(array $args = [], string $selection = 'billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt'): array
    {
        return $this->execute('query', 'order', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns approved order attribution definitions for the calling app on the current shop. Learn more in the [order attribution guide](https://shopify.dev/docs/apps/build/sales-channels/order-attribution
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderAttributionDefinitions(array $args = [], string $selection = 'edges { node { displayName handle icon id } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'orderAttributionDefinitions', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Return an order by an identifier.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: identifier: OrderIdentifierInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderByIdentifier(array $args = [], string $selection = 'billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt'): array
    {
        return $this->execute('query', 'orderByIdentifier', $args, ['identifier' => 'OrderIdentifierInput!'], $selection);
    }

    /**
     * Returns a `OrderEditSession` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderEditSession(array $args = [], string $selection = 'id'): array
    {
        return $this->execute('query', 'orderEditSession', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Retrieves the status of a deferred payment by its payment reference ID. Use this query to monitor the processing status of payments that are initiated through payment mutations. Deferred payments are
     *
     * @param array<string,mixed> $args Variaveis GraphQL: paymentReferenceId: String!, orderId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderPaymentStatus(array $args = [], string $selection = 'errorMessage paymentReferenceId status translatedErrorMessage'): array
    {
        return $this->execute('query', 'orderPaymentStatus', $args, ['paymentReferenceId' => 'String!', 'orderId' => 'ID!'], $selection);
    }

    /**
     * Returns [saved searches](https://shopify.dev/docs/api/admin-graphql/latest/objects/SavedSearch) for orders in the shop. Saved searches store search queries with their filters and search terms.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderSavedSearches(array $args = [], string $selection = 'edges { node { id legacyResourceId name query resourceType searchTerms } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'orderSavedSearches', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Returns a list of [orders](https://shopify.dev/api/admin-graphql/latest/objects/Order) placed in the store, including data such as order status, customer, and line item details. Use the `orders` query
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: OrderSortKeys, query: String, savedSearchId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orders(array $args = [], string $selection = 'edges { node { billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'orders', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'OrderSortKeys', 'query' => 'String', 'savedSearchId' => 'ID'], $selection);
    }

    /**
     * Returns the number of [orders](https://shopify.dev/docs/api/admin-graphql/latest/objects/Order) in the shop. You can filter orders using [search syntax](https://shopify.dev/docs/api/usage/search-synta
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String, savedSearchId: ID, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function ordersCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'ordersCount', $args, ['query' => 'String', 'savedSearchId' => 'ID', 'limit' => 'Int'], $selection);
    }

    /**
     * The number of pendings orders. Limited to a maximum of 10000.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pendingOrdersCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'pendingOrdersCount', $args, [], $selection);
    }
}

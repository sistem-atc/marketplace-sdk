<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio DraftOrder.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class DraftOrderQueries extends BaseOperations
{
    /**
     * Retrieves a [draft order](https://shopify.dev/docs/api/admin-graphql/latest/objects/DraftOrder) by its ID. A draft order is an order created by a merchant on behalf of their customers. Draft orders co
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrder(array $args = [], string $selection = 'acceptAutomaticDiscounts allVariantPricesOverridden allowDiscountCodesInCheckout anyVariantPricesOverridden billingAddressMatchesShippingAddress completedAt createdAt currencyCode defaultCursor discountCodes email hasTimelineComment id invoiceEmailTemplateSubject invoiceSentAt invoiceUrl legacyResourceId marketName marketRegionCountryCode name note2 phone poNumber presentmentCurrencyCode ready reserveInventoryUntil status subtotalPrice tags taxExempt taxesIncluded totalPrice totalQuantityOfLineItems totalShippingPrice totalTax totalWeight transformerFingerprint updatedAt visibleToCustomer'): array
    {
        return $this->execute('query', 'draftOrder', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Available delivery options for a [`DraftOrder`](https://shopify.dev/docs/api/admin-graphql/latest/objects/DraftOrder) based on the provided input. The query returns shipping rates, local delivery rate
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: DraftOrderAvailableDeliveryOptionsInput!, search: String, localPickupFrom: Int, localPickupCount: Int, sessionToken: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrderAvailableDeliveryOptions(array $args = [], string $selection = '__typename'): array
    {
        return $this->execute('query', 'draftOrderAvailableDeliveryOptions', $args, ['input' => 'DraftOrderAvailableDeliveryOptionsInput!', 'search' => 'String', 'localPickupFrom' => 'Int', 'localPickupCount' => 'Int', 'sessionToken' => 'String'], $selection);
    }

    /**
     * List of the shop's draft order saved searches.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrderSavedSearches(array $args = [], string $selection = 'edges { node { id legacyResourceId name query resourceType searchTerms } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'draftOrderSavedSearches', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Returns a `DraftOrderTag` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrderTag(array $args = [], string $selection = 'handle id title'): array
    {
        return $this->execute('query', 'draftOrderTag', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * List of saved draft orders.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: DraftOrderSortKeys, query: String, savedSearchId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrders(array $args = [], string $selection = 'edges { node { acceptAutomaticDiscounts allVariantPricesOverridden allowDiscountCodesInCheckout anyVariantPricesOverridden billingAddressMatchesShippingAddress completedAt createdAt currencyCode defaultCursor discountCodes email hasTimelineComment id invoiceEmailTemplateSubject invoiceSentAt invoiceUrl legacyResourceId marketName marketRegionCountryCode name note2 phone poNumber presentmentCurrencyCode ready reserveInventoryUntil status subtotalPrice tags taxExempt taxesIncluded totalPrice totalQuantityOfLineItems totalShippingPrice totalTax totalWeight transformerFingerprint updatedAt visibleToCustomer } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'draftOrders', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'DraftOrderSortKeys', 'query' => 'String', 'savedSearchId' => 'ID'], $selection);
    }

    /**
     * Returns the number of draft orders that match the query. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String, savedSearchId: ID, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrdersCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'draftOrdersCount', $args, ['query' => 'String', 'savedSearchId' => 'ID', 'limit' => 'Int'], $selection);
    }
}

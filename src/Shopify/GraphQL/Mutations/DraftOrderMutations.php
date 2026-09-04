<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio DraftOrder.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class DraftOrderMutations extends BaseOperations
{
    /**
     * Adds tags to multiple draft orders.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: search: String, savedSearchId: ID, ids: [ID!], tags: [String!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrderBulkAddTags(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'draftOrderBulkAddTags', $args, ['search' => 'String', 'savedSearchId' => 'ID', 'ids' => '[ID!]', 'tags' => '[String!]!'], $selection);
    }

    /**
     * Deletes multiple draft orders.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: search: String, savedSearchId: ID, ids: [ID!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrderBulkDelete(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'draftOrderBulkDelete', $args, ['search' => 'String', 'savedSearchId' => 'ID', 'ids' => '[ID!]'], $selection);
    }

    /**
     * Removes tags from multiple draft orders.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: search: String, savedSearchId: ID, ids: [ID!], tags: [String!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrderBulkRemoveTags(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'draftOrderBulkRemoveTags', $args, ['search' => 'String', 'savedSearchId' => 'ID', 'ids' => '[ID!]', 'tags' => '[String!]!'], $selection);
    }

    /**
     * Calculates the properties of a [`DraftOrder`](https://shopify.dev/docs/api/admin-graphql/latest/objects/DraftOrder) without creating it. Returns pricing information including [`CalculatedDraftOrderLin
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: DraftOrderInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrderCalculate(array $args = [], string $selection = 'calculatedDraftOrder { acceptAutomaticDiscounts allVariantPricesOverridden anyVariantPricesOverridden billingAddressMatchesShippingAddress currencyCode discountCodes marketName marketRegionCountryCode phone presentmentCurrencyCode subtotalPrice taxesIncluded totalPrice totalQuantityOfLineItems totalShippingPrice totalTax transformerFingerprint } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'draftOrderCalculate', $args, ['input' => 'DraftOrderInput!'], $selection);
    }

    /**
     * Completes a [draft order](https://shopify.dev/docs/api/admin-graphql/latest/objects/DraftOrder) and converts it into a [regular order](https://shopify.dev/docs/api/admin-graphql/latest/objects/Order).
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, paymentGatewayId: ID, sourceName: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrderComplete(array $args = [], string $selection = 'draftOrder { acceptAutomaticDiscounts allVariantPricesOverridden allowDiscountCodesInCheckout anyVariantPricesOverridden billingAddressMatchesShippingAddress completedAt createdAt currencyCode defaultCursor discountCodes email hasTimelineComment id invoiceEmailTemplateSubject invoiceSentAt invoiceUrl legacyResourceId marketName marketRegionCountryCode name note2 phone poNumber presentmentCurrencyCode ready reserveInventoryUntil status subtotalPrice tags taxExempt taxesIncluded totalPrice totalQuantityOfLineItems totalShippingPrice totalTax totalWeight transformerFingerprint updatedAt visibleToCustomer } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'draftOrderComplete', $args, ['id' => 'ID!', 'paymentGatewayId' => 'ID', 'sourceName' => 'String'], $selection);
    }

    /**
     * Creates a [draft order](https://shopify.dev/docs/api/admin-graphql/latest/objects/DraftOrder) with attributes such as customer information, line items, shipping and billing addresses, and payment term
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: DraftOrderInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrderCreate(array $args = [], string $selection = 'draftOrder { acceptAutomaticDiscounts allVariantPricesOverridden allowDiscountCodesInCheckout anyVariantPricesOverridden billingAddressMatchesShippingAddress completedAt createdAt currencyCode defaultCursor discountCodes email hasTimelineComment id invoiceEmailTemplateSubject invoiceSentAt invoiceUrl legacyResourceId marketName marketRegionCountryCode name note2 phone poNumber presentmentCurrencyCode ready reserveInventoryUntil status subtotalPrice tags taxExempt taxesIncluded totalPrice totalQuantityOfLineItems totalShippingPrice totalTax totalWeight transformerFingerprint updatedAt visibleToCustomer } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'draftOrderCreate', $args, ['input' => 'DraftOrderInput!'], $selection);
    }

    /**
     * Creates a draft order from order.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: orderId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrderCreateFromOrder(array $args = [], string $selection = 'draftOrder { acceptAutomaticDiscounts allVariantPricesOverridden allowDiscountCodesInCheckout anyVariantPricesOverridden billingAddressMatchesShippingAddress completedAt createdAt currencyCode defaultCursor discountCodes email hasTimelineComment id invoiceEmailTemplateSubject invoiceSentAt invoiceUrl legacyResourceId marketName marketRegionCountryCode name note2 phone poNumber presentmentCurrencyCode ready reserveInventoryUntil status subtotalPrice tags taxExempt taxesIncluded totalPrice totalQuantityOfLineItems totalShippingPrice totalTax totalWeight transformerFingerprint updatedAt visibleToCustomer } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'draftOrderCreateFromOrder', $args, ['orderId' => 'ID!'], $selection);
    }

    /**
     * Deletes a draft order.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: DraftOrderDeleteInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrderDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'draftOrderDelete', $args, ['input' => 'DraftOrderDeleteInput!'], $selection);
    }

    /**
     * Duplicates a draft order.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrderDuplicate(array $args = [], string $selection = 'draftOrder { acceptAutomaticDiscounts allVariantPricesOverridden allowDiscountCodesInCheckout anyVariantPricesOverridden billingAddressMatchesShippingAddress completedAt createdAt currencyCode defaultCursor discountCodes email hasTimelineComment id invoiceEmailTemplateSubject invoiceSentAt invoiceUrl legacyResourceId marketName marketRegionCountryCode name note2 phone poNumber presentmentCurrencyCode ready reserveInventoryUntil status subtotalPrice tags taxExempt taxesIncluded totalPrice totalQuantityOfLineItems totalShippingPrice totalTax totalWeight transformerFingerprint updatedAt visibleToCustomer } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'draftOrderDuplicate', $args, ['id' => 'ID'], $selection);
    }

    /**
     * Previews a draft order invoice email.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, email: EmailInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrderInvoicePreview(array $args = [], string $selection = 'previewHtml previewSubject userErrors { field message }'): array
    {
        return $this->execute('mutation', 'draftOrderInvoicePreview', $args, ['id' => 'ID!', 'email' => 'EmailInput'], $selection);
    }

    /**
     * Sends an invoice email for a [`DraftOrder`](https://shopify.dev/docs/api/admin-graphql/latest/objects/DraftOrder). The invoice includes a secure checkout link for reviewing and paying for the order. U
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, email: EmailInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrderInvoiceSend(array $args = [], string $selection = 'draftOrder { acceptAutomaticDiscounts allVariantPricesOverridden allowDiscountCodesInCheckout anyVariantPricesOverridden billingAddressMatchesShippingAddress completedAt createdAt currencyCode defaultCursor discountCodes email hasTimelineComment id invoiceEmailTemplateSubject invoiceSentAt invoiceUrl legacyResourceId marketName marketRegionCountryCode name note2 phone poNumber presentmentCurrencyCode ready reserveInventoryUntil status subtotalPrice tags taxExempt taxesIncluded totalPrice totalQuantityOfLineItems totalShippingPrice totalTax totalWeight transformerFingerprint updatedAt visibleToCustomer } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'draftOrderInvoiceSend', $args, ['id' => 'ID!', 'email' => 'EmailInput'], $selection);
    }

    /**
     * Updates a draft order. If a checkout has been started for a draft order, any update to the draft will unlink the checkout. Checkouts are created but not immediately completed when opening the merchan
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: DraftOrderInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function draftOrderUpdate(array $args = [], string $selection = 'draftOrder { acceptAutomaticDiscounts allVariantPricesOverridden allowDiscountCodesInCheckout anyVariantPricesOverridden billingAddressMatchesShippingAddress completedAt createdAt currencyCode defaultCursor discountCodes email hasTimelineComment id invoiceEmailTemplateSubject invoiceSentAt invoiceUrl legacyResourceId marketName marketRegionCountryCode name note2 phone poNumber presentmentCurrencyCode ready reserveInventoryUntil status subtotalPrice tags taxExempt taxesIncluded totalPrice totalQuantityOfLineItems totalShippingPrice totalTax totalWeight transformerFingerprint updatedAt visibleToCustomer } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'draftOrderUpdate', $args, ['id' => 'ID!', 'input' => 'DraftOrderInput!'], $selection);
    }
}

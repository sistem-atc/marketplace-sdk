<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Order.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class OrderMutations extends BaseOperations
{
    /**
     * Deletes an order attribution definition for the calling app on the current shop. Existing orders attributed to this definition are preserved. Learn more in the [order attribution guide](https://shopi
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderAttributionDefinitionDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderAttributionDefinitionDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Upserts an order attribution definition for the calling app on the current shop. Apps can use attribution definitions to label orders they route to a shop, replacing or augmenting any definitions ship
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: OrderAttributionDefinitionUpsertInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderAttributionDefinitionUpsert(array $args = [], string $selection = 'orderAttributionDefinition { displayName handle icon id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderAttributionDefinitionUpsert', $args, ['input' => 'OrderAttributionDefinitionUpsertInput!'], $selection);
    }

    /**
     * Cancels an order, with options for refunding, restocking inventory, and customer notification. > Caution: > Order cancellation is irreversible. An order that has been cancelled can't be restored to i
     *
     * @param array<string,mixed> $args Variaveis GraphQL: orderId: ID!, refundMethod: OrderCancelRefundMethodInput, restock: Boolean!, reason: OrderCancelReason!, notifyCustomer: Boolean, staffNote: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderCancel(array $args = [], string $selection = 'job { done id } orderCancelUserErrors { field message } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderCancel', $args, ['orderId' => 'ID!', 'refundMethod' => 'OrderCancelRefundMethodInput', 'restock' => 'Boolean!', 'reason' => 'OrderCancelReason!', 'notifyCustomer' => 'Boolean', 'staffNote' => 'String'], $selection);
    }

    /**
     * Captures payment for an authorized transaction on an order. Use this mutation to claim the money that was previously reserved by an authorization transaction. The `orderCapture` mutation can be used
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: OrderCaptureInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderCapture(array $args = [], string $selection = 'transaction { accountNumber amount authorizationCode authorizationExpiresAt createdAt errorCode formattedGateway gateway id kind manualPaymentGateway manuallyCapturable maximumRefundable multiCapturable paymentId paymentMethod processedAt receiptJson settlementCurrency settlementCurrencyRate status test totalUnsettled } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderCapture', $args, ['input' => 'OrderCaptureInput!'], $selection);
    }

    /**
     * Marks an open [`Order`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Order) as closed. A closed order is one where merchants fulfill or cancel all [`LineItem`](https://shopify.dev/docs/ap
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: OrderCloseInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderClose(array $args = [], string $selection = 'order { billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderClose', $args, ['input' => 'OrderCloseInput!'], $selection);
    }

    /**
     * Creates an order with attributes such as customer information, line items, and shipping and billing addresses. Use the `orderCreate` mutation to programmatically generate orders in scenarios where or
     *
     * @param array<string,mixed> $args Variaveis GraphQL: order: OrderCreateOrderInput!, options: OrderCreateOptionsInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderCreate(array $args = [], string $selection = 'order { billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderCreate', $args, ['order' => 'OrderCreateOrderInput!', 'options' => 'OrderCreateOptionsInput'], $selection);
    }

    /**
     * Creates a payment for an [`Order`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Order) using a stored [`PaymentMandate`](https://shopify.dev/docs/api/admin-graphql/latest/objects/PaymentM
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, paymentScheduleId: ID, idempotencyKey: String!, mandateId: ID!, amount: MoneyInput, autoCapture: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderCreateMandatePayment(array $args = [], string $selection = 'job { done id } jobResult { done id status } paymentReferenceId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderCreateMandatePayment', $args, ['id' => 'ID!', 'paymentScheduleId' => 'ID', 'idempotencyKey' => 'String!', 'mandateId' => 'ID!', 'amount' => 'MoneyInput', 'autoCapture' => 'Boolean'], $selection);
    }

    /**
     * Records a manual payment for an [`Order`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Order) that isn't fully paid. Use this mutation to track payments received outside the standard chec
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, amount: MoneyInput, paymentMethodName: String, processedAt: DateTime
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderCreateManualPayment(array $args = [], string $selection = 'order { billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderCreateManualPayment', $args, ['id' => 'ID!', 'amount' => 'MoneyInput', 'paymentMethodName' => 'String', 'processedAt' => 'DateTime'], $selection);
    }

    /**
     * Removes customer from an order.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: orderId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderCustomerRemove(array $args = [], string $selection = 'order { billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderCustomerRemove', $args, ['orderId' => 'ID!'], $selection);
    }

    /**
     * Sets a customer on an order.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: orderId: ID!, customerId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderCustomerSet(array $args = [], string $selection = 'order { billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderCustomerSet', $args, ['orderId' => 'ID!', 'customerId' => 'ID!'], $selection);
    }

    /**
     * Permanently deletes an [`Order`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Order) from the store. You can only delete [specific order types](https://help.shopify.com/manual/orders/can
     *
     * @param array<string,mixed> $args Variaveis GraphQL: orderId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderDelete', $args, ['orderId' => 'ID!'], $selection);
    }

    /**
     * Adds a custom line item to an existing [`Order`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Order). Custom line items represent products or services not in your catalog, such as gift wr
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, title: String!, locationId: ID, price: MoneyInput!, quantity: Int!, taxable: Boolean, requiresShipping: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderEditAddCustomItem(array $args = [], string $selection = 'calculatedLineItem { editableQuantity editableQuantityBeforeChanges hasStagedLineItemDiscount id quantity restockable restocking sku title variantTitle } calculatedOrder { committed id notificationPreviewHtml notificationPreviewTitle subtotalLineItemsQuantity } orderEditSession { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderEditAddCustomItem', $args, ['id' => 'ID!', 'title' => 'String!', 'locationId' => 'ID', 'price' => 'MoneyInput!', 'quantity' => 'Int!', 'taxable' => 'Boolean', 'requiresShipping' => 'Boolean'], $selection);
    }

    /**
     * Applies a discount to a [`LineItem`](https://shopify.dev/docs/api/admin-graphql/latest/objects/LineItem) during an order edit session. The discount can be either a fixed amount or percentage value. T
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, lineItemId: ID!, discount: OrderEditAppliedDiscountInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderEditAddLineItemDiscount(array $args = [], string $selection = 'addedDiscountStagedChange { description id } calculatedLineItem { editableQuantity editableQuantityBeforeChanges hasStagedLineItemDiscount id quantity restockable restocking sku title variantTitle } calculatedOrder { committed id notificationPreviewHtml notificationPreviewTitle subtotalLineItemsQuantity } orderEditSession { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderEditAddLineItemDiscount', $args, ['id' => 'ID!', 'lineItemId' => 'ID!', 'discount' => 'OrderEditAppliedDiscountInput!'], $selection);
    }

    /**
     * Adds a custom shipping line to an [`Order`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Order) during an edit session. Specify the shipping title and price to create a new [`ShippingLine
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, shippingLine: OrderEditAddShippingLineInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderEditAddShippingLine(array $args = [], string $selection = 'calculatedOrder { committed id notificationPreviewHtml notificationPreviewTitle subtotalLineItemsQuantity } calculatedShippingLine { id stagedStatus title } orderEditSession { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderEditAddShippingLine', $args, ['id' => 'ID!', 'shippingLine' => 'OrderEditAddShippingLineInput!'], $selection);
    }

    /**
     * Adds a [`ProductVariant`](https://shopify.dev/docs/api/admin-graphql/latest/objects/ProductVariant) as a line item to an [`Order`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Order) that
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, variantId: ID!, locationId: ID, quantity: Int!, allowDuplicates: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderEditAddVariant(array $args = [], string $selection = 'calculatedLineItem { editableQuantity editableQuantityBeforeChanges hasStagedLineItemDiscount id quantity restockable restocking sku title variantTitle } calculatedOrder { committed id notificationPreviewHtml notificationPreviewTitle subtotalLineItemsQuantity } orderEditSession { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderEditAddVariant', $args, ['id' => 'ID!', 'variantId' => 'ID!', 'locationId' => 'ID', 'quantity' => 'Int!', 'allowDuplicates' => 'Boolean'], $selection);
    }

    /**
     * Starts an order editing session that enables you to modify an existing [`Order`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Order). This mutation creates an [`OrderEditSession`](https:/
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderEditBegin(array $args = [], string $selection = 'calculatedOrder { committed id notificationPreviewHtml notificationPreviewTitle subtotalLineItemsQuantity } orderEditSession { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderEditBegin', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Applies staged changes from an order editing session to the original order. This finalizes all modifications made during the edit session, including changes to line items, quantities, discounts, and s
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, notifyCustomer: Boolean, staffNote: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderEditCommit(array $args = [], string $selection = 'order { billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt } successMessages userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderEditCommit', $args, ['id' => 'ID!', 'notifyCustomer' => 'Boolean', 'staffNote' => 'String'], $selection);
    }

    /**
     * Removes a discount on the current order edit. For more information on how to use the GraphQL Admin API to edit an existing order, refer to [Edit existing orders](https://shopify.dev/apps/fulfillment/o
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, discountApplicationId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderEditRemoveDiscount(array $args = [], string $selection = 'calculatedOrder { committed id notificationPreviewHtml notificationPreviewTitle subtotalLineItemsQuantity } orderEditSession { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderEditRemoveDiscount', $args, ['id' => 'ID!', 'discountApplicationId' => 'ID!'], $selection);
    }

    /**
     * Removes a line item discount that was applied as part of an order edit.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, discountApplicationId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderEditRemoveLineItemDiscount(array $args = [], string $selection = 'calculatedLineItem { editableQuantity editableQuantityBeforeChanges hasStagedLineItemDiscount id quantity restockable restocking sku title variantTitle } calculatedOrder { committed id notificationPreviewHtml notificationPreviewTitle subtotalLineItemsQuantity } orderEditSession { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderEditRemoveLineItemDiscount', $args, ['id' => 'ID!', 'discountApplicationId' => 'ID!'], $selection);
    }

    /**
     * Removes a shipping line from an existing order. For more information on how to use the GraphQL Admin API to edit an existing order, refer to [Edit existing orders](https://shopify.dev/apps/fulfillment
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, shippingLineId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderEditRemoveShippingLine(array $args = [], string $selection = 'calculatedOrder { committed id notificationPreviewHtml notificationPreviewTitle subtotalLineItemsQuantity } orderEditSession { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderEditRemoveShippingLine', $args, ['id' => 'ID!', 'shippingLineId' => 'ID!'], $selection);
    }

    /**
     * Sets the quantity of a line item on an order that's being edited. Use this mutation to increase, decrease, or remove items by adjusting their quantities. Setting the quantity to zero effectively remo
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, lineItemId: ID!, quantity: Int!, restock: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderEditSetQuantity(array $args = [], string $selection = 'calculatedLineItem { editableQuantity editableQuantityBeforeChanges hasStagedLineItemDiscount id quantity restockable restocking sku title variantTitle } calculatedOrder { committed id notificationPreviewHtml notificationPreviewTitle subtotalLineItemsQuantity } orderEditSession { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderEditSetQuantity', $args, ['id' => 'ID!', 'lineItemId' => 'ID!', 'quantity' => 'Int!', 'restock' => 'Boolean'], $selection);
    }

    /**
     * Updates a manual line level discount on the current order edit. For more information on how to use the GraphQL Admin API to edit an existing order, refer to [Edit existing orders](https://shopify.dev/
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, discount: OrderEditAppliedDiscountInput!, discountApplicationId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderEditUpdateDiscount(array $args = [], string $selection = 'calculatedOrder { committed id notificationPreviewHtml notificationPreviewTitle subtotalLineItemsQuantity } orderEditSession { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderEditUpdateDiscount', $args, ['id' => 'ID!', 'discount' => 'OrderEditAppliedDiscountInput!', 'discountApplicationId' => 'ID!'], $selection);
    }

    /**
     * Updates a shipping line on the current order edit. For more information on how to use the GraphQL Admin API to edit an existing order, refer to [Edit existing orders](https://shopify.dev/apps/fulfillm
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, shippingLine: OrderEditUpdateShippingLineInput!, shippingLineId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderEditUpdateShippingLine(array $args = [], string $selection = 'calculatedOrder { committed id notificationPreviewHtml notificationPreviewTitle subtotalLineItemsQuantity } orderEditSession { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderEditUpdateShippingLine', $args, ['id' => 'ID!', 'shippingLine' => 'OrderEditUpdateShippingLineInput!', 'shippingLineId' => 'ID!'], $selection);
    }

    /**
     * Sends an email invoice for an [`Order`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Order). You can customize the email recipient, sender, and subject line using the [`email`](https://s
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, email: EmailInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderInvoiceSend(array $args = [], string $selection = 'order { billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderInvoiceSend', $args, ['id' => 'ID!', 'email' => 'EmailInput'], $selection);
    }

    /**
     * Marks an order as paid by recording a payment transaction for the outstanding amount. Use the `orderMarkAsPaid` mutation to record payments received outside the standard checkout process. The `orderM
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: OrderMarkAsPaidInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderMarkAsPaid(array $args = [], string $selection = 'order { billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderMarkAsPaid', $args, ['input' => 'OrderMarkAsPaidInput!'], $selection);
    }

    /**
     * Opens a closed order.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: OrderOpenInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderOpen(array $args = [], string $selection = 'order { billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderOpen', $args, ['input' => 'OrderOpenInput!'], $selection);
    }

    /**
     * Creates a risk assessment for a specific order using the provided risk level and facts. Shopify sends an `orders/risk_assessment_changed` webhook when the assessment is created.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: orderRiskAssessmentInput: OrderRiskAssessmentCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderRiskAssessmentCreate(array $args = [], string $selection = 'orderRiskAssessment { riskLevel } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderRiskAssessmentCreate', $args, ['orderRiskAssessmentInput' => 'OrderRiskAssessmentCreateInput!'], $selection);
    }

    /**
     * Updates the attributes of an order, such as the customer's email, the shipping address for the order, tags, and [metafields](https://shopify.dev/docs/apps/build/custom-data) associated with the order.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: OrderInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function orderUpdate(array $args = [], string $selection = 'order { billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'orderUpdate', $args, ['input' => 'OrderInput!'], $selection);
    }
}

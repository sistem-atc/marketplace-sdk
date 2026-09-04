<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Fulfillment.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class FulfillmentMutations extends BaseOperations
{
    /**
     * Cancels an existing [`Fulfillment`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Fulfillment) and reverses its effects on associated [`FulfillmentOrder`](https://shopify.dev/docs/api/admi
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentCancel(array $args = [], string $selection = 'fulfillment { createdAt deliveredAt displayStatus estimatedDeliveryAt id inTransitAt legacyResourceId name requiresShipping status totalQuantity updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentCancel', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Creates a fulfillment constraint rule and its metafield.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: functionHandle: String, deliveryMethodTypes: [DeliveryMethodType!]!, metafields: [MetafieldInput!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentConstraintRuleCreate(array $args = [], string $selection = 'fulfillmentConstraintRule { deliveryMethodTypes id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentConstraintRuleCreate', $args, ['functionHandle' => 'String', 'deliveryMethodTypes' => '[DeliveryMethodType!]!', 'metafields' => '[MetafieldInput!]'], $selection);
    }

    /**
     * Deletes a fulfillment constraint rule and its metafields.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentConstraintRuleDelete(array $args = [], string $selection = 'success userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentConstraintRuleDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Update a fulfillment constraint rule.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, deliveryMethodTypes: [DeliveryMethodType!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentConstraintRuleUpdate(array $args = [], string $selection = 'fulfillmentConstraintRule { deliveryMethodTypes id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentConstraintRuleUpdate', $args, ['id' => 'ID!', 'deliveryMethodTypes' => '[DeliveryMethodType!]!'], $selection);
    }

    /**
     * Creates a fulfillment for one or more [`FulfillmentOrder`](https://shopify.dev/docs/api/admin-graphql/latest/objects/FulfillmentOrder) objects. The fulfillment orders are associated with the same [`Or
     *
     * @param array<string,mixed> $args Variaveis GraphQL: fulfillment: FulfillmentInput!, message: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentCreate(array $args = [], string $selection = 'fulfillment { createdAt deliveredAt displayStatus estimatedDeliveryAt id inTransitAt legacyResourceId name requiresShipping status totalQuantity updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentCreate', $args, ['fulfillment' => 'FulfillmentInput!', 'message' => 'String'], $selection);
    }

    /**
     * Creates a fulfillment for one or many fulfillment orders. The fulfillment orders are associated with the same order and are assigned to the same location.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: fulfillment: FulfillmentV2Input!, message: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentCreateV2(array $args = [], string $selection = 'fulfillment { createdAt deliveredAt displayStatus estimatedDeliveryAt id inTransitAt legacyResourceId name requiresShipping status totalQuantity updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentCreateV2', $args, ['fulfillment' => 'FulfillmentV2Input!', 'message' => 'String'], $selection);
    }

    /**
     * Creates a [`FulfillmentEvent`](https://shopify.dev/docs/api/admin-graphql/latest/objects/FulfillmentEvent) to track the shipment status and location of items that have shipped. Events capture status u
     *
     * @param array<string,mixed> $args Variaveis GraphQL: fulfillmentEvent: FulfillmentEventInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentEventCreate(array $args = [], string $selection = 'fulfillmentEvent { address1 city country createdAt estimatedDeliveryAt happenedAt id latitude longitude message province status zip } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentEventCreate', $args, ['fulfillmentEvent' => 'FulfillmentEventInput!'], $selection);
    }

    /**
     * Accept a cancellation request sent to a fulfillment service for a fulfillment order.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, message: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderAcceptCancellationRequest(array $args = [], string $selection = 'fulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderAcceptCancellationRequest', $args, ['id' => 'ID!', 'message' => 'String'], $selection);
    }

    /**
     * Accepts a fulfillment request that the fulfillment service has received for a [`FulfillmentOrder`](https://shopify.dev/docs/api/admin-graphql/latest/objects/FulfillmentOrder) which signals that the fu
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, message: String, estimatedShippedAt: DateTime
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderAcceptFulfillmentRequest(array $args = [], string $selection = 'fulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderAcceptFulfillmentRequest', $args, ['id' => 'ID!', 'message' => 'String', 'estimatedShippedAt' => 'DateTime'], $selection);
    }

    /**
     * Cancels a [`FulfillmentOrder`](https://shopify.dev/docs/api/admin-graphql/latest/objects/FulfillmentOrder) and creates a replacement fulfillment order to represent the work left to be done. The origin
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderCancel(array $args = [], string $selection = 'fulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } replacementFulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderCancel', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Marks an in-progress fulfillment order as incomplete, indicating the fulfillment service is unable to ship any remaining items, and closes the fulfillment request. This mutation can only be called fo
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, message: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderClose(array $args = [], string $selection = 'fulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderClose', $args, ['id' => 'ID!', 'message' => 'String'], $selection);
    }

    /**
     * Applies a fulfillment hold on a fulfillment order. As of the [2025-01 API version](https://shopify.dev/changelog/apply-multiple-holds-to-a-single-fulfillment-order), the mutation can be successfully
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, fulfillmentHold: FulfillmentOrderHoldInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderHold(array $args = [], string $selection = 'fulfillmentHold { displayReason handle heldByRequestingApp id reason reasonNotes } fulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } remainingFulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderHold', $args, ['id' => 'ID!', 'fulfillmentHold' => 'FulfillmentOrderHoldInput!'], $selection);
    }

    /**
     * Marks [fulfillment order line items](https://shopify.dev/docs/api/admin-graphql/latest/objects/FulfillmentOrderLineItem) as ready for customer pickup. When executed, this mutation automatically sends
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: FulfillmentOrderLineItemsPreparedForPickupInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderLineItemsPreparedForPickup(array $args = [], string $selection = 'userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderLineItemsPreparedForPickup', $args, ['input' => 'FulfillmentOrderLineItemsPreparedForPickupInput!'], $selection);
    }

    /**
     * Merges a set or multiple sets of fulfillment orders together into one based on line item inputs and quantities.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: fulfillmentOrderMergeInputs: [FulfillmentOrderMergeInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderMerge(array $args = [], string $selection = 'userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderMerge', $args, ['fulfillmentOrderMergeInputs' => '[FulfillmentOrderMergeInput!]!'], $selection);
    }

    /**
     * Changes the location which is assigned to fulfill a number of unfulfilled fulfillment order line items. Moving a fulfillment order will fail in the following circumstances: * The fulfillment order i
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, newLocationId: ID!, fulfillmentOrderLineItems: [FulfillmentOrderLineItemInput!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderMove(array $args = [], string $selection = 'movedFulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } originalFulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } remainingFulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderMove', $args, ['id' => 'ID!', 'newLocationId' => 'ID!', 'fulfillmentOrderLineItems' => '[FulfillmentOrderLineItemInput!]'], $selection);
    }

    /**
     * Marks a scheduled fulfillment order as open. From API version 2026-01, this will also mark a fulfillment order as open when it is assigned to a merchant managed location and has had progress reported
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderOpen(array $args = [], string $selection = 'fulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderOpen', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Rejects a cancellation request sent to a fulfillment service for a fulfillment order.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, message: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderRejectCancellationRequest(array $args = [], string $selection = 'fulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderRejectCancellationRequest', $args, ['id' => 'ID!', 'message' => 'String'], $selection);
    }

    /**
     * Rejects a fulfillment request sent to a fulfillment service for a fulfillment order.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, reason: FulfillmentOrderRejectionReason, message: String, lineItems: [IncomingRequestLineItemInput!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderRejectFulfillmentRequest(array $args = [], string $selection = 'fulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderRejectFulfillmentRequest', $args, ['id' => 'ID!', 'reason' => 'FulfillmentOrderRejectionReason', 'message' => 'String', 'lineItems' => '[IncomingRequestLineItemInput!]'], $selection);
    }

    /**
     * Releases the fulfillment hold on a fulfillment order.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, holdIds: [ID!], externalId: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderReleaseHold(array $args = [], string $selection = 'fulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderReleaseHold', $args, ['id' => 'ID!', 'holdIds' => '[ID!]', 'externalId' => 'String'], $selection);
    }

    /**
     * Reports the progress of an open or in-progress fulfillment order.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, progressReport: FulfillmentOrderReportProgressInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderReportProgress(array $args = [], string $selection = 'fulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderReportProgress', $args, ['id' => 'ID!', 'progressReport' => 'FulfillmentOrderReportProgressInput'], $selection);
    }

    /**
     * Reschedules a scheduled fulfillment order. Updates the value of the `fulfillAt` field on a scheduled fulfillment order. The fulfillment order will be marked as ready for fulfillment at this date and
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, fulfillAt: DateTime!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderReschedule(array $args = [], string $selection = 'fulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderReschedule', $args, ['id' => 'ID!', 'fulfillAt' => 'DateTime!'], $selection);
    }

    /**
     * Splits [`FulfillmentOrder`](https://shopify.dev/docs/api/admin-graphql/latest/objects/FulfillmentOrder) objects by moving the specified [`LineItem`](https://shopify.dev/docs/api/admin-graphql/latest/o
     *
     * @param array<string,mixed> $args Variaveis GraphQL: fulfillmentOrderSplits: [FulfillmentOrderSplitInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderSplit(array $args = [], string $selection = 'userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderSplit', $args, ['fulfillmentOrderSplits' => '[FulfillmentOrderSplitInput!]!'], $selection);
    }

    /**
     * Sends a cancellation request to the fulfillment service of a fulfillment order.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, message: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderSubmitCancellationRequest(array $args = [], string $selection = 'fulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderSubmitCancellationRequest', $args, ['id' => 'ID!', 'message' => 'String'], $selection);
    }

    /**
     * Sends a fulfillment request to the fulfillment service assigned to a [`FulfillmentOrder`](https://shopify.dev/docs/api/admin-graphql/latest/objects/FulfillmentOrder). The fulfillment service must then
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, message: String, notifyCustomer: Boolean, fulfillmentOrderLineItems: [FulfillmentOrderLineItemInput!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrderSubmitFulfillmentRequest(array $args = [], string $selection = 'originalFulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } submittedFulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } unsubmittedFulfillmentOrder { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrderSubmitFulfillmentRequest', $args, ['id' => 'ID!', 'message' => 'String', 'notifyCustomer' => 'Boolean', 'fulfillmentOrderLineItems' => '[FulfillmentOrderLineItemInput!]'], $selection);
    }

    /**
     * Route the fulfillment orders to an alternative location, according to the shop's order routing settings. This involves: * Finding an alternate location that can fulfill the fulfillment orders. * Assig
     *
     * @param array<string,mixed> $args Variaveis GraphQL: fulfillmentOrderIds: [ID!]!, includedLocationIds: [ID!], excludedLocationIds: [ID!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrdersReroute(array $args = [], string $selection = 'movedFulfillmentOrders { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrdersReroute', $args, ['fulfillmentOrderIds' => '[ID!]!', 'includedLocationIds' => '[ID!]', 'excludedLocationIds' => '[ID!]'], $selection);
    }

    /**
     * Sets the latest date and time by which the fulfillment orders need to be fulfilled.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: fulfillmentOrderIds: [ID!]!, fulfillmentDeadline: DateTime!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrdersSetFulfillmentDeadline(array $args = [], string $selection = 'success userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentOrdersSetFulfillmentDeadline', $args, ['fulfillmentOrderIds' => '[ID!]!', 'fulfillmentDeadline' => 'DateTime!'], $selection);
    }

    /**
     * Creates a fulfillment service. ## Fulfillment service location When creating a fulfillment service, a new location will be automatically created on the shop and will be associated with this fulfillm
     *
     * @param array<string,mixed> $args Variaveis GraphQL: name: String!, callbackUrl: URL, trackingSupport: Boolean, inventoryManagement: Boolean, requiresShippingMethod: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentServiceCreate(array $args = [], string $selection = 'fulfillmentService { callbackUrl fulfillmentOrdersOptIn handle id inventoryManagement requiresShippingMethod serviceName trackingSupport type } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentServiceCreate', $args, ['name' => 'String!', 'callbackUrl' => 'URL', 'trackingSupport' => 'Boolean', 'inventoryManagement' => 'Boolean', 'requiresShippingMethod' => 'Boolean'], $selection);
    }

    /**
     * Deletes a fulfillment service.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, destinationLocationId: ID, inventoryAction: FulfillmentServiceDeleteInventoryAction
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentServiceDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentServiceDelete', $args, ['id' => 'ID!', 'destinationLocationId' => 'ID', 'inventoryAction' => 'FulfillmentServiceDeleteInventoryAction'], $selection);
    }

    /**
     * Updates the [`FulfillmentService`](https://shopify.dev/docs/api/admin-graphql/latest/objects/FulfillmentService) configuration, including its name, callback URL, and operational settings. The mutatio
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, name: String, callbackUrl: URL, trackingSupport: Boolean, inventoryManagement: Boolean, requiresShippingMethod: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentServiceUpdate(array $args = [], string $selection = 'fulfillmentService { callbackUrl fulfillmentOrdersOptIn handle id inventoryManagement requiresShippingMethod serviceName trackingSupport type } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentServiceUpdate', $args, ['id' => 'ID!', 'name' => 'String', 'callbackUrl' => 'URL', 'trackingSupport' => 'Boolean', 'inventoryManagement' => 'Boolean', 'requiresShippingMethod' => 'Boolean'], $selection);
    }

    /**
     * Updates tracking information for a fulfillment, including the carrier name, tracking numbers, and tracking URLs. You can provide either single or multiple tracking numbers for shipments with multiple
     *
     * @param array<string,mixed> $args Variaveis GraphQL: fulfillmentId: ID!, trackingInfoInput: FulfillmentTrackingInput!, notifyCustomer: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentTrackingInfoUpdate(array $args = [], string $selection = 'fulfillment { createdAt deliveredAt displayStatus estimatedDeliveryAt id inTransitAt legacyResourceId name requiresShipping status totalQuantity updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentTrackingInfoUpdate', $args, ['fulfillmentId' => 'ID!', 'trackingInfoInput' => 'FulfillmentTrackingInput!', 'notifyCustomer' => 'Boolean'], $selection);
    }

    /**
     * Updates tracking information for a fulfillment.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: fulfillmentId: ID!, trackingInfoInput: FulfillmentTrackingInput!, notifyCustomer: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentTrackingInfoUpdateV2(array $args = [], string $selection = 'fulfillment { createdAt deliveredAt displayStatus estimatedDeliveryAt id inTransitAt legacyResourceId name requiresShipping status totalQuantity updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fulfillmentTrackingInfoUpdateV2', $args, ['fulfillmentId' => 'ID!', 'trackingInfoInput' => 'FulfillmentTrackingInput!', 'notifyCustomer' => 'Boolean'], $selection);
    }
}

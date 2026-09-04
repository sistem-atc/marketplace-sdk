<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Inventory.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class InventoryMutations extends BaseOperations
{
    /**
     * Activates an inventory item at a [`Location`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Location) by creating an [`InventoryLevel`](https://shopify.dev/docs/api/admin-graphql/latest/ob
     *
     * @param array<string,mixed> $args Variaveis GraphQL: inventoryItemId: ID!, locationId: ID!, available: Int, onHand: Int, stockAtLegacyLocation: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryActivate(array $args = [], string $selection = 'inventoryLevel { canDeactivate createdAt deactivationAlert id isActive updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryActivate', $args, ['inventoryItemId' => 'ID!', 'locationId' => 'ID!', 'available' => 'Int', 'onHand' => 'Int', 'stockAtLegacyLocation' => 'Boolean'], $selection);
    }

    /**
     * Adjusts quantities for inventory items by applying incremental changes at specific locations. Each adjustment modifies the quantity by a delta value rather than setting an absolute amount. The mutati
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: InventoryAdjustQuantitiesInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryAdjustQuantities(array $args = [], string $selection = 'inventoryAdjustmentGroup { createdAt id reason referenceDocumentUri } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryAdjustQuantities', $args, ['input' => 'InventoryAdjustQuantitiesInput!'], $selection);
    }

    /**
     * Activates or deactivates an inventory item at multiple locations. When you activate an [`InventoryItem`](https://shopify.dev/docs/api/admin-graphql/latest/objects/InventoryItem) at a [`Location`](http
     *
     * @param array<string,mixed> $args Variaveis GraphQL: inventoryItemId: ID!, inventoryItemUpdates: [InventoryBulkToggleActivationInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryBulkToggleActivation(array $args = [], string $selection = 'inventoryItem { countryCodeOfOrigin createdAt duplicateSkuCount harmonizedSystemCode id inventoryHistoryUrl legacyResourceId provinceCodeOfOrigin requiresShipping sku tracked updatedAt } inventoryLevels { canDeactivate createdAt deactivationAlert id isActive updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryBulkToggleActivation', $args, ['inventoryItemId' => 'ID!', 'inventoryItemUpdates' => '[InventoryBulkToggleActivationInput!]!'], $selection);
    }

    /**
     * Removes an inventory item's quantities from a location, and turns off inventory at the location.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: inventoryLevelId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryDeactivate(array $args = [], string $selection = 'userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryDeactivate', $args, ['inventoryLevelId' => 'ID!'], $selection);
    }

    /**
     * Updates an [`InventoryItem`](https://shopify.dev/docs/api/admin-graphql/latest/objects/InventoryItem)'s properties including whether inventory is tracked, cost, SKU, and whether shipping is required.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: InventoryItemInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryItemUpdate(array $args = [], string $selection = 'inventoryItem { countryCodeOfOrigin createdAt duplicateSkuCount harmonizedSystemCode id inventoryHistoryUrl legacyResourceId provinceCodeOfOrigin requiresShipping sku tracked updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryItemUpdate', $args, ['id' => 'ID!', 'input' => 'InventoryItemInput!'], $selection);
    }

    /**
     * Moves inventory quantities for a single inventory item between different states at a single location. Use this mutation to reallocate inventory across quantity states without moving it between locatio
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: InventoryMoveQuantitiesInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryMoveQuantities(array $args = [], string $selection = 'inventoryAdjustmentGroup { createdAt id reason referenceDocumentUri } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryMoveQuantities', $args, ['input' => 'InventoryMoveQuantitiesInput!'], $selection);
    }

    /**
     * Sets an inventory item's on-hand quantities to specific absolute values at designated locations. The mutation takes a reason for tracking purposes and a reference document URI for audit trails. Retur
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: input: InventorySetOnHandQuantitiesInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventorySetOnHandQuantities(array $args = [], string $selection = 'inventoryAdjustmentGroup { createdAt id reason referenceDocumentUri } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventorySetOnHandQuantities', $args, ['input' => 'InventorySetOnHandQuantitiesInput!'], $selection);
    }

    /**
     * Set quantities of specified name using absolute values. This mutation supports compare-and-set functionality to handle concurrent requests properly. If `ignoreCompareQuantity` is not set to true, the
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: InventorySetQuantitiesInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventorySetQuantities(array $args = [], string $selection = 'inventoryAdjustmentGroup { createdAt id reason referenceDocumentUri } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventorySetQuantities', $args, ['input' => 'InventorySetQuantitiesInput!'], $selection);
    }

    /**
     * Adds items to an inventory shipment. > Caution: > As of 2026-01, this mutation supports an optional idempotency key using the `@idempotent` directive. > As of 2026-04, the idempotency key is required
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, lineItems: [InventoryShipmentLineItemInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryShipmentAddItems(array $args = [], string $selection = 'addedItems { acceptedQuantity id quantity rejectedQuantity unreceivedQuantity } inventoryShipment { barcode dateCreated dateReceived dateShipped id lineItemTotalQuantity name status totalAcceptedQuantity totalReceivedQuantity totalRejectedQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryShipmentAddItems', $args, ['id' => 'ID!', 'lineItems' => '[InventoryShipmentLineItemInput!]!'], $selection);
    }

    /**
     * Adds a draft shipment to an inventory transfer. > Caution: > As of 2026-01, this mutation supports an optional idempotency key using the `@idempotent` directive. > As of 2026-04, the idempotency key
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: InventoryShipmentCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryShipmentCreate(array $args = [], string $selection = 'inventoryShipment { barcode dateCreated dateReceived dateShipped id lineItemTotalQuantity name status totalAcceptedQuantity totalReceivedQuantity totalRejectedQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryShipmentCreate', $args, ['input' => 'InventoryShipmentCreateInput!'], $selection);
    }

    /**
     * Adds an in-transit shipment to an inventory transfer. > Caution: > As of 2026-01, this mutation supports an optional idempotency key using the `@idempotent` directive. > As of 2026-04, the idempotenc
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: InventoryShipmentCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryShipmentCreateInTransit(array $args = [], string $selection = 'inventoryShipment { barcode dateCreated dateReceived dateShipped id lineItemTotalQuantity name status totalAcceptedQuantity totalReceivedQuantity totalRejectedQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryShipmentCreateInTransit', $args, ['input' => 'InventoryShipmentCreateInput!'], $selection);
    }

    /**
     * Deletes an inventory shipment. Only draft shipments can be deleted.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryShipmentDelete(array $args = [], string $selection = 'id userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryShipmentDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Marks a draft inventory shipment as in transit.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, dateShipped: DateTime
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryShipmentMarkInTransit(array $args = [], string $selection = 'inventoryShipment { barcode dateCreated dateReceived dateShipped id lineItemTotalQuantity name status totalAcceptedQuantity totalReceivedQuantity totalRejectedQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryShipmentMarkInTransit', $args, ['id' => 'ID!', 'dateShipped' => 'DateTime'], $selection);
    }

    /**
     * Receive an inventory shipment. > Caution: > As of 2026-01, this mutation supports an optional idempotency key using the `@idempotent` directive. > As of 2026-04, the idempotency key is required and m
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, lineItems: [InventoryShipmentReceiveItemInput!], dateReceived: DateTime, bulkReceiveAction: InventoryShipmentReceiveLineItemReason
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryShipmentReceive(array $args = [], string $selection = 'inventoryShipment { barcode dateCreated dateReceived dateShipped id lineItemTotalQuantity name status totalAcceptedQuantity totalReceivedQuantity totalRejectedQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryShipmentReceive', $args, ['id' => 'ID!', 'lineItems' => '[InventoryShipmentReceiveItemInput!]', 'dateReceived' => 'DateTime', 'bulkReceiveAction' => 'InventoryShipmentReceiveLineItemReason'], $selection);
    }

    /**
     * Remove items from an inventory shipment.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, lineItems: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryShipmentRemoveItems(array $args = [], string $selection = 'inventoryShipment { barcode dateCreated dateReceived dateShipped id lineItemTotalQuantity name status totalAcceptedQuantity totalReceivedQuantity totalRejectedQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryShipmentRemoveItems', $args, ['id' => 'ID!', 'lineItems' => '[ID!]!'], $selection);
    }

    /**
     * Sets the barcode on an inventory shipment.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, barcode: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryShipmentSetBarcode(array $args = [], string $selection = 'inventoryShipment { barcode dateCreated dateReceived dateShipped id lineItemTotalQuantity name status totalAcceptedQuantity totalReceivedQuantity totalRejectedQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryShipmentSetBarcode', $args, ['id' => 'ID!', 'barcode' => 'String!'], $selection);
    }

    /**
     * Edits the tracking info on an inventory shipment.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, tracking: InventoryShipmentTrackingInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryShipmentSetTracking(array $args = [], string $selection = 'inventoryShipment { barcode dateCreated dateReceived dateShipped id lineItemTotalQuantity name status totalAcceptedQuantity totalReceivedQuantity totalRejectedQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryShipmentSetTracking', $args, ['id' => 'ID!', 'tracking' => 'InventoryShipmentTrackingInput!'], $selection);
    }

    /**
     * Updates items on an inventory shipment.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, items: [InventoryShipmentUpdateItemQuantitiesInput!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryShipmentUpdateItemQuantities(array $args = [], string $selection = 'shipment { barcode dateCreated dateReceived dateShipped id lineItemTotalQuantity name status totalAcceptedQuantity totalReceivedQuantity totalRejectedQuantity } updatedLineItems { acceptedQuantity id quantity rejectedQuantity unreceivedQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryShipmentUpdateItemQuantities', $args, ['id' => 'ID!', 'items' => '[InventoryShipmentUpdateItemQuantitiesInput!]'], $selection);
    }

    /**
     * Cancels an inventory transfer.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryTransferCancel(array $args = [], string $selection = 'inventoryTransfer { dateCreated hasTimelineComment id name note receivedQuantity referenceName status tags totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryTransferCancel', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Creates a draft inventory transfer to move inventory items between [`Location`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Location) objects in your store. The transfer tracks which ite
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: InventoryTransferCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryTransferCreate(array $args = [], string $selection = 'inventoryTransfer { dateCreated hasTimelineComment id name note receivedQuantity referenceName status tags totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryTransferCreate', $args, ['input' => 'InventoryTransferCreateInput!'], $selection);
    }

    /**
     * Creates an inventory transfer in ready to ship. > Caution: > As of 2026-01, this mutation supports an optional idempotency key using the `@idempotent` directive. > As of 2026-04, the idempotency key
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: InventoryTransferCreateAsReadyToShipInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryTransferCreateAsReadyToShip(array $args = [], string $selection = 'inventoryTransfer { dateCreated hasTimelineComment id name note receivedQuantity referenceName status tags totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryTransferCreateAsReadyToShip', $args, ['input' => 'InventoryTransferCreateAsReadyToShipInput!'], $selection);
    }

    /**
     * Deletes an inventory transfer.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryTransferDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryTransferDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * This mutation allows duplicating an existing inventory transfer. The duplicated transfer will have the same line items and quantities as the original transfer, but will be in a draft state with no shi
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryTransferDuplicate(array $args = [], string $selection = 'inventoryTransfer { dateCreated hasTimelineComment id name note receivedQuantity referenceName status tags totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryTransferDuplicate', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Edits an inventory transfer.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: InventoryTransferEditInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryTransferEdit(array $args = [], string $selection = 'inventoryTransfer { dateCreated hasTimelineComment id name note receivedQuantity referenceName status tags totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryTransferEdit', $args, ['id' => 'ID!', 'input' => 'InventoryTransferEditInput!'], $selection);
    }

    /**
     * Sets an inventory transfer to ready to ship.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryTransferMarkAsReadyToShip(array $args = [], string $selection = 'inventoryTransfer { dateCreated hasTimelineComment id name note receivedQuantity referenceName status tags totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryTransferMarkAsReadyToShip', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * This mutation removes [`InventoryTransferLineItem`s](https://shopify.dev/docs/api/admin-graphql/latest/objects/InventoryTransferLineItem), or portions of them, from a `DRAFT` or `READY_TO_SHIP` Transf
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: InventoryTransferRemoveItemsInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryTransferRemoveItems(array $args = [], string $selection = 'inventoryTransfer { dateCreated hasTimelineComment id name note receivedQuantity referenceName status tags totalQuantity } removedQuantities { deltaQuantity inventoryItemId newQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryTransferRemoveItems', $args, ['input' => 'InventoryTransferRemoveItemsInput!'], $selection);
    }

    /**
     * This mutation sets the quantity for one or more line items on a Transfer. Only the items you include in the `lineItems` field are updated. Items already on the transfer but not referenced in your upd
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: InventoryTransferSetItemsInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryTransferSetItems(array $args = [], string $selection = 'inventoryTransfer { dateCreated hasTimelineComment id name note receivedQuantity referenceName status tags totalQuantity } updatedLineItems { deltaQuantity inventoryItemId newQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'inventoryTransferSetItems', $args, ['input' => 'InventoryTransferSetItemsInput!'], $selection);
    }
}

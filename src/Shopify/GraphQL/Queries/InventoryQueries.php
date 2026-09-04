<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Inventory.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class InventoryQueries extends BaseOperations
{
    /**
     * Returns an [InventoryItem](https://shopify.dev/docs/api/admin-graphql/latest/objects/InventoryItem) object by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryItem(array $args = [], string $selection = 'countryCodeOfOrigin createdAt duplicateSkuCount harmonizedSystemCode id inventoryHistoryUrl legacyResourceId provinceCodeOfOrigin requiresShipping sku tracked updatedAt'): array
    {
        return $this->execute('query', 'inventoryItem', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a list of inventory items.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryItems(array $args = [], string $selection = 'edges { node { countryCodeOfOrigin createdAt duplicateSkuCount harmonizedSystemCode id inventoryHistoryUrl legacyResourceId provinceCodeOfOrigin requiresShipping sku tracked updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'inventoryItems', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'query' => 'String'], $selection);
    }

    /**
     * Returns an [InventoryLevel](https://shopify.dev/docs/api/admin-graphql/latest/objects/InventoryLevel) object by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryLevel(array $args = [], string $selection = 'canDeactivate createdAt deactivationAlert id isActive updatedAt'): array
    {
        return $this->execute('query', 'inventoryLevel', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns the shop's inventory configuration, including all inventory quantity names. Quantity names represent different [inventory states](https://shopify.dev/docs/apps/build/orders-fulfillment/invento
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryProperties(array $args = [], string $selection = '__typename'): array
    {
        return $this->execute('query', 'inventoryProperties', $args, [], $selection);
    }

    /**
     * Retrieves an [`InventoryShipment`](https://shopify.dev/docs/api/admin-graphql/latest/objects/InventoryShipment) by ID. Returns tracking details, [`InventoryShipmentLineItem`](https://shopify.dev/docs/
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryShipment(array $args = [], string $selection = 'barcode dateCreated dateReceived dateShipped id lineItemTotalQuantity name status totalAcceptedQuantity totalReceivedQuantity totalRejectedQuantity'): array
    {
        return $this->execute('query', 'inventoryShipment', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a paginated list of [`InventoryShipment`](https://shopify.dev/docs/api/admin-graphql/latest/objects/InventoryShipment) objects. Supports filtering by barcode (e.g. `barcode:"12345"`), status
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, sortKey: InventoryShipmentSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryShipments(array $args = [], string $selection = 'edges { node { barcode dateCreated dateReceived dateShipped id lineItemTotalQuantity name status totalAcceptedQuantity totalReceivedQuantity totalRejectedQuantity } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'inventoryShipments', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'sortKey' => 'InventoryShipmentSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * Returns an [`InventoryTransfer`](https://shopify.dev/docs/api/admin-graphql/latest/objects/InventoryTransfer) by ID. Inventory transfers track the movement of inventory between locations, including or
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryTransfer(array $args = [], string $selection = 'dateCreated hasTimelineComment id name note receivedQuantity referenceName status tags totalQuantity'): array
    {
        return $this->execute('query', 'inventoryTransfer', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a paginated list of [`InventoryTransfer`](https://shopify.dev/docs/api/admin-graphql/latest/objects/InventoryTransfer) objects between locations. Transfers track the movement of [`InventoryIte
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: TransferSortKeys, query: String, savedSearchId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function inventoryTransfers(array $args = [], string $selection = 'edges { node { dateCreated hasTimelineComment id name note receivedQuantity referenceName status tags totalQuantity } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'inventoryTransfers', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'TransferSortKeys', 'query' => 'String', 'savedSearchId' => 'ID'], $selection);
    }
}

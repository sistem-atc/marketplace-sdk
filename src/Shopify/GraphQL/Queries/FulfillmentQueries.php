<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Fulfillment.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class FulfillmentQueries extends BaseOperations
{
    /**
     * The paginated list of fulfillment orders assigned to the shop locations owned by the app. Assigned fulfillment orders are fulfillment orders that are set to be fulfilled from locations managed by [fu
     *
     * @param array<string,mixed> $args Variaveis GraphQL: assignmentStatus: FulfillmentOrderAssignmentStatus, locationIds: [ID!], first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: FulfillmentOrderSortKeys
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function assignedFulfillmentOrders(array $args = [], string $selection = 'edges { node { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'assignedFulfillmentOrders', $args, ['assignmentStatus' => 'FulfillmentOrderAssignmentStatus', 'locationIds' => '[ID!]', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'FulfillmentOrderSortKeys'], $selection);
    }

    /**
     * Retrieves a [`Fulfillment`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Fulfillment) by its ID. A fulfillment is a record that the merchant has completed their work required for one or m
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillment(array $args = [], string $selection = 'createdAt deliveredAt displayStatus estimatedDeliveryAt id inTransitAt legacyResourceId name requiresShipping status totalQuantity updatedAt'): array
    {
        return $this->execute('query', 'fulfillment', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * The fulfillment constraint rules that belong to a shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentConstraintRules(array $args = [], string $selection = 'deliveryMethodTypes id'): array
    {
        return $this->execute('query', 'fulfillmentConstraintRules', $args, [], $selection);
    }

    /**
     * Returns a `FulfillmentOrder` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrder(array $args = [], string $selection = 'channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt'): array
    {
        return $this->execute('query', 'fulfillmentOrder', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * The paginated list of all fulfillment orders. The returned fulfillment orders are filtered according to the [fulfillment order access scopes](https://shopify.dev/api/admin-graphql/latest/objects/fulfi
     *
     * @param array<string,mixed> $args Variaveis GraphQL: includeClosed: Boolean, first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: FulfillmentOrderSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentOrders(array $args = [], string $selection = 'edges { node { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'fulfillmentOrders', $args, ['includeClosed' => 'Boolean', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'FulfillmentOrderSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * Returns a [`FulfillmentService`](https://shopify.dev/docs/api/admin-graphql/latest/objects/FulfillmentService) by its ID. The service can manage inventory, process fulfillment requests, and provide tr
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fulfillmentService(array $args = [], string $selection = 'callbackUrl fulfillmentOrdersOptIn handle id inventoryManagement requiresShippingMethod serviceName trackingSupport type'): array
    {
        return $this->execute('query', 'fulfillmentService', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a list of fulfillment orders that are on hold.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String, first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function manualHoldsFulfillmentOrders(array $args = [], string $selection = 'edges { node { channelId createdAt fulfillAt fulfillBy id orderId orderName orderProcessedAt requestStatus status updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'manualHoldsFulfillmentOrders', $args, ['query' => 'String', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }
}

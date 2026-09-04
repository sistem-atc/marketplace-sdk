<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Location.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class LocationQueries extends BaseOperations
{
    /**
     * Retrieves a [`Location`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Location) by its ID. Locations are physical places where merchants store inventory, such as warehouses, retail stores
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function location(array $args = [], string $selection = 'activatable addressVerified createdAt deactivatable deactivatedAt deletable fulfillsOnlineOrders hasActiveInventory hasUnfulfilledOrders id isActive isFulfillmentService isPrimary legacyResourceId name shipsInventory updatedAt'): array
    {
        return $this->execute('query', 'location', $args, ['id' => 'ID'], $selection);
    }

    /**
     * Return a location by an identifier.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: identifier: LocationIdentifierInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function locationByIdentifier(array $args = [], string $selection = 'activatable addressVerified createdAt deactivatable deactivatedAt deletable fulfillsOnlineOrders hasActiveInventory hasUnfulfilledOrders id isActive isFulfillmentService isPrimary legacyResourceId name shipsInventory updatedAt'): array
    {
        return $this->execute('query', 'locationByIdentifier', $args, ['identifier' => 'LocationIdentifierInput!'], $selection);
    }

    /**
     * A paginated list of inventory locations where merchants can stock [`Product`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) items and fulfill [`Order`](https://shopify.dev/docs/ap
     *
     * @param array<string,mixed> $args Variaveis GraphQL: includeLegacy: Boolean, includeInactive: Boolean, first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: LocationSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function locations(array $args = [], string $selection = 'edges { node { activatable addressVerified createdAt deactivatable deactivatedAt deletable fulfillsOnlineOrders hasActiveInventory hasUnfulfilledOrders id isActive isFulfillmentService isPrimary legacyResourceId name shipsInventory updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'locations', $args, ['includeLegacy' => 'Boolean', 'includeInactive' => 'Boolean', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'LocationSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * Returns a list of all origin locations available for a delivery profile.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function locationsAvailableForDeliveryProfiles(array $args = [], string $selection = 'activatable addressVerified createdAt deactivatable deactivatedAt deletable fulfillsOnlineOrders hasActiveInventory hasUnfulfilledOrders id isActive isFulfillmentService isPrimary legacyResourceId name shipsInventory updatedAt'): array
    {
        return $this->execute('query', 'locationsAvailableForDeliveryProfiles', $args, [], $selection);
    }

    /**
     * Returns a list of all origin locations available for a delivery profile.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function locationsAvailableForDeliveryProfilesConnection(array $args = [], string $selection = 'edges { node { activatable addressVerified createdAt deactivatable deactivatedAt deletable fulfillsOnlineOrders hasActiveInventory hasUnfulfilledOrders id isActive isFulfillmentService isPrimary legacyResourceId name shipsInventory updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'locationsAvailableForDeliveryProfilesConnection', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Returns the count of locations for the given shop. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function locationsCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'locationsCount', $args, ['query' => 'String', 'limit' => 'Int'], $selection);
    }
}

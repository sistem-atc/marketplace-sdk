<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Location.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class LocationMutations extends BaseOperations
{
    /**
     * Activates a location so that you can stock inventory at the location. Refer to the [`isActive`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Location#field-isactive) and [`activatable`](h
     *
     * @param array<string,mixed> $args Variaveis GraphQL: locationId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function locationActivate(array $args = [], string $selection = 'location { activatable addressVerified createdAt deactivatable deactivatedAt deletable fulfillsOnlineOrders hasActiveInventory hasUnfulfilledOrders id isActive isFulfillmentService isPrimary legacyResourceId name shipsInventory updatedAt } locationActivateUserErrors { field message }'): array
    {
        return $this->execute('mutation', 'locationActivate', $args, ['locationId' => 'ID!'], $selection);
    }

    /**
     * Adds a new [`Location`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Location) where you can stock inventory and fulfill orders. Locations represent physical places like warehouses, retai
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: LocationAddInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function locationAdd(array $args = [], string $selection = 'location { activatable addressVerified createdAt deactivatable deactivatedAt deletable fulfillsOnlineOrders hasActiveInventory hasUnfulfilledOrders id isActive isFulfillmentService isPrimary legacyResourceId name shipsInventory updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'locationAdd', $args, ['input' => 'LocationAddInput!'], $selection);
    }

    /**
     * Deactivates a location and moves inventory, pending orders, and moving transfers " "to a destination location. > Caution: > As of 2026-01, this mutation supports an optional idempotency key using the
     *
     * @param array<string,mixed> $args Variaveis GraphQL: locationId: ID!, destinationLocationId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function locationDeactivate(array $args = [], string $selection = 'location { activatable addressVerified createdAt deactivatable deactivatedAt deletable fulfillsOnlineOrders hasActiveInventory hasUnfulfilledOrders id isActive isFulfillmentService isPrimary legacyResourceId name shipsInventory updatedAt } locationDeactivateUserErrors { field message }'): array
    {
        return $this->execute('mutation', 'locationDeactivate', $args, ['locationId' => 'ID!', 'destinationLocationId' => 'ID'], $selection);
    }

    /**
     * Deletes a location.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: locationId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function locationDelete(array $args = [], string $selection = 'deletedLocationId locationDeleteUserErrors { field message }'): array
    {
        return $this->execute('mutation', 'locationDelete', $args, ['locationId' => 'ID!'], $selection);
    }

    /**
     * Updates the properties of an existing [`Location`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Location). You can modify the location's name, address, whether it fulfills online orders,
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: LocationEditInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function locationEdit(array $args = [], string $selection = 'location { activatable addressVerified createdAt deactivatable deactivatedAt deletable fulfillsOnlineOrders hasActiveInventory hasUnfulfilledOrders id isActive isFulfillmentService isPrimary legacyResourceId name shipsInventory updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'locationEdit', $args, ['id' => 'ID!', 'input' => 'LocationEditInput!'], $selection);
    }

    /**
     * Disables local pickup for a location.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: locationId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function locationLocalPickupDisable(array $args = [], string $selection = 'locationId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'locationLocalPickupDisable', $args, ['locationId' => 'ID!'], $selection);
    }

    /**
     * Enables local pickup for a location so customers can collect their orders in person. Configures the estimated pickup time that customers see at checkout and optional instructions for finding or access
     *
     * @param array<string,mixed> $args Variaveis GraphQL: localPickupSettings: DeliveryLocationLocalPickupEnableInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function locationLocalPickupEnable(array $args = [], string $selection = 'localPickupSettings { instructions pickupTime } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'locationLocalPickupEnable', $args, ['localPickupSettings' => 'DeliveryLocationLocalPickupEnableInput!'], $selection);
    }
}

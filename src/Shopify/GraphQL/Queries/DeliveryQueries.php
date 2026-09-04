<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Delivery.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class DeliveryQueries extends BaseOperations
{
    /**
     * Returns a list of activated carrier services and associated shop locations that support them.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function availableCarrierServices(array $args = [], string $selection = '__typename'): array
    {
        return $this->execute('query', 'availableCarrierServices', $args, [], $selection);
    }

    /**
     * Returns a `DeliveryCarrierService` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function carrierService(array $args = [], string $selection = 'active callbackUrl formattedName id name supportsServiceDiscovery'): array
    {
        return $this->execute('query', 'carrierService', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * A paginated list of carrier services configured for the shop. Carrier services provide real-time shipping rates from external providers like FedEx, UPS, or custom shipping solutions. Use the `query` p
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: CarrierServiceSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function carrierServices(array $args = [], string $selection = 'edges { node { active callbackUrl formattedName id name supportsServiceDiscovery } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'carrierServices', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'CarrierServiceSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * The delivery customization.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryCustomization(array $args = [], string $selection = 'enabled functionId id title'): array
    {
        return $this->execute('query', 'deliveryCustomization', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * The delivery customizations.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryCustomizations(array $args = [], string $selection = 'edges { node { enabled functionId id title } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'deliveryCustomizations', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'query' => 'String'], $selection);
    }

    /**
     * Retrieves a [`DeliveryProfile`](https://shopify.dev/docs/api/admin-graphql/latest/objects/DeliveryProfile) by ID. Delivery profiles group shipping settings for specific [`Product`](https://shopify.dev
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryProfile(array $args = [], string $selection = 'activeMethodDefinitionsCount coversAllItems default id locationsWithoutRatesCount name originLocationCount version zoneCountryCount'): array
    {
        return $this->execute('query', 'deliveryProfile', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a paginated list of [`DeliveryProfile`](https://shopify.dev/docs/api/admin-graphql/latest/objects/DeliveryProfile) objects for the shop. Delivery profiles group [`Product`](https://shopify.dev
     *
     * @param array<string,mixed> $args Variaveis GraphQL: merchantOwnedOnly: Boolean, first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryProfiles(array $args = [], string $selection = 'edges { node { activeMethodDefinitionsCount coversAllItems default id locationsWithoutRatesCount name originLocationCount version zoneCountryCount } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'deliveryProfiles', $args, ['merchantOwnedOnly' => 'Boolean', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Returns delivery promise participants.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: ownerIds: [ID!], brandedPromiseHandle: String!, first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryPromiseParticipants(array $args = [], string $selection = 'edges { node { id ownerType } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'deliveryPromiseParticipants', $args, ['ownerIds' => '[ID!]', 'brandedPromiseHandle' => 'String!', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Lookup a delivery promise provider.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: locationId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryPromiseProvider(array $args = [], string $selection = 'active fulfillmentDelay id timeZone'): array
    {
        return $this->execute('query', 'deliveryPromiseProvider', $args, ['locationId' => 'ID!'], $selection);
    }

    /**
     * Represents the delivery promise settings for a shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryPromiseSettings(array $args = [], string $selection = 'deliveryDatesEnabled processingTime'): array
    {
        return $this->execute('query', 'deliveryPromiseSettings', $args, [], $selection);
    }

    /**
     * Returns a `ShippingLabel` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shippingLabel(array $args = [], string $selection = 'cancellable id printed'): array
    {
        return $this->execute('query', 'shippingLabel', $args, ['id' => 'ID!'], $selection);
    }
}

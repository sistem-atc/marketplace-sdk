<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Market.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class MarketQueries extends BaseOperations
{
    /**
     * The geographic regions that you can set as the [`Shop`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Shop)'s backup region. The backup region serves as a fallback when the system can't de
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function availableBackupRegions(array $args = [], string $selection = 'id name'): array
    {
        return $this->execute('query', 'availableBackupRegions', $args, [], $selection);
    }

    /**
     * The backup region of the shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function backupRegion(array $args = [], string $selection = 'id name'): array
    {
        return $this->execute('query', 'backupRegion', $args, [], $selection);
    }

    /**
     * Returns a `Market` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function market(array $args = [], string $selection = 'assignedCustomization enabled handle id name primary status type'): array
    {
        return $this->execute('query', 'market', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns the applicable market for a customer based on where they are in the world.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: countryCode: CountryCode!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketByGeography(array $args = [], string $selection = 'assignedCustomization enabled handle id name primary status type'): array
    {
        return $this->execute('query', 'marketByGeography', $args, ['countryCode' => 'CountryCode!'], $selection);
    }

    /**
     * A resource that can have localized values for different markets.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: resourceId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketLocalizableResource(array $args = [], string $selection = 'resourceId'): array
    {
        return $this->execute('query', 'marketLocalizableResource', $args, ['resourceId' => 'ID!'], $selection);
    }

    /**
     * Resources that can have localized values for different markets.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: resourceType: MarketLocalizableResourceType!, first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketLocalizableResources(array $args = [], string $selection = 'edges { node { resourceId } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'marketLocalizableResources', $args, ['resourceType' => 'MarketLocalizableResourceType!', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Resources that can have localized values for different markets.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: resourceIds: [ID!]!, first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketLocalizableResourcesByIds(array $args = [], string $selection = 'edges { node { resourceId } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'marketLocalizableResourcesByIds', $args, ['resourceIds' => '[ID!]!', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Returns a paginated list of [`Market`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Market) objects configured for the shop. Markets match buyers based on defined conditions to deliver cu
     *
     * @param array<string,mixed> $args Variaveis GraphQL: type: MarketType, first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: MarketsSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function markets(array $args = [], string $selection = 'edges { node { assignedCustomization enabled handle id name primary status type } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'markets', $args, ['type' => 'MarketType', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'MarketsSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * The resolved values for a buyer signal.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: buyerSignal: BuyerSignalInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketsResolvedValues(array $args = [], string $selection = 'currencyCode'): array
    {
        return $this->execute('query', 'marketsResolvedValues', $args, ['buyerSignal' => 'BuyerSignalInput!'], $selection);
    }

    /**
     * The primary market of the shop.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function primaryMarket(array $args = [], string $selection = 'assignedCustomization enabled handle id name primary status type'): array
    {
        return $this->execute('query', 'primaryMarket', $args, [], $selection);
    }
}

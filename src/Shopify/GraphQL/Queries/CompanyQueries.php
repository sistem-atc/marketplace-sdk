<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Company.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class CompanyQueries extends BaseOperations
{
    /**
     * A paginated list of companies in the shop. [`Company`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Company) objects are business entities that purchase from the merchant. Use the [`quer
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: CompanySortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companies(array $args = [], string $selection = 'edges { node { contactCount createdAt customerSince defaultCursor externalId hasTimelineComment id lifetimeDuration name note updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'companies', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'CompanySortKeys', 'query' => 'String'], $selection);
    }

    /**
     * The number of companies for a shop. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companiesCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'companiesCount', $args, ['limit' => 'Int'], $selection);
    }

    /**
     * Returns a `Company` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function company(array $args = [], string $selection = 'contactCount createdAt customerSince defaultCursor externalId hasTimelineComment id lifetimeDuration name note updatedAt'): array
    {
        return $this->execute('query', 'company', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a `CompanyContact` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyContact(array $args = [], string $selection = 'createdAt id isMainContact lifetimeDuration locale title updatedAt'): array
    {
        return $this->execute('query', 'companyContact', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a `CompanyContactRole` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyContactRole(array $args = [], string $selection = 'id name note'): array
    {
        return $this->execute('query', 'companyContactRole', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a `CompanyLocation` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocation(array $args = [], string $selection = 'createdAt currency defaultCursor externalId hasTimelineComment id inCatalog locale name note orderCount phone taxExemptions taxRegistrationId updatedAt'): array
    {
        return $this->execute('query', 'companyLocation', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * A paginated list of [`CompanyLocation`](https://shopify.dev/docs/api/admin-graphql/latest/objects/CompanyLocation) objects for B2B customers. Company locations represent individual branches or offices
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: CompanyLocationSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocations(array $args = [], string $selection = 'edges { node { createdAt currency defaultCursor externalId hasTimelineComment id inCatalog locale name note orderCount phone taxExemptions taxRegistrationId updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'companyLocations', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'CompanyLocationSortKeys', 'query' => 'String'], $selection);
    }
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Catalog.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class CatalogQueries extends BaseOperations
{
    /**
     * Retrieves a [catalog](https://shopify.dev/docs/api/admin-graphql/latest/interfaces/Catalog) by its ID. A catalog represents a list of products with publishing and pricing information, and can be assoc
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function catalog(array $args = [], string $selection = 'id status title'): array
    {
        return $this->execute('query', 'catalog', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns the most recent catalog operations for the shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function catalogOperations(array $args = [], string $selection = 'id processedRowCount status'): array
    {
        return $this->execute('query', 'catalogOperations', $args, [], $selection);
    }

    /**
     * Returns a paginated list of catalogs for the shop. Catalogs control which products are published and how they're priced in different contexts, such as international markets (Canada vs. United States),
     *
     * @param array<string,mixed> $args Variaveis GraphQL: type: CatalogType, first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: CatalogSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function catalogs(array $args = [], string $selection = 'edges { node { id status title } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'catalogs', $args, ['type' => 'CatalogType', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'CatalogSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * The count of catalogs belonging to the shop. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: type: CatalogType, query: String, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function catalogsCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'catalogsCount', $args, ['type' => 'CatalogType', 'query' => 'String', 'limit' => 'Int'], $selection);
    }
}

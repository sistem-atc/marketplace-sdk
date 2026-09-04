<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Segment.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class SegmentQueries extends BaseOperations
{
    /**
     * Retrieves a customer [`Segment`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Segment) by ID. Segments are dynamic groups of customers that meet specific criteria defined through [Shopify
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function segment(array $args = [], string $selection = 'creationDate id lastEditDate name query'): array
    {
        return $this->execute('query', 'segment', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * A list of filter suggestions associated with a segment. A segment is a group of members (commonly customers) that meet specific criteria.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: search: String!, first: Int!, after: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function segmentFilterSuggestions(array $args = [], string $selection = 'edges { node { localizedName multiValue queryName } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'segmentFilterSuggestions', $args, ['search' => 'String!', 'first' => 'Int!', 'after' => 'String'], $selection);
    }

    /**
     * A list of filters.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function segmentFilters(array $args = [], string $selection = 'edges { node { localizedName multiValue queryName } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'segmentFilters', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String'], $selection);
    }

    /**
     * A list of a shop's segment migrations.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: savedSearchId: ID, first: Int, after: String, last: Int, before: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function segmentMigrations(array $args = [], string $selection = 'edges { node { id savedSearchId segmentId } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'segmentMigrations', $args, ['savedSearchId' => 'ID', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String'], $selection);
    }

    /**
     * The list of suggested values corresponding to a particular filter for a segment. A segment is a group of members, such as customers, that meet specific criteria.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: search: String!, filterQueryName: String, functionParameterQueryName: String, first: Int, after: String, last: Int, before: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function segmentValueSuggestions(array $args = [], string $selection = 'edges { node { localizedValue queryName } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'segmentValueSuggestions', $args, ['search' => 'String!', 'filterQueryName' => 'String', 'functionParameterQueryName' => 'String', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String'], $selection);
    }

    /**
     * Returns a paginated list of [`Segment`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Segment) objects for the shop. Segments are dynamic groups of customers that meet specific criteria de
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: SegmentSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function segments(array $args = [], string $selection = 'edges { node { creationDate id lastEditDate name query } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'segments', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'SegmentSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * The number of segments for a shop. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function segmentsCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'segmentsCount', $args, ['limit' => 'Int'], $selection);
    }
}

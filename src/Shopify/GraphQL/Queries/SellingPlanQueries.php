<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio SellingPlan.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class SellingPlanQueries extends BaseOperations
{
    /**
     * Returns a `SellingPlanGroup` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function sellingPlanGroup(array $args = [], string $selection = 'appId appliesToProduct appliesToProductVariant appliesToProductVariants createdAt description id merchantCode name options position summary'): array
    {
        return $this->execute('query', 'sellingPlanGroup', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Retrieves a paginated list of [`SellingPlanGroup`](https://shopify.dev/docs/api/admin-graphql/latest/objects/SellingPlanGroup) objects that belong to the app making the API call. Selling plan groups a
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: SellingPlanGroupSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function sellingPlanGroups(array $args = [], string $selection = 'edges { node { appId appliesToProduct appliesToProductVariant appliesToProductVariants createdAt description id merchantCode name options position summary } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'sellingPlanGroups', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'SellingPlanGroupSortKeys', 'query' => 'String'], $selection);
    }
}

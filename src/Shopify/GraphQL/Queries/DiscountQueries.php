<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Discount.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class DiscountQueries extends BaseOperations
{
    /**
     * Returns a `DiscountAutomatic` resource by ID.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function automaticDiscount(array $args = [], string $selection = '__typename'): array
    {
        return $this->execute('query', 'automaticDiscount', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a `DiscountAutomaticNode` resource by ID.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function automaticDiscountNode(array $args = [], string $selection = 'id'): array
    {
        return $this->execute('query', 'automaticDiscountNode', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a list of [automatic discounts](https://help.shopify.com/manual/discounts/discount-types#automatic-discounts).
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: AutomaticDiscountSortKeys, query: String, savedSearchId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function automaticDiscountNodes(array $args = [], string $selection = 'edges { node { id } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'automaticDiscountNodes', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'AutomaticDiscountSortKeys', 'query' => 'String', 'savedSearchId' => 'ID'], $selection);
    }

    /**
     * List of the shop's automatic discount saved searches.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function automaticDiscountSavedSearches(array $args = [], string $selection = 'edges { node { id legacyResourceId name query resourceType searchTerms } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'automaticDiscountSavedSearches', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Returns a list of automatic discounts that are applied in the cart and at checkout without requiring a discount code.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: AutomaticDiscountSortKeys, query: String, savedSearchId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function automaticDiscounts(array $args = [], string $selection = 'edges { node { __typename } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'automaticDiscounts', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'AutomaticDiscountSortKeys', 'query' => 'String', 'savedSearchId' => 'ID'], $selection);
    }

    /**
     * Returns a [code discount](https://help.shopify.com/manual/discounts/discount-types#discount-codes) resource by ID.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function codeDiscountNode(array $args = [], string $selection = 'id'): array
    {
        return $this->execute('query', 'codeDiscountNode', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Retrieves a [code discount](https://help.shopify.com/manual/discounts/discount-types#discount-codes) by its discount code. The search is case-insensitive, enabling you to find discounts regardless of
     *
     * @param array<string,mixed> $args Variaveis GraphQL: code: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function codeDiscountNodeByCode(array $args = [], string $selection = 'id'): array
    {
        return $this->execute('query', 'codeDiscountNodeByCode', $args, ['code' => 'String!'], $selection);
    }

    /**
     * Returns a list of [code-based discounts](https://help.shopify.com/manual/discounts/discount-types#discount-codes).
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: CodeDiscountSortKeys, query: String, savedSearchId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function codeDiscountNodes(array $args = [], string $selection = 'edges { node { id } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'codeDiscountNodes', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'CodeDiscountSortKeys', 'query' => 'String', 'savedSearchId' => 'ID'], $selection);
    }

    /**
     * List of the shop's code discount saved searches.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function codeDiscountSavedSearches(array $args = [], string $selection = 'edges { node { id legacyResourceId name query resourceType searchTerms } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'codeDiscountSavedSearches', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * The total number of discount codes for the shop. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodesCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'discountCodesCount', $args, ['query' => 'String', 'limit' => 'Int'], $selection);
    }

    /**
     * Returns a `DiscountNode` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountNode(array $args = [], string $selection = 'id'): array
    {
        return $this->execute('query', 'discountNode', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a list of discounts.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: DiscountSortKeys, query: String, savedSearchId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountNodes(array $args = [], string $selection = 'edges { node { id } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'discountNodes', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'DiscountSortKeys', 'query' => 'String', 'savedSearchId' => 'ID'], $selection);
    }

    /**
     * The total number of discounts for the shop. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String, savedSearchId: ID, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountNodesCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'discountNodesCount', $args, ['query' => 'String', 'savedSearchId' => 'ID', 'limit' => 'Int'], $selection);
    }

    /**
     * Returns a `DiscountRedeemCodeBulkCreation` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountRedeemCodeBulkCreation(array $args = [], string $selection = 'codesCount createdAt done failedCount id importedCount'): array
    {
        return $this->execute('query', 'discountRedeemCodeBulkCreation', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * List of the shop's redeemed discount code saved searches.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: DiscountCodeSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountRedeemCodeSavedSearches(array $args = [], string $selection = 'edges { node { id legacyResourceId name query resourceType searchTerms } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'discountRedeemCodeSavedSearches', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'DiscountCodeSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * List of tags associated to discounts.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: DiscountTagSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountTags(array $args = [], string $selection = 'edges { node { __typename } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'discountTags', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'DiscountTagSortKeys', 'query' => 'String'], $selection);
    }
}

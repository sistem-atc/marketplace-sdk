<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Return.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class ReturnQueries extends BaseOperations
{
    /**
     * Retrieves a return by its ID. A return represents the intent of a buyer to ship one or more items from an order back to a merchant or a third-party fulfillment location. Use the `return` query to ret
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function return(array $args = [], string $selection = 'closedAt createdAt id name requestApprovedAt status totalQuantity'): array
    {
        return $this->execute('query', 'return', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Calculates the financial outcome of a [`Return`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Return) without creating it. Use this query to preview return costs before initiating the act
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: CalculateReturnInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function returnCalculate(array $args = [], string $selection = 'id'): array
    {
        return $this->execute('query', 'returnCalculate', $args, ['input' => 'CalculateReturnInput!'], $selection);
    }

    /**
     * Returns the full library of available return reason definitions. Use this query to retrieve the standardized return reasons available for creating returns. Filter by IDs or handles to get specific de
     *
     * @param array<string,mixed> $args Variaveis GraphQL: ids: [ID!], handles: [String!], first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: ReturnReasonDefinitionSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function returnReasonDefinitions(array $args = [], string $selection = 'edges { node { deleted handle id name } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'returnReasonDefinitions', $args, ['ids' => '[ID!]', 'handles' => '[String!]', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'ReturnReasonDefinitionSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * Returns a `ReturnableFulfillment` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function returnableFulfillment(array $args = [], string $selection = 'id'): array
    {
        return $this->execute('query', 'returnableFulfillment', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * List of returnable fulfillments.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: orderId: ID!, first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function returnableFulfillments(array $args = [], string $selection = 'edges { node { id } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'returnableFulfillments', $args, ['orderId' => 'ID!', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Lookup a reverse delivery by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function reverseDelivery(array $args = [], string $selection = 'id'): array
    {
        return $this->execute('query', 'reverseDelivery', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Lookup a reverse fulfillment order by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function reverseFulfillmentOrder(array $args = [], string $selection = 'id status'): array
    {
        return $this->execute('query', 'reverseFulfillmentOrder', $args, ['id' => 'ID!'], $selection);
    }
}

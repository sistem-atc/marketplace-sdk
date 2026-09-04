<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Event.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class EventQueries extends BaseOperations
{
    /**
     * Retrieves a single event by ID. Events chronicle activities in your store such as resource creation, updates, or staff comments. The query returns an [`Event`](https://shopify.dev/docs/api/admin-graph
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function event(array $args = [], string $selection = 'action appTitle attributeToApp attributeToUser createdAt criticalAlert id message'): array
    {
        return $this->execute('query', 'event', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * A paginated list of events that chronicle activities in the store. [`Event`](https://shopify.dev/docs/api/admin-graphql/latest/interfaces/Event) is an interface implemented by types such as [`BasicEve
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: EventSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function events(array $args = [], string $selection = 'edges { node { action appTitle attributeToApp attributeToUser createdAt criticalAlert id message } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'events', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'EventSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * Count of events. Limited to a maximum of 10000.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function eventsCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'eventsCount', $args, ['query' => 'String'], $selection);
    }
}

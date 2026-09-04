<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio BulkOperation.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class BulkOperationQueries extends BaseOperations
{
    /**
     * Returns a `BulkOperation` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function bulkOperation(array $args = [], string $selection = 'completedAt createdAt errorCode fileSize id objectCount partialDataUrl query rootObjectCount status type url'): array
    {
        return $this->execute('query', 'bulkOperation', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns the app's bulk operations meeting the specified filters. Defaults to sorting by created_at, with newest operations first.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: BulkOperationsSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function bulkOperations(array $args = [], string $selection = 'edges { node { completedAt createdAt errorCode fileSize id objectCount partialDataUrl query rootObjectCount status type url } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'bulkOperations', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'BulkOperationsSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * Returns the current app's most recent [`BulkOperation`](https://shopify.dev/docs/api/admin-graphql/latest/objects/BulkOperation). Bulk query and bulk mutation operations can run at the same time per s
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: type: BulkOperationType
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function currentBulkOperation(array $args = [], string $selection = 'completedAt createdAt errorCode fileSize id objectCount partialDataUrl query rootObjectCount status type url'): array
    {
        return $this->execute('query', 'currentBulkOperation', $args, ['type' => 'BulkOperationType'], $selection);
    }
}

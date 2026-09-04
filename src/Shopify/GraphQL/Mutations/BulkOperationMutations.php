<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio BulkOperation.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class BulkOperationMutations extends BaseOperations
{
    /**
     * Starts the cancelation process of a running bulk operation. There may be a short delay from when a cancelation starts until the operation is actually canceled.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function bulkOperationCancel(array $args = [], string $selection = 'bulkOperation { completedAt createdAt errorCode fileSize id objectCount partialDataUrl query rootObjectCount status type url } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'bulkOperationCancel', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Creates and runs a [bulk operation](https://shopify.dev/docs/api/admin-graphql/latest/objects/BulkOperation) to import data asynchronously. This mutation executes a specified GraphQL mutation multiple
     *
     * @param array<string,mixed> $args Variaveis GraphQL: mutation: String!, stagedUploadPath: String!, clientIdentifier: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function bulkOperationRunMutation(array $args = [], string $selection = 'bulkOperation { completedAt createdAt errorCode fileSize id objectCount partialDataUrl query rootObjectCount status type url } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'bulkOperationRunMutation', $args, ['mutation' => 'String!', 'stagedUploadPath' => 'String!', 'clientIdentifier' => 'String'], $selection);
    }

    /**
     * Creates and runs a [bulk operation](https://shopify.dev/docs/api/admin-graphql/latest/objects/BulkOperation) to fetch data asynchronously. The operation processes your GraphQL query in the background
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String!, groupObjects: Boolean!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function bulkOperationRunQuery(array $args = [], string $selection = 'bulkOperation { completedAt createdAt errorCode fileSize id objectCount partialDataUrl query rootObjectCount status type url } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'bulkOperationRunQuery', $args, ['query' => 'String!', 'groupObjects' => 'Boolean!'], $selection);
    }
}

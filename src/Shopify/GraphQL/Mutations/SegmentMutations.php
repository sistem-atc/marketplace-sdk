<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Segment.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class SegmentMutations extends BaseOperations
{
    /**
     * Creates a segment.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: name: String!, query: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function segmentCreate(array $args = [], string $selection = 'segment { creationDate id lastEditDate name query } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'segmentCreate', $args, ['name' => 'String!', 'query' => 'String!'], $selection);
    }

    /**
     * Deletes a segment.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function segmentDelete(array $args = [], string $selection = 'deletedSegmentId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'segmentDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates a segment.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, name: String, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function segmentUpdate(array $args = [], string $selection = 'segment { creationDate id lastEditDate name query } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'segmentUpdate', $args, ['id' => 'ID!', 'name' => 'String', 'query' => 'String'], $selection);
    }
}

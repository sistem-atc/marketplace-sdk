<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Translation.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class TranslationQueries extends BaseOperations
{
    /**
     * Retrieves a resource that has translatable fields. Returns the resource's [`Translation`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Translation) objects for different locales and marke
     *
     * @param array<string,mixed> $args Variaveis GraphQL: resourceId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function translatableResource(array $args = [], string $selection = 'resourceId'): array
    {
        return $this->execute('query', 'translatableResource', $args, ['resourceId' => 'ID!'], $selection);
    }

    /**
     * Returns a paginated list of [`TranslatableResource`](https://shopify.dev/docs/api/admin-graphql/latest/objects/TranslatableResource) objects for a specific resource type. Each resource provides transl
     *
     * @param array<string,mixed> $args Variaveis GraphQL: resourceType: TranslatableResourceType!, first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function translatableResources(array $args = [], string $selection = 'edges { node { resourceId } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'translatableResources', $args, ['resourceType' => 'TranslatableResourceType!', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Returns a paginated list of [`TranslatableResource`](https://shopify.dev/docs/api/admin-graphql/latest/objects/TranslatableResource) objects for the specified resource IDs. Each resource provides tran
     *
     * @param array<string,mixed> $args Variaveis GraphQL: resourceIds: [ID!]!, first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function translatableResourcesByIds(array $args = [], string $selection = 'edges { node { resourceId } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'translatableResourcesByIds', $args, ['resourceIds' => '[ID!]!', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }
}

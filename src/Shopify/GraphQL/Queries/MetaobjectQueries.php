<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Metaobject.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class MetaobjectQueries extends BaseOperations
{
    /**
     * Retrieves a single [`Metaobject`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Metaobject) by its global ID. [Metaobjects](https://shopify.dev/docs/apps/build/custom-data#what-are-metaobj
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metaobject(array $args = [], string $selection = 'createdAt displayName handle id type updatedAt values'): array
    {
        return $this->execute('query', 'metaobject', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Retrieves a [`Metaobject`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Metaobject) by its handle and type. Handles are unique identifiers within a metaobject type.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: handle: MetaobjectHandleInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metaobjectByHandle(array $args = [], string $selection = 'createdAt displayName handle id type updatedAt values'): array
    {
        return $this->execute('query', 'metaobjectByHandle', $args, ['handle' => 'MetaobjectHandleInput!'], $selection);
    }

    /**
     * Retrieves a [`MetaobjectDefinition`](https://shopify.dev/docs/api/admin-graphql/latest/objects/MetaobjectDefinition) by its global ID. Metaobject definitions provide the structure and fields for metao
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metaobjectDefinition(array $args = [], string $selection = 'createdAt description displayNameKey hasThumbnailField id metaobjectsCount name type updatedAt'): array
    {
        return $this->execute('query', 'metaobjectDefinition', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Retrieves a [`MetaobjectDefinition`](https://shopify.dev/docs/api/admin-graphql/latest/objects/MetaobjectDefinition) by its type. The type serves as a unique identifier that distinguishes one metaobje
     *
     * @param array<string,mixed> $args Variaveis GraphQL: type: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metaobjectDefinitionByType(array $args = [], string $selection = 'createdAt description displayNameKey hasThumbnailField id metaobjectsCount name type updatedAt'): array
    {
        return $this->execute('query', 'metaobjectDefinitionByType', $args, ['type' => 'String!'], $selection);
    }

    /**
     * Returns a paginated list of all [`MetaobjectDefinition`](https://shopify.dev/docs/api/admin-graphql/latest/objects/MetaobjectDefinition) objects configured for the store. Metaobject definitions provid
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metaobjectDefinitions(array $args = [], string $selection = 'edges { node { createdAt description displayNameKey hasThumbnailField id metaobjectsCount name type updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'metaobjectDefinitions', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Returns a paginated list of [`Metaobject`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Metaobject) entries for a specific type. Metaobjects are custom data structures that extend Shopify
     *
     * @param array<string,mixed> $args Variaveis GraphQL: type: String!, sortKey: String, first: Int, after: String, last: Int, before: String, reverse: Boolean, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metaobjects(array $args = [], string $selection = 'edges { node { createdAt displayName handle id type updatedAt values } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'metaobjects', $args, ['type' => 'String!', 'sortKey' => 'String', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'query' => 'String'], $selection);
    }
}

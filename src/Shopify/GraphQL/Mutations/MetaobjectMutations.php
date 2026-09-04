<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Metaobject.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class MetaobjectMutations extends BaseOperations
{
    /**
     * Asynchronously delete metaobjects and their associated metafields in bulk.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: where: MetaobjectBulkDeleteWhereCondition!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metaobjectBulkDelete(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'metaobjectBulkDelete', $args, ['where' => 'MetaobjectBulkDeleteWhereCondition!'], $selection);
    }

    /**
     * Creates a metaobject entry based on an existing [`MetaobjectDefinition`](https://shopify.dev/docs/api/admin-graphql/latest/objects/MetaobjectDefinition). The type must match a definition that already
     *
     * @param array<string,mixed> $args Variaveis GraphQL: metaobject: MetaobjectCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metaobjectCreate(array $args = [], string $selection = 'metaobject { createdAt displayName handle id type updatedAt values } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'metaobjectCreate', $args, ['metaobject' => 'MetaobjectCreateInput!'], $selection);
    }

    /**
     * Creates a metaobject definition that establishes the structure for custom data objects in your store. The definition specifies the fields, data types, and access permissions that all [`Metaobject`](ht
     *
     * @param array<string,mixed> $args Variaveis GraphQL: definition: MetaobjectDefinitionCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metaobjectDefinitionCreate(array $args = [], string $selection = 'metaobjectDefinition { createdAt description displayNameKey hasThumbnailField id metaobjectsCount name type updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'metaobjectDefinitionCreate', $args, ['definition' => 'MetaobjectDefinitionCreateInput!'], $selection);
    }

    /**
     * Deletes the specified metaobject definition. Also deletes all related metafield definitions, metaobjects, and metafields asynchronously.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metaobjectDefinitionDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'metaobjectDefinitionDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates a [`MetaobjectDefinition`](https://shopify.dev/docs/api/admin-graphql/latest/objects/MetaobjectDefinition)'s configuration and field structure. You can modify the definition's name, descriptio
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, definition: MetaobjectDefinitionUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metaobjectDefinitionUpdate(array $args = [], string $selection = 'metaobjectDefinition { createdAt description displayNameKey hasThumbnailField id metaobjectsCount name type updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'metaobjectDefinitionUpdate', $args, ['id' => 'ID!', 'definition' => 'MetaobjectDefinitionUpdateInput!'], $selection);
    }

    /**
     * Deletes the specified metaobject and its associated metafields.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metaobjectDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'metaobjectDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates a [`Metaobject`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Metaobject) with new field values, handle, or capabilities. [Metaobjects](https://shopify.dev/docs/apps/build/custom-
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, metaobject: MetaobjectUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metaobjectUpdate(array $args = [], string $selection = 'metaobject { createdAt displayName handle id type updatedAt values } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'metaobjectUpdate', $args, ['id' => 'ID!', 'metaobject' => 'MetaobjectUpdateInput!'], $selection);
    }

    /**
     * Creates or updates a [`Metaobject`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Metaobject) based on its handle. If a metaobject with the specified handle exists, the mutation updates it
     *
     * @param array<string,mixed> $args Variaveis GraphQL: handle: MetaobjectHandleInput!, metaobject: MetaobjectUpsertInput, values: JSON
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metaobjectUpsert(array $args = [], string $selection = 'metaobject { createdAt displayName handle id type updatedAt values } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'metaobjectUpsert', $args, ['handle' => 'MetaobjectHandleInput!', 'metaobject' => 'MetaobjectUpsertInput', 'values' => 'JSON'], $selection);
    }

    /**
     * Enables the specified standard metaobject definition from its template.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: type: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function standardMetaobjectDefinitionEnable(array $args = [], string $selection = 'metaobjectDefinition { createdAt description displayNameKey hasThumbnailField id metaobjectsCount name type updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'standardMetaobjectDefinitionEnable', $args, ['type' => 'String!'], $selection);
    }
}

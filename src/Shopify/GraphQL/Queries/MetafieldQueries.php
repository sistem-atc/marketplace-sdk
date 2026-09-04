<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Metafield.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class MetafieldQueries extends BaseOperations
{
    /**
     * Retrieves a [`MetafieldDefinition`](https://shopify.dev/docs/api/admin-graphql/current/objects/MetafieldDefinition) by its identifier. You can identify a definition using either its owner type, namesp
     *
     * @param array<string,mixed> $args Variaveis GraphQL: identifier: MetafieldDefinitionIdentifierInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metafieldDefinition(array $args = [], string $selection = 'description id key metafieldsCount name namespace ownerType pinnedPosition useAsCollectionCondition validationStatus'): array
    {
        return $this->execute('query', 'metafieldDefinition', $args, ['identifier' => 'MetafieldDefinitionIdentifierInput'], $selection);
    }

    /**
     * The available metafield types that you can use when creating [`MetafieldDefinition`](https://shopify.dev/docs/api/admin-graphql/current/objects/MetafieldDefinition) objects. Each type specifies what k
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metafieldDefinitionTypes(array $args = [], string $selection = 'category name supportsDefinitionMigrations valueType'): array
    {
        return $this->execute('query', 'metafieldDefinitionTypes', $args, [], $selection);
    }

    /**
     * Returns a list of metafield definitions.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: key: String, namespace: String, ownerType: MetafieldOwnerType!, pinnedStatus: MetafieldDefinitionPinnedStatus, constraintSubtype: MetafieldDefinitionConstraintSubtypeIdentifier, constraintStatus: MetafieldDefinitionConstraintStatus, first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: MetafieldDefinitionSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metafieldDefinitions(array $args = [], string $selection = 'edges { node { description id key metafieldsCount name namespace ownerType pinnedPosition useAsCollectionCondition validationStatus } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'metafieldDefinitions', $args, ['key' => 'String', 'namespace' => 'String', 'ownerType' => 'MetafieldOwnerType!', 'pinnedStatus' => 'MetafieldDefinitionPinnedStatus', 'constraintSubtype' => 'MetafieldDefinitionConstraintSubtypeIdentifier', 'constraintStatus' => 'MetafieldDefinitionConstraintStatus', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'MetafieldDefinitionSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * Retrieves preset metafield definition templates for common use cases. Each template provides a reserved namespace and key combination for specific purposes like product subtitles, care guides, or ISBN
     *
     * @param array<string,mixed> $args Variaveis GraphQL: constraintSubtype: MetafieldDefinitionConstraintSubtypeIdentifier, constraintStatus: MetafieldDefinitionConstraintStatus, excludeActivated: Boolean, first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function standardMetafieldDefinitionTemplates(array $args = [], string $selection = 'edges { node { description id key name namespace ownerTypes visibleToStorefrontApi } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'standardMetafieldDefinitionTemplates', $args, ['constraintSubtype' => 'MetafieldDefinitionConstraintSubtypeIdentifier', 'constraintStatus' => 'MetafieldDefinitionConstraintStatus', 'excludeActivated' => 'Boolean', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }
}

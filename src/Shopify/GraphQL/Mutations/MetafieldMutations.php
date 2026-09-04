<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Metafield.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class MetafieldMutations extends BaseOperations
{
    /**
     * Creates a [`MetafieldDefinition`](https://shopify.dev/docs/api/admin-graphql/current/objects/MetafieldDefinition) that establishes structure and validation rules for metafields. The definition specifi
     *
     * @param array<string,mixed> $args Variaveis GraphQL: definition: MetafieldDefinitionInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metafieldDefinitionCreate(array $args = [], string $selection = 'createdDefinition { description id key metafieldsCount name namespace ownerType pinnedPosition useAsCollectionCondition validationStatus } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'metafieldDefinitionCreate', $args, ['definition' => 'MetafieldDefinitionInput!'], $selection);
    }

    /**
     * Deletes a [`MetafieldDefinition`](https://shopify.dev/docs/api/admin-graphql/current/objects/MetafieldDefinition). You can identify the definition by providing either its owner type, namespace, and ke
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID, identifier: MetafieldDefinitionIdentifierInput, deleteAllAssociatedMetafields: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metafieldDefinitionDelete(array $args = [], string $selection = 'deletedDefinition { key namespace ownerType } deletedDefinitionId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'metafieldDefinitionDelete', $args, ['id' => 'ID', 'identifier' => 'MetafieldDefinitionIdentifierInput', 'deleteAllAssociatedMetafields' => 'Boolean'], $selection);
    }

    /**
     * You can organize your metafields in your Shopify admin by pinning/unpinning metafield definitions. The order of your pinned metafield definitions determines the order in which your metafields are disp
     *
     * @param array<string,mixed> $args Variaveis GraphQL: definitionId: ID, identifier: MetafieldDefinitionIdentifierInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metafieldDefinitionPin(array $args = [], string $selection = 'pinnedDefinition { description id key metafieldsCount name namespace ownerType pinnedPosition useAsCollectionCondition validationStatus } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'metafieldDefinitionPin', $args, ['definitionId' => 'ID', 'identifier' => 'MetafieldDefinitionIdentifierInput'], $selection);
    }

    /**
     * You can organize your metafields in your Shopify admin by pinning/unpinning metafield definitions. The order of your pinned metafield definitions determines the order in which your metafields are disp
     *
     * @param array<string,mixed> $args Variaveis GraphQL: definitionId: ID, identifier: MetafieldDefinitionIdentifierInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metafieldDefinitionUnpin(array $args = [], string $selection = 'unpinnedDefinition { description id key metafieldsCount name namespace ownerType pinnedPosition useAsCollectionCondition validationStatus } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'metafieldDefinitionUnpin', $args, ['definitionId' => 'ID', 'identifier' => 'MetafieldDefinitionIdentifierInput'], $selection);
    }

    /**
     * Updates a [`MetafieldDefinition`](https://shopify.dev/docs/api/admin-graphql/current/objects/MetafieldDefinition)'s configuration and settings. You can modify the definition's name, description, valid
     *
     * @param array<string,mixed> $args Variaveis GraphQL: definition: MetafieldDefinitionUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metafieldDefinitionUpdate(array $args = [], string $selection = 'updatedDefinition { description id key metafieldsCount name namespace ownerType pinnedPosition useAsCollectionCondition validationStatus } validationJob { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'metafieldDefinitionUpdate', $args, ['definition' => 'MetafieldDefinitionUpdateInput!'], $selection);
    }

    /**
     * Deletes [`Metafield`](https://shopify.dev/docs/api/admin-graphql/current/objects/Metafield) objects in bulk by specifying combinations of owner ID, namespace, and key. Returns the identifiers of succ
     *
     * @param array<string,mixed> $args Variaveis GraphQL: metafields: [MetafieldIdentifierInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metafieldsDelete(array $args = [], string $selection = 'deletedMetafields { key namespace ownerId } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'metafieldsDelete', $args, ['metafields' => '[MetafieldIdentifierInput!]!'], $selection);
    }

    /**
     * Sets metafield values. Metafield values will be set regardless if they were previously created or not. Allows a maximum of 25 metafields to be set at a time, with a maximum total request payload size
     *
     * @param array<string,mixed> $args Variaveis GraphQL: metafields: [MetafieldsSetInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function metafieldsSet(array $args = [], string $selection = 'metafields { compareDigest createdAt description id jsonValue key legacyResourceId namespace ownerType sizeInBytes type updatedAt value } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'metafieldsSet', $args, ['metafields' => '[MetafieldsSetInput!]!'], $selection);
    }

    /**
     * Activates the specified standard metafield definition from its template. Refer to the [list of standard metafield definition templates](https://shopify.dev/apps/metafields/definitions/standard-defini
     *
     * @param array<string,mixed> $args Variaveis GraphQL: ownerType: MetafieldOwnerType!, id: ID, namespace: String, key: String, pin: Boolean, capabilities: MetafieldCapabilityCreateInput, access: StandardMetafieldDefinitionAccessInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function standardMetafieldDefinitionEnable(array $args = [], string $selection = 'createdDefinition { description id key metafieldsCount name namespace ownerType pinnedPosition useAsCollectionCondition validationStatus } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'standardMetafieldDefinitionEnable', $args, ['ownerType' => 'MetafieldOwnerType!', 'id' => 'ID', 'namespace' => 'String', 'key' => 'String', 'pin' => 'Boolean', 'capabilities' => 'MetafieldCapabilityCreateInput', 'access' => 'StandardMetafieldDefinitionAccessInput'], $selection);
    }
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Catalog.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class CatalogMutations extends BaseOperations
{
    /**
     * Modifies which contexts, like [markets](https://shopify.dev/docs/api/admin-graphql/latest/objects/Market) or B2B [company locations](https://shopify.dev/docs/api/admin-graphql/latest/objects/CompanyLo
     *
     * @param array<string,mixed> $args Variaveis GraphQL: catalogId: ID!, contextsToAdd: CatalogContextInput, contextsToRemove: CatalogContextInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function catalogContextUpdate(array $args = [], string $selection = 'catalog { id status title } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'catalogContextUpdate', $args, ['catalogId' => 'ID!', 'contextsToAdd' => 'CatalogContextInput', 'contextsToRemove' => 'CatalogContextInput'], $selection);
    }

    /**
     * Creates a [`Catalog`](https://shopify.dev/docs/api/admin-graphql/latest/interfaces/Catalog) that controls product availability and pricing for specific contexts like [markets](https://shopify.dev/docs
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: CatalogCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function catalogCreate(array $args = [], string $selection = 'catalog { id status title } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'catalogCreate', $args, ['input' => 'CatalogCreateInput!'], $selection);
    }

    /**
     * Delete a catalog.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, deleteDependentResources: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function catalogDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'catalogDelete', $args, ['id' => 'ID!', 'deleteDependentResources' => 'Boolean'], $selection);
    }

    /**
     * Updates an existing [catalog's](https://shopify.dev/docs/api/admin-graphql/latest/interfaces/Catalog) configuration. Catalogs control product publishing and pricing for specific contexts like [markets
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: CatalogUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function catalogUpdate(array $args = [], string $selection = 'catalog { id status title } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'catalogUpdate', $args, ['id' => 'ID!', 'input' => 'CatalogUpdateInput!'], $selection);
    }
}

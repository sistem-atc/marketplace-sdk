<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio SellingPlan.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class SellingPlanMutations extends BaseOperations
{
    /**
     * Adds multiple product variants to a selling plan group.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, productVariantIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function sellingPlanGroupAddProductVariants(array $args = [], string $selection = 'sellingPlanGroup { appId appliesToProduct appliesToProductVariant appliesToProductVariants createdAt description id merchantCode name options position summary } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'sellingPlanGroupAddProductVariants', $args, ['id' => 'ID!', 'productVariantIds' => '[ID!]!'], $selection);
    }

    /**
     * Adds multiple products to a selling plan group.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, productIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function sellingPlanGroupAddProducts(array $args = [], string $selection = 'sellingPlanGroup { appId appliesToProduct appliesToProductVariant appliesToProductVariants createdAt description id merchantCode name options position summary } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'sellingPlanGroupAddProducts', $args, ['id' => 'ID!', 'productIds' => '[ID!]!'], $selection);
    }

    /**
     * Creates a selling plan group that defines how products can be sold and purchased. A selling plan group represents a selling method such as "Subscribe and save", "Pre-order", or "Try before you buy" an
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: SellingPlanGroupInput!, resources: SellingPlanGroupResourceInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function sellingPlanGroupCreate(array $args = [], string $selection = 'sellingPlanGroup { appId appliesToProduct appliesToProductVariant appliesToProductVariants createdAt description id merchantCode name options position summary } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'sellingPlanGroupCreate', $args, ['input' => 'SellingPlanGroupInput!', 'resources' => 'SellingPlanGroupResourceInput'], $selection);
    }

    /**
     * Delete a Selling Plan Group. This does not affect subscription contracts.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function sellingPlanGroupDelete(array $args = [], string $selection = 'deletedSellingPlanGroupId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'sellingPlanGroupDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Removes multiple product variants from a selling plan group.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, productVariantIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function sellingPlanGroupRemoveProductVariants(array $args = [], string $selection = 'removedProductVariantIds userErrors { field message }'): array
    {
        return $this->execute('mutation', 'sellingPlanGroupRemoveProductVariants', $args, ['id' => 'ID!', 'productVariantIds' => '[ID!]!'], $selection);
    }

    /**
     * Removes multiple products from a selling plan group.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, productIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function sellingPlanGroupRemoveProducts(array $args = [], string $selection = 'removedProductIds userErrors { field message }'): array
    {
        return $this->execute('mutation', 'sellingPlanGroupRemoveProducts', $args, ['id' => 'ID!', 'productIds' => '[ID!]!'], $selection);
    }

    /**
     * Update a Selling Plan Group.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: SellingPlanGroupInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function sellingPlanGroupUpdate(array $args = [], string $selection = 'deletedSellingPlanIds sellingPlanGroup { appId appliesToProduct appliesToProductVariant appliesToProductVariants createdAt description id merchantCode name options position summary } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'sellingPlanGroupUpdate', $args, ['id' => 'ID!', 'input' => 'SellingPlanGroupInput!'], $selection);
    }
}

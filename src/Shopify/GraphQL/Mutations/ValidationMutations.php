<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Validation.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class ValidationMutations extends BaseOperations
{
    /**
     * Creates a cart and checkout validation: a server-side rule enforced before a customer can complete checkout. Each validation is powered by a cart and checkout validation function that you provide usin
     *
     * @param array<string,mixed> $args Variaveis GraphQL: validation: ValidationCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function validationCreate(array $args = [], string $selection = 'validation { blockOnFailure enabled id title } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'validationCreate', $args, ['validation' => 'ValidationCreateInput!'], $selection);
    }

    /**
     * Deletes a cart and checkout validation, removing its rule from the shop's checkout. Once deleted, its cart and checkout validation function no longer runs during checkout.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function validationDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'validationDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates a cart and checkout validation. Use `validationUpdate` to rename it, toggle whether it's enabled at checkout, change its `blockOnFailure` behavior, or update its metafields. Validation errors
     *
     * @param array<string,mixed> $args Variaveis GraphQL: validation: ValidationUpdateInput!, id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function validationUpdate(array $args = [], string $selection = 'validation { blockOnFailure enabled id title } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'validationUpdate', $args, ['validation' => 'ValidationUpdateInput!', 'id' => 'ID!'], $selection);
    }
}

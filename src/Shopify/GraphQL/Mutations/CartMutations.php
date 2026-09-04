<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Cart.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class CartMutations extends BaseOperations
{
    /**
     * Creates a cart transform function that lets merchants customize how products are bundled and presented during checkout. This gives merchants powerful control over their merchandising strategy by allow
     *
     * @param array<string,mixed> $args Variaveis GraphQL: functionHandle: String, blockOnFailure: Boolean, metafields: [MetafieldInput!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function cartTransformCreate(array $args = [], string $selection = 'cartTransform { blockOnFailure functionId id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'cartTransformCreate', $args, ['functionHandle' => 'String', 'blockOnFailure' => 'Boolean', 'metafields' => '[MetafieldInput!]'], $selection);
    }

    /**
     * Removes an existing cart transform function from the merchant's store, disabling any customized bundle or cart modification logic it provided. This mutation persistently deletes the transform configur
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function cartTransformDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'cartTransformDelete', $args, ['id' => 'ID!'], $selection);
    }
}

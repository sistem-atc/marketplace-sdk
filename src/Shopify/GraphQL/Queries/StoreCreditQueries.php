<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio StoreCredit.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class StoreCreditQueries extends BaseOperations
{
    /**
     * Retrieves a [`StoreCreditAccount`](https://shopify.dev/docs/api/admin-graphql/latest/objects/StoreCreditAccount) by ID. Store credit accounts hold monetary balances that account owners can use at chec
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function storeCreditAccount(array $args = [], string $selection = 'id'): array
    {
        return $this->execute('query', 'storeCreditAccount', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns the store credit configuration for a shop, including whether store credit is enabled for customers at checkout. Use this to display the current state of a merchant's store credit program or ch
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function storeCreditConfiguration(array $args = [], string $selection = 'storeCreditEnabled'): array
    {
        return $this->execute('query', 'storeCreditConfiguration', $args, [], $selection);
    }
}

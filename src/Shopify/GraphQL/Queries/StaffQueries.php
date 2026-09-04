<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Staff.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class StaffQueries extends BaseOperations
{
    /**
     * The staff member making the API request.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function currentStaffMember(array $args = [], string $selection = 'accountType active email exists firstName id initials isShopOwner lastName locale name phone'): array
    {
        return $this->execute('query', 'currentStaffMember', $args, [], $selection);
    }

    /**
     * Retrieves a [staff member](https://shopify.dev/docs/api/admin-graphql/latest/objects/StaffMember) by ID. If no ID is provided, the query returns the staff member that's making the request. A staff mem
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function staffMember(array $args = [], string $selection = 'accountType active email exists firstName id initials isShopOwner lastName locale name phone'): array
    {
        return $this->execute('query', 'staffMember', $args, ['id' => 'ID'], $selection);
    }

    /**
     * Returns a paginated list of [`StaffMember`](https://shopify.dev/docs/api/admin-graphql/latest/objects/StaffMember) objects for the shop. Staff members are users who can access the Shopify admin to man
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: StaffMembersSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function staffMembers(array $args = [], string $selection = 'edges { node { accountType active email exists firstName id initials isShopOwner lastName locale name phone } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'staffMembers', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'StaffMembersSortKeys', 'query' => 'String'], $selection);
    }
}

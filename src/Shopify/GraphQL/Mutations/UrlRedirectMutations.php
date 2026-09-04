<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio UrlRedirect.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class UrlRedirectMutations extends BaseOperations
{
    /**
     * Asynchronously delete [URL redirects](https://shopify.dev/api/admin-graphql/latest/objects/UrlRedirect) in bulk.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function urlRedirectBulkDeleteAll(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'urlRedirectBulkDeleteAll', $args, [], $selection);
    }

    /**
     * Asynchronously delete [URLRedirect](https://shopify.dev/api/admin-graphql/latest/objects/UrlRedirect) objects in bulk by IDs. Learn more about [URLRedirect](https://help.shopify.com/en/manual/online-
     *
     * @param array<string,mixed> $args Variaveis GraphQL: ids: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function urlRedirectBulkDeleteByIds(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'urlRedirectBulkDeleteByIds', $args, ['ids' => '[ID!]!'], $selection);
    }

    /**
     * Asynchronously delete redirects in bulk.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: savedSearchId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function urlRedirectBulkDeleteBySavedSearch(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'urlRedirectBulkDeleteBySavedSearch', $args, ['savedSearchId' => 'ID!'], $selection);
    }

    /**
     * Asynchronously delete redirects in bulk.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: search: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function urlRedirectBulkDeleteBySearch(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'urlRedirectBulkDeleteBySearch', $args, ['search' => 'String!'], $selection);
    }

    /**
     * Creates a [`UrlRedirect`](https://shopify.dev/api/admin-graphql/latest/objects/UrlRedirect) object.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: urlRedirect: UrlRedirectInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function urlRedirectCreate(array $args = [], string $selection = 'urlRedirect { id path target } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'urlRedirectCreate', $args, ['urlRedirect' => 'UrlRedirectInput!'], $selection);
    }

    /**
     * Deletes a [`UrlRedirect`](https://shopify.dev/api/admin-graphql/latest/objects/UrlRedirect) object.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function urlRedirectDelete(array $args = [], string $selection = 'deletedUrlRedirectId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'urlRedirectDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Creates a [`UrlRedirectImport`](https://shopify.dev/api/admin-graphql/latest/objects/UrlRedirectImport) object. After creating the `UrlRedirectImport` object, the `UrlRedirectImport` request can be p
     *
     * @param array<string,mixed> $args Variaveis GraphQL: url: URL!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function urlRedirectImportCreate(array $args = [], string $selection = 'urlRedirectImport { count createdCount failedCount finished finishedAt id updatedCount } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'urlRedirectImportCreate', $args, ['url' => 'URL!'], $selection);
    }

    /**
     * Submits a `UrlRedirectImport` request to be processed. The `UrlRedirectImport` request is first created with the [`urlRedirectImportCreate`](https://shopify.dev/api/admin-graphql/latest/mutations/url
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function urlRedirectImportSubmit(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'urlRedirectImportSubmit', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates a URL redirect.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, urlRedirect: UrlRedirectInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function urlRedirectUpdate(array $args = [], string $selection = 'urlRedirect { id path target } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'urlRedirectUpdate', $args, ['id' => 'ID!', 'urlRedirect' => 'UrlRedirectInput!'], $selection);
    }
}

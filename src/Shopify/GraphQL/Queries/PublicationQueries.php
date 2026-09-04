<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Publication.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class PublicationQueries extends BaseOperations
{
    /**
     * Retrieves a [`Publication`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Publication) by [`ID`](https://shopify.dev/docs/api/usage/gids). Returns `null` if the publication doesn't exist.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function publication(array $args = [], string $selection = 'autoPublish hasCollection id name supportsFuturePublishing'): array
    {
        return $this->execute('query', 'publication', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a paginated list of [`Publication`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Publication). Filter publications by [`CatalogType`](https://shopify.dev/docs/api/admin-graphql/l
     *
     * @param array<string,mixed> $args Variaveis GraphQL: catalogType: CatalogType, first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function publications(array $args = [], string $selection = 'edges { node { autoPublish hasCollection id name supportsFuturePublishing } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'publications', $args, ['catalogType' => 'CatalogType', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Count of publications. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: catalogType: CatalogType, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function publicationsCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'publicationsCount', $args, ['catalogType' => 'CatalogType', 'limit' => 'Int'], $selection);
    }

    /**
     * Returns a count of published products by publication ID. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: publicationId: ID!, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function publishedProductsCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'publishedProductsCount', $args, ['publicationId' => 'ID!', 'limit' => 'Int'], $selection);
    }
}

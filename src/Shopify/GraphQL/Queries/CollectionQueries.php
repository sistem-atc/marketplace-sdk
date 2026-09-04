<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Collection.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class CollectionQueries extends BaseOperations
{
    /**
     * Retrieves a [collection](https://shopify.dev/docs/api/admin-graphql/latest/objects/Collection) by its ID. A collection represents a grouping of [products](https://shopify.dev/docs/api/admin-graphql/la
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collection(array $args = [], string $selection = 'description descriptionHtml handle hasProduct id legacyResourceId publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication sortOrder storefrontId templateSuffix title updatedAt'): array
    {
        return $this->execute('query', 'collection', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Retrieves a collection by its unique handle identifier. Handles provide a URL-friendly way to reference collections and are commonly used in storefront URLs and navigation. For example, a collection
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: handle: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionByHandle(array $args = [], string $selection = 'description descriptionHtml handle hasProduct id legacyResourceId publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication sortOrder storefrontId templateSuffix title updatedAt'): array
    {
        return $this->execute('query', 'collectionByHandle', $args, ['handle' => 'String!'], $selection);
    }

    /**
     * Return a collection by an identifier.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: identifier: CollectionIdentifierInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionByIdentifier(array $args = [], string $selection = 'description descriptionHtml handle hasProduct id legacyResourceId publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication sortOrder storefrontId templateSuffix title updatedAt'): array
    {
        return $this->execute('query', 'collectionByIdentifier', $args, ['identifier' => 'CollectionIdentifierInput!'], $selection);
    }

    /**
     * Lists all metafield definitions that can be used to create collection conditions.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionConditionMetafieldDefinitions(array $args = [], string $selection = 'description id key name namespace ownerType'): array
    {
        return $this->execute('query', 'collectionConditionMetafieldDefinitions', $args, [], $selection);
    }

    /**
     * Returns the shareable collection sources owned by the given app for the shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: appId: ID!, first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionConditionsSources(array $args = [], string $selection = 'edges { node { description id shareable targetType title } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'collectionConditionsSources', $args, ['appId' => 'ID!', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Returns the apps that publish shareable collection sources for the shop, paginated.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionConditionsSourcesByApp(array $args = [], string $selection = 'edges { node { __typename } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'collectionConditionsSourcesByApp', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Lists all rules that can be used to create collections.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionRulesConditions(array $args = [], string $selection = 'allowedRelations defaultRelation ruleType'): array
    {
        return $this->execute('query', 'collectionRulesConditions', $args, [], $selection);
    }

    /**
     * Returns a list of the shop's collection saved searches.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionSavedSearches(array $args = [], string $selection = 'edges { node { id legacyResourceId name query resourceType searchTerms } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'collectionSavedSearches', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Retrieves a list of [collections](https://shopify.dev/docs/api/admin-graphql/latest/objects/Collection) in a store. Collections are groups of [products](https://shopify.dev/docs/api/admin-graphql/late
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: CollectionSortKeys, query: String, savedSearchId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collections(array $args = [], string $selection = 'edges { node { description descriptionHtml handle hasProduct id legacyResourceId publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication sortOrder storefrontId templateSuffix title updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'collections', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'CollectionSortKeys', 'query' => 'String', 'savedSearchId' => 'ID'], $selection);
    }

    /**
     * Count of collections. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String, savedSearchId: ID, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionsCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'collectionsCount', $args, ['query' => 'String', 'savedSearchId' => 'ID', 'limit' => 'Int'], $selection);
    }
}

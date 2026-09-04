<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Content.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class ContentQueries extends BaseOperations
{
    /**
     * Returns a `Article` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function article(array $args = [], string $selection = 'body createdAt defaultCursor handle id isPublished publishedAt summary tags templateSuffix title updatedAt'): array
    {
        return $this->execute('query', 'article', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * List of article authors for the shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function articleAuthors(array $args = [], string $selection = 'edges { node { name } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'articleAuthors', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * List of all article tags.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: sort: ArticleTagSort, limit: Int!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function articleTags(array $args = [], string $selection = ''): array
    {
        return $this->execute('query', 'articleTags', $args, ['sort' => 'ArticleTagSort', 'limit' => 'Int!'], $selection);
    }

    /**
     * Returns a paginated list of articles from the shop's blogs. [`Article`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Article) objects are blog posts that contain content like text, images
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: ArticleSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function articles(array $args = [], string $selection = 'edges { node { body createdAt defaultCursor handle id isPublished publishedAt summary tags templateSuffix title updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'articles', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'ArticleSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * Returns a `Blog` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function blog(array $args = [], string $selection = 'commentPolicy createdAt handle id tags templateSuffix title updatedAt'): array
    {
        return $this->execute('query', 'blog', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a paginated list of the shop's [`Blog`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Blog) objects. Blogs serve as containers for [`Article`](https://shopify.dev/docs/api/admin-gr
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: BlogSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function blogs(array $args = [], string $selection = 'edges { node { commentPolicy createdAt handle id tags templateSuffix title updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'blogs', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'BlogSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * Count of blogs. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function blogsCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'blogsCount', $args, ['query' => 'String', 'limit' => 'Int'], $selection);
    }

    /**
     * Returns a `Comment` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function comment(array $args = [], string $selection = 'body bodyHtml createdAt id ip isPublished publishedAt status updatedAt userAgent'): array
    {
        return $this->execute('query', 'comment', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * List of the shop's comments.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: CommentSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function comments(array $args = [], string $selection = 'edges { node { body bodyHtml createdAt id ip isPublished publishedAt status updatedAt userAgent } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'comments', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'CommentSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * Returns a `Menu` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function menu(array $args = [], string $selection = 'handle id isDefault title'): array
    {
        return $this->execute('query', 'menu', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Retrieves navigation menus. Menus organize content into hierarchical navigation structures that merchants can display in the online store (for example, in headers, footers, and sidebars) and customer
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: MenuSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function menus(array $args = [], string $selection = 'edges { node { handle id isDefault title } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'menus', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'MenuSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * The shop's online store channel.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function onlineStore(array $args = [], string $selection = '__typename'): array
    {
        return $this->execute('query', 'onlineStore', $args, [], $selection);
    }

    /**
     * Returns a `Page` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function page(array $args = [], string $selection = 'body bodySummary createdAt defaultCursor handle id isPublished publishedAt templateSuffix title updatedAt'): array
    {
        return $this->execute('query', 'page', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * A paginated list of pages from the online store. [`Page`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Page) objects are content pages that merchants create to provide information to cust
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: PageSortKeys, query: String, savedSearchId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pages(array $args = [], string $selection = 'edges { node { body bodySummary createdAt defaultCursor handle id isPublished publishedAt templateSuffix title updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'pages', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'PageSortKeys', 'query' => 'String', 'savedSearchId' => 'ID'], $selection);
    }

    /**
     * Count of pages. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pagesCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'pagesCount', $args, ['limit' => 'Int'], $selection);
    }

    /**
     * Returns an [`OnlineStoreTheme`](https://shopify.dev/docs/api/admin-graphql/latest/objects/OnlineStoreTheme) by its ID. Use this query to retrieve theme metadata and access the theme's [`files`](https:
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function theme(array $args = [], string $selection = 'createdAt id name prefix processing processingFailed role themeStoreId updatedAt'): array
    {
        return $this->execute('query', 'theme', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a paginated list of [`OnlineStoreTheme`](https://shopify.dev/docs/api/admin-graphql/latest/objects/OnlineStoreTheme) objects for the online store. Themes control the appearance and layout of t
     *
     * @param array<string,mixed> $args Variaveis GraphQL: roles: [ThemeRole!], names: [String!], first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function themes(array $args = [], string $selection = 'edges { node { createdAt id name prefix processing processingFailed role themeStoreId updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'themes', $args, ['roles' => '[ThemeRole!]', 'names' => '[String!]', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }
}

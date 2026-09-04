<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Content.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class ContentMutations extends BaseOperations
{
    /**
     * Creates an [`Article`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Article). Articles are content pieces that include a title, body text, and author information. You can publish the art
     *
     * @param array<string,mixed> $args Variaveis GraphQL: article: ArticleCreateInput!, blog: ArticleBlogInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function articleCreate(array $args = [], string $selection = 'article { body createdAt defaultCursor handle id isPublished publishedAt summary tags templateSuffix title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'articleCreate', $args, ['article' => 'ArticleCreateInput!', 'blog' => 'ArticleBlogInput'], $selection);
    }

    /**
     * Permanently deletes a blog article from a shop's blog. This mutation removes the article and all associated metadata. For example, when outdated product information or seasonal content needs removal,
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function articleDelete(array $args = [], string $selection = 'deletedArticleId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'articleDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates an existing [`Article`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Article). You can modify the article's content, metadata, publication status, and associated properties like a
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, article: ArticleUpdateInput!, blog: ArticleBlogInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function articleUpdate(array $args = [], string $selection = 'article { body createdAt defaultCursor handle id isPublished publishedAt summary tags templateSuffix title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'articleUpdate', $args, ['id' => 'ID!', 'article' => 'ArticleUpdateInput!', 'blog' => 'ArticleBlogInput'], $selection);
    }

    /**
     * Creates a new blog within a shop, establishing a container for organizing articles. For example, a fitness equipment retailer launching a wellness blog would use this mutation to create the blog, ena
     *
     * @param array<string,mixed> $args Variaveis GraphQL: blog: BlogCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function blogCreate(array $args = [], string $selection = 'blog { commentPolicy createdAt handle id tags templateSuffix title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'blogCreate', $args, ['blog' => 'BlogCreateInput!'], $selection);
    }

    /**
     * Permanently deletes a blog from a shop. This mutation removes the blog container and its organizational structure. For example, when consolidating multiple seasonal blogs into a single year-round con
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function blogDelete(array $args = [], string $selection = 'deletedBlogId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'blogDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates an existing blog's configuration and settings. This mutation allows merchants to modify blog properties to keep their content strategy current. For example, a merchant might update their blog
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, blog: BlogUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function blogUpdate(array $args = [], string $selection = 'blog { commentPolicy createdAt handle id tags templateSuffix title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'blogUpdate', $args, ['id' => 'ID!', 'blog' => 'BlogUpdateInput!'], $selection);
    }

    /**
     * Approves a pending comment, making it visible to store visitors on the associated blog article. For example, when a customer submits a question about a product in a blog post, merchants can approve t
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function commentApprove(array $args = [], string $selection = 'comment { body bodyHtml createdAt id ip isPublished publishedAt status updatedAt userAgent } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'commentApprove', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Permanently removes a comment from a blog article. For example, when a comment contains spam links or inappropriate language that violates store policies, merchants can delete it entirely. Use the `
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function commentDelete(array $args = [], string $selection = 'deletedCommentId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'commentDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Reverses a spam classification on a comment, restoring it to normal moderation status. This mutation allows merchants to change their decision when a comment has been manually marked as spam. For exa
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function commentNotSpam(array $args = [], string $selection = 'comment { body bodyHtml createdAt id ip isPublished publishedAt status updatedAt userAgent } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'commentNotSpam', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Marks a comment as spam, removing it from public view. This mutation enables merchants to quickly handle unwanted promotional content, malicious links, or other spam that appears in blog discussions.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function commentSpam(array $args = [], string $selection = 'comment { body bodyHtml createdAt id ip isPublished publishedAt status updatedAt userAgent } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'commentSpam', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Creates a navigation [`Menu`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Menu) for the online store. Menus organize links that help customers navigate to [collections](https://shopify.d
     *
     * @param array<string,mixed> $args Variaveis GraphQL: title: String!, handle: String!, items: [MenuItemCreateInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function menuCreate(array $args = [], string $selection = 'menu { handle id isDefault title } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'menuCreate', $args, ['title' => 'String!', 'handle' => 'String!', 'items' => '[MenuItemCreateInput!]!'], $selection);
    }

    /**
     * Deletes a menu.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function menuDelete(array $args = [], string $selection = 'deletedMenuId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'menuDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates a [`Menu`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Menu) for display on the storefront. Modifies the menu's title and navigation structure, including nested [`MenuItem`](http
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, title: String!, handle: String, items: [MenuItemUpdateInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function menuUpdate(array $args = [], string $selection = 'menu { handle id isDefault title } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'menuUpdate', $args, ['id' => 'ID!', 'title' => 'String!', 'handle' => 'String', 'items' => '[MenuItemUpdateInput!]!'], $selection);
    }

    /**
     * Creates a [`Page`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Page) for the online store. Pages contain custom content like "About Us" or "Contact" information that merchants display o
     *
     * @param array<string,mixed> $args Variaveis GraphQL: page: PageCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pageCreate(array $args = [], string $selection = 'page { body bodySummary createdAt defaultCursor handle id isPublished publishedAt templateSuffix title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'pageCreate', $args, ['page' => 'PageCreateInput!'], $selection);
    }

    /**
     * Permanently deletes a page from the online store. For example, merchants might delete seasonal landing pages after campaigns end, or remove outdated policy pages when terms change. Use the `pageDele
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pageDelete(array $args = [], string $selection = 'deletedPageId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'pageDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates an existing page's content and settings. For example, merchants can update their "Shipping Policy" page when rates change, or refresh their "About Us" page with new team information. Use the
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, page: PageUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pageUpdate(array $args = [], string $selection = 'page { body bodySummary createdAt defaultCursor handle id isPublished publishedAt templateSuffix title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'pageUpdate', $args, ['id' => 'ID!', 'page' => 'PageUpdateInput!'], $selection);
    }

    /**
     * Creates a theme from an external URL or staged upload. The theme source can either be a ZIP file hosted at a public URL or files previously uploaded using the [`stagedUploadsCreate`](https://shopify.d
     *
     * @param array<string,mixed> $args Variaveis GraphQL: source: URL!, name: String, role: ThemeRole
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function themeCreate(array $args = [], string $selection = 'theme { createdAt id name prefix processing processingFailed role themeStoreId updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'themeCreate', $args, ['source' => 'URL!', 'name' => 'String', 'role' => 'ThemeRole'], $selection);
    }

    /**
     * Deletes a theme.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function themeDelete(array $args = [], string $selection = 'deletedThemeId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'themeDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Duplicates a theme.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, name: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function themeDuplicate(array $args = [], string $selection = 'newTheme { createdAt id name prefix processing processingFailed role themeStoreId updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'themeDuplicate', $args, ['id' => 'ID!', 'name' => 'String'], $selection);
    }

    /**
     * Copy theme files. Copying to existing theme files will overwrite them.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: themeId: ID!, files: [ThemeFilesCopyFileInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function themeFilesCopy(array $args = [], string $selection = 'copiedThemeFiles { checksumMd5 createdAt filename size updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'themeFilesCopy', $args, ['themeId' => 'ID!', 'files' => '[ThemeFilesCopyFileInput!]!'], $selection);
    }

    /**
     * Deletes a theme's files.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: themeId: ID!, files: [String!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function themeFilesDelete(array $args = [], string $selection = 'deletedThemeFiles { checksumMd5 createdAt filename size updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'themeFilesDelete', $args, ['themeId' => 'ID!', 'files' => '[String!]!'], $selection);
    }

    /**
     * Creates or updates theme files in an online store theme. This mutation allows batch operations on multiple theme files, either creating new files or overwriting existing ones with the same filename.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: themeId: ID!, files: [OnlineStoreThemeFilesUpsertFileInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function themeFilesUpsert(array $args = [], string $selection = 'job { done id } upsertedThemeFiles { checksumMd5 createdAt filename size updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'themeFilesUpsert', $args, ['themeId' => 'ID!', 'files' => '[OnlineStoreThemeFilesUpsertFileInput!]!'], $selection);
    }

    /**
     * Publishes a theme.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function themePublish(array $args = [], string $selection = 'theme { createdAt id name prefix processing processingFailed role themeStoreId updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'themePublish', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates a theme.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: OnlineStoreThemeInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function themeUpdate(array $args = [], string $selection = 'theme { createdAt id name prefix processing processingFailed role themeStoreId updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'themeUpdate', $args, ['id' => 'ID!', 'input' => 'OnlineStoreThemeInput!'], $selection);
    }
}

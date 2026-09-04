<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Product.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class ProductQueries extends BaseOperations
{
    /**
     * Retrieves a [product](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) by its ID. A product is an item that a merchant can sell in their store. Use the `product` query when you need
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function product(array $args = [], string $selection = 'bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor'): array
    {
        return $this->execute('query', 'product', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Retrieves a [`Product`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) using its handle. A handle is a unique, URL-friendly string that Shopify automatically generates from the pro
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: handle: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productByHandle(array $args = [], string $selection = 'bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor'): array
    {
        return $this->execute('query', 'productByHandle', $args, ['handle' => 'String!'], $selection);
    }

    /**
     * Return a product by an identifier.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: identifier: ProductIdentifierInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productByIdentifier(array $args = [], string $selection = 'bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor'): array
    {
        return $this->execute('query', 'productByIdentifier', $args, ['identifier' => 'ProductIdentifierInput!'], $selection);
    }

    /**
     * Returns the product duplicate job.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productDuplicateJob(array $args = [], string $selection = 'done id'): array
    {
        return $this->execute('query', 'productDuplicateJob', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a ProductFeed resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productFeed(array $args = [], string $selection = 'channelId country id language status'): array
    {
        return $this->execute('query', 'productFeed', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * The product feeds for the shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productFeeds(array $args = [], string $selection = 'edges { node { channelId country id language status } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'productFeeds', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Returns a ProductOperation resource by ID. This can be used to query the [ProductSetOperation](https://shopify.dev/api/admin-graphql/current/objects/ProductSetOperation), using the ID that was return
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productOperation(array $args = [], string $selection = 'status'): array
    {
        return $this->execute('query', 'productOperation', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Retrieves product resource feedback for the currently authenticated app, providing insights into product data quality, completeness, and optimization opportunities. This feedback helps apps guide merc
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, channelId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productResourceFeedback(array $args = [], string $selection = 'feedbackGeneratedAt messages productId productUpdatedAt state'): array
    {
        return $this->execute('query', 'productResourceFeedback', $args, ['id' => 'ID!', 'channelId' => 'ID'], $selection);
    }

    /**
     * Returns a list of the shop's product saved searches.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productSavedSearches(array $args = [], string $selection = 'edges { node { id legacyResourceId name query resourceType searchTerms } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'productSavedSearches', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Returns tags added to [`Product`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) objects in the shop. Provides a paginated list of tag strings. The maximum page size is 5000 tags
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productTags(array $args = [], string $selection = 'edges { node { __typename } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'productTags', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Returns a paginated list of product types assigned to [products](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) in the store. The maximum page size is 1000. The maximum page size i
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productTypes(array $args = [], string $selection = 'edges { node { __typename } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'productTypes', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Retrieves a [product variant](https://shopify.dev/docs/api/admin-graphql/latest/objects/ProductVariant) by its ID. A product variant is a specific version of a [product](https://shopify.dev/docs/api/
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productVariant(array $args = [], string $selection = 'availableForSale barcode compareAtPrice createdAt defaultCursor displayName id inventoryPolicy inventoryQuantity legacyResourceId position price publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresComponents sellableOnlineQuantity sellingPlanGroupCount showUnitPrice sku storefrontId taxCode taxable title updatedAt'): array
    {
        return $this->execute('query', 'productVariant', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Return a product variant by an identifier.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: identifier: ProductVariantIdentifierInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productVariantByIdentifier(array $args = [], string $selection = 'availableForSale barcode compareAtPrice createdAt defaultCursor displayName id inventoryPolicy inventoryQuantity legacyResourceId position price publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresComponents sellableOnlineQuantity sellingPlanGroupCount showUnitPrice sku storefrontId taxCode taxable title updatedAt'): array
    {
        return $this->execute('query', 'productVariantByIdentifier', $args, ['identifier' => 'ProductVariantIdentifierInput!'], $selection);
    }

    /**
     * Retrieves a list of [product variants](https://shopify.dev/docs/api/admin-graphql/latest/objects/ProductVariant) associated with a [product](https://shopify.dev/docs/api/admin-graphql/latest/objects/P
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: ProductVariantSortKeys, query: String, savedSearchId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productVariants(array $args = [], string $selection = 'edges { node { availableForSale barcode compareAtPrice createdAt defaultCursor displayName id inventoryPolicy inventoryQuantity legacyResourceId position price publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresComponents sellableOnlineQuantity sellingPlanGroupCount showUnitPrice sku storefrontId taxCode taxable title updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'productVariants', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'ProductVariantSortKeys', 'query' => 'String', 'savedSearchId' => 'ID'], $selection);
    }

    /**
     * Count of product variants. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productVariantsCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'productVariantsCount', $args, ['query' => 'String', 'limit' => 'Int'], $selection);
    }

    /**
     * The list of vendors added to products. The maximum page size is 1000.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productVendors(array $args = [], string $selection = 'edges { node { __typename } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'productVendors', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Retrieves a list of [products](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) in a store. Products are the items that merchants can sell in their store. Use the `products` query w
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: ProductSortKeys, query: String, savedSearchId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function products(array $args = [], string $selection = 'edges { node { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'products', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'ProductSortKeys', 'query' => 'String', 'savedSearchId' => 'ID'], $selection);
    }

    /**
     * Count of products. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String, savedSearchId: ID, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productsCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'productsCount', $args, ['query' => 'String', 'savedSearchId' => 'ID', 'limit' => 'Int'], $selection);
    }
}

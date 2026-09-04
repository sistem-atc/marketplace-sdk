<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Product.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class ProductMutations extends BaseOperations
{
    /**
     * Creates a product bundle that groups multiple [`Product`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) objects together as components. The bundle appears as a single product in t
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: ProductBundleCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productBundleCreate(array $args = [], string $selection = 'productBundleOperation { id status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productBundleCreate', $args, ['input' => 'ProductBundleCreateInput!'], $selection);
    }

    /**
     * Updates a product bundle or componentized product.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: ProductBundleUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productBundleUpdate(array $args = [], string $selection = 'productBundleOperation { id status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productBundleUpdate', $args, ['input' => 'ProductBundleUpdateInput!'], $selection);
    }

    /**
     * Changes the status of a product. This allows you to set the availability of the product across all channels.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: productId: ID!, status: ProductStatus!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productChangeStatus(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productChangeStatus', $args, ['productId' => 'ID!', 'status' => 'ProductStatus!'], $selection);
    }

    /**
     * Creates a [product](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) with attributes such as title, description, vendor, and media. The `productCreate` mutation helps you create man
     *
     * @param array<string,mixed> $args Variaveis GraphQL: product: ProductCreateInput, media: [CreateMediaInput!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productCreate(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productCreate', $args, ['product' => 'ProductCreateInput', 'media' => '[CreateMediaInput!]'], $selection);
    }

    /**
     * Adds media files to a [`Product`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product), such as images, videos, or 3D models. Media files enhance product listings by providing visual rep
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: productId: ID!, media: [CreateMediaInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productCreateMedia(array $args = [], string $selection = 'media { alt id mediaContentType status } product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } mediaUserErrors { field message } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productCreateMedia', $args, ['productId' => 'ID!', 'media' => '[CreateMediaInput!]!'], $selection);
    }

    /**
     * Permanently deletes a product and all its associated data, including variants, media, publications, and inventory items. Use the `productDelete` mutation to programmatically remove products from your
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: ProductDeleteInput!, synchronous: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productDelete(array $args = [], string $selection = 'deletedProductId productDeleteOperation { deletedProductId id status } shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productDelete', $args, ['input' => 'ProductDeleteInput!', 'synchronous' => 'Boolean'], $selection);
    }

    /**
     * Deletes media from a [`Product`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product), such as images, videos, and 3D models. When you delete media images, the mutation also removes any
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: productId: ID!, mediaIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productDeleteMedia(array $args = [], string $selection = 'deletedMediaIds deletedProductImageIds product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } mediaUserErrors { field message } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productDeleteMedia', $args, ['productId' => 'ID!', 'mediaIds' => '[ID!]!'], $selection);
    }

    /**
     * Duplicates a product. If you need to duplicate a large product, such as one that has many [variants](https://shopify.dev/api/admin-graphql/latest/input-objects/ProductVariantInput) that are active at
     *
     * @param array<string,mixed> $args Variaveis GraphQL: productId: ID!, newTitle: String!, newStatus: ProductStatus, includeImages: Boolean, includeTranslations: Boolean, synchronous: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productDuplicate(array $args = [], string $selection = 'imageJob { done id } newProduct { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } productDuplicateOperation { id status } shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productDuplicate', $args, ['productId' => 'ID!', 'newTitle' => 'String!', 'newStatus' => 'ProductStatus', 'includeImages' => 'Boolean', 'includeTranslations' => 'Boolean', 'synchronous' => 'Boolean'], $selection);
    }

    /**
     * Creates a product feed for a specific publication.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: ProductFeedInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productFeedCreate(array $args = [], string $selection = 'productFeed { channelId country id language status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productFeedCreate', $args, ['input' => 'ProductFeedInput'], $selection);
    }

    /**
     * Deletes a product feed for a specific publication.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productFeedDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productFeedDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Runs the full product sync for a given shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: beforeUpdatedAt: DateTime, id: ID!, updatedAtSince: DateTime
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productFullSync(array $args = [], string $selection = 'id userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productFullSync', $args, ['beforeUpdatedAt' => 'DateTime', 'id' => 'ID!', 'updatedAtSince' => 'DateTime'], $selection);
    }

    /**
     * Adds multiple selling plan groups to a product.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, sellingPlanGroupIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productJoinSellingPlanGroups(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productJoinSellingPlanGroups', $args, ['id' => 'ID!', 'sellingPlanGroupIds' => '[ID!]!'], $selection);
    }

    /**
     * Removes multiple groups from a product.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, sellingPlanGroupIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productLeaveSellingPlanGroups(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productLeaveSellingPlanGroups', $args, ['id' => 'ID!', 'sellingPlanGroupIds' => '[ID!]!'], $selection);
    }

    /**
     * Updates an [option](https://shopify.dev/docs/api/admin-graphql/latest/objects/ProductOption) on a [product](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product), such as size, color, or
     *
     * @param array<string,mixed> $args Variaveis GraphQL: option: OptionUpdateInput!, productId: ID!, optionValuesToAdd: [OptionValueCreateInput!], optionValuesToUpdate: [OptionValueUpdateInput!], optionValuesToDelete: [ID!], variantStrategy: ProductOptionUpdateVariantStrategy
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productOptionUpdate(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productOptionUpdate', $args, ['option' => 'OptionUpdateInput!', 'productId' => 'ID!', 'optionValuesToAdd' => '[OptionValueCreateInput!]', 'optionValuesToUpdate' => '[OptionValueUpdateInput!]', 'optionValuesToDelete' => '[ID!]', 'variantStrategy' => 'ProductOptionUpdateVariantStrategy'], $selection);
    }

    /**
     * Creates one or more [options](https://shopify.dev/docs/api/admin-graphql/latest/objects/ProductOption) on a [product](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product), such as size,
     *
     * @param array<string,mixed> $args Variaveis GraphQL: productId: ID!, options: [OptionCreateInput!]!, variantStrategy: ProductOptionCreateVariantStrategy
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productOptionsCreate(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productOptionsCreate', $args, ['productId' => 'ID!', 'options' => '[OptionCreateInput!]!', 'variantStrategy' => 'ProductOptionCreateVariantStrategy'], $selection);
    }

    /**
     * Deletes one or more [options](https://shopify.dev/docs/api/admin-graphql/latest/objects/ProductOption) from a [product](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product). Product opti
     *
     * @param array<string,mixed> $args Variaveis GraphQL: productId: ID!, options: [ID!]!, strategy: ProductOptionDeleteStrategy
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productOptionsDelete(array $args = [], string $selection = 'deletedOptionsIds product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productOptionsDelete', $args, ['productId' => 'ID!', 'options' => '[ID!]!', 'strategy' => 'ProductOptionDeleteStrategy'], $selection);
    }

    /**
     * Reorders the [options](https://shopify.dev/docs/api/admin-graphql/latest/objects/ProductOption) and [option values](https://shopify.dev/docs/api/admin-graphql/latest/objects/ProductOptionValue) on a [
     *
     * @param array<string,mixed> $args Variaveis GraphQL: productId: ID!, options: [OptionReorderInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productOptionsReorder(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productOptionsReorder', $args, ['productId' => 'ID!', 'options' => '[OptionReorderInput!]!'], $selection);
    }

    /**
     * Publishes a [`Product`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) to specified [`Publication`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Publication) objects.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: input: ProductPublishInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productPublish(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } productPublications { isPublished publishDate } shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productPublish', $args, ['input' => 'ProductPublishInput!'], $selection);
    }

    /**
     * Reorders [media](https://shopify.dev/docs/api/admin-graphql/latest/interfaces/Media) attached to a product, changing their sequence in product displays. The operation processes asynchronously to handl
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, moves: [MoveInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productReorderMedia(array $args = [], string $selection = 'job { done id } mediaUserErrors { field message } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productReorderMedia', $args, ['id' => 'ID!', 'moves' => '[MoveInput!]!'], $selection);
    }

    /**
     * Performs multiple operations to create or update products in a single request. Use the `productSet` mutation to sync information from an external data source into Shopify, manage large product catalo
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: ProductSetInput!, synchronous: Boolean, identifier: ProductSetIdentifiers
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productSet(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } productSetOperation { id status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productSet', $args, ['input' => 'ProductSetInput!', 'synchronous' => 'Boolean', 'identifier' => 'ProductSetIdentifiers'], $selection);
    }

    /**
     * Unpublishes a product.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: input: ProductUnpublishInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productUnpublish(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productUnpublish', $args, ['input' => 'ProductUnpublishInput!'], $selection);
    }

    /**
     * Updates a [product](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) with attributes such as title, description, vendor, and media. The `productUpdate` mutation helps you modify man
     *
     * @param array<string,mixed> $args Variaveis GraphQL: product: ProductUpdateInput, media: [CreateMediaInput!], identifier: ProductUpdateIdentifiers
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productUpdate(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productUpdate', $args, ['product' => 'ProductUpdateInput', 'media' => '[CreateMediaInput!]', 'identifier' => 'ProductUpdateIdentifiers'], $selection);
    }

    /**
     * Updates properties of media attached to a [`Product`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product). You can modify alt text for accessibility or change preview images for existin
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: productId: ID!, media: [UpdateMediaInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productUpdateMedia(array $args = [], string $selection = 'media { alt id mediaContentType status } product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } mediaUserErrors { field message } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productUpdateMedia', $args, ['productId' => 'ID!', 'media' => '[UpdateMediaInput!]!'], $selection);
    }

    /**
     * Appends existing media from a product to specific variants of that product, creating associations between media files and particular product options. This allows different variants to showcase relevan
     *
     * @param array<string,mixed> $args Variaveis GraphQL: productId: ID!, variantMedia: [ProductVariantAppendMediaInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productVariantAppendMedia(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } productVariants { availableForSale barcode compareAtPrice createdAt defaultCursor displayName id inventoryPolicy inventoryQuantity legacyResourceId position price publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresComponents sellableOnlineQuantity sellingPlanGroupCount showUnitPrice sku storefrontId taxCode taxable title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productVariantAppendMedia', $args, ['productId' => 'ID!', 'variantMedia' => '[ProductVariantAppendMediaInput!]!'], $selection);
    }

    /**
     * Detaches media from product variants.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: productId: ID!, variantMedia: [ProductVariantDetachMediaInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productVariantDetachMedia(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } productVariants { availableForSale barcode compareAtPrice createdAt defaultCursor displayName id inventoryPolicy inventoryQuantity legacyResourceId position price publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresComponents sellableOnlineQuantity sellingPlanGroupCount showUnitPrice sku storefrontId taxCode taxable title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productVariantDetachMedia', $args, ['productId' => 'ID!', 'variantMedia' => '[ProductVariantDetachMediaInput!]!'], $selection);
    }

    /**
     * Adds multiple selling plan groups to a product variant.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, sellingPlanGroupIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productVariantJoinSellingPlanGroups(array $args = [], string $selection = 'productVariant { availableForSale barcode compareAtPrice createdAt defaultCursor displayName id inventoryPolicy inventoryQuantity legacyResourceId position price publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresComponents sellableOnlineQuantity sellingPlanGroupCount showUnitPrice sku storefrontId taxCode taxable title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productVariantJoinSellingPlanGroups', $args, ['id' => 'ID!', 'sellingPlanGroupIds' => '[ID!]!'], $selection);
    }

    /**
     * Remove multiple groups from a product variant.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, sellingPlanGroupIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productVariantLeaveSellingPlanGroups(array $args = [], string $selection = 'productVariant { availableForSale barcode compareAtPrice createdAt defaultCursor displayName id inventoryPolicy inventoryQuantity legacyResourceId position price publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresComponents sellableOnlineQuantity sellingPlanGroupCount showUnitPrice sku storefrontId taxCode taxable title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productVariantLeaveSellingPlanGroups', $args, ['id' => 'ID!', 'sellingPlanGroupIds' => '[ID!]!'], $selection);
    }

    /**
     * Creates new bundles, updates component quantities in existing bundles, and removes bundle components for one or multiple [`ProductVariant`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Pr
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: [ProductVariantRelationshipUpdateInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productVariantRelationshipBulkUpdate(array $args = [], string $selection = 'parentProductVariants { availableForSale barcode compareAtPrice createdAt defaultCursor displayName id inventoryPolicy inventoryQuantity legacyResourceId position price publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresComponents sellableOnlineQuantity sellingPlanGroupCount showUnitPrice sku storefrontId taxCode taxable title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productVariantRelationshipBulkUpdate', $args, ['input' => '[ProductVariantRelationshipUpdateInput!]!'], $selection);
    }

    /**
     * Creates multiple [product variants](https://shopify.dev/docs/api/admin-graphql/latest/objects/ProductVariant) for a single [product](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product)
     *
     * @param array<string,mixed> $args Variaveis GraphQL: variants: [ProductVariantsBulkInput!]!, productId: ID!, media: [CreateMediaInput!], strategy: ProductVariantsBulkCreateStrategy
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productVariantsBulkCreate(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } productVariants { availableForSale barcode compareAtPrice createdAt defaultCursor displayName id inventoryPolicy inventoryQuantity legacyResourceId position price publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresComponents sellableOnlineQuantity sellingPlanGroupCount showUnitPrice sku storefrontId taxCode taxable title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productVariantsBulkCreate', $args, ['variants' => '[ProductVariantsBulkInput!]!', 'productId' => 'ID!', 'media' => '[CreateMediaInput!]', 'strategy' => 'ProductVariantsBulkCreateStrategy'], $selection);
    }

    /**
     * Deletes multiple variants in a single [`Product`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product). Specify the product ID and an array of variant IDs to remove variants in bulk. You
     *
     * @param array<string,mixed> $args Variaveis GraphQL: variantsIds: [ID!]!, productId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productVariantsBulkDelete(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productVariantsBulkDelete', $args, ['variantsIds' => '[ID!]!', 'productId' => 'ID!'], $selection);
    }

    /**
     * Reorders multiple variants in a single product. This mutation can be called directly or via the bulkOperation.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: productId: ID!, positions: [ProductVariantPositionInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productVariantsBulkReorder(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productVariantsBulkReorder', $args, ['productId' => 'ID!', 'positions' => '[ProductVariantPositionInput!]!'], $selection);
    }

    /**
     * Updates multiple [product variants](https://shopify.dev/docs/api/admin-graphql/latest/objects/ProductVariant) for a single [product](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product)
     *
     * @param array<string,mixed> $args Variaveis GraphQL: variants: [ProductVariantsBulkInput!]!, productId: ID!, media: [CreateMediaInput!], allowPartialUpdates: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function productVariantsBulkUpdate(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } productVariants { availableForSale barcode compareAtPrice createdAt defaultCursor displayName id inventoryPolicy inventoryQuantity legacyResourceId position price publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresComponents sellableOnlineQuantity sellingPlanGroupCount showUnitPrice sku storefrontId taxCode taxable title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'productVariantsBulkUpdate', $args, ['variants' => '[ProductVariantsBulkInput!]!', 'productId' => 'ID!', 'media' => '[CreateMediaInput!]', 'allowPartialUpdates' => 'Boolean'], $selection);
    }
}

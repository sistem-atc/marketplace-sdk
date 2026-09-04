<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Price.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class PriceMutations extends BaseOperations
{
    /**
     * Creates a [`PriceList`](https://shopify.dev/docs/api/admin-graphql/latest/objects/PriceList). Price lists enable contextual pricing by defining fixed prices or percentage-based adjustments. The price
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: PriceListCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function priceListCreate(array $args = [], string $selection = 'priceList { currency fixedPricesCount id name } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'priceListCreate', $args, ['input' => 'PriceListCreateInput!'], $selection);
    }

    /**
     * Deletes a price list. For example, you can delete a price list so that it no longer applies for products in the associated market.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function priceListDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'priceListDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Creates or updates fixed prices on a [`PriceList`](https://shopify.dev/docs/api/admin-graphql/latest/objects/PriceList). Use this mutation to set specific prices for [`ProductVariant`](https://shopify
     *
     * @param array<string,mixed> $args Variaveis GraphQL: priceListId: ID!, prices: [PriceListPriceInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function priceListFixedPricesAdd(array $args = [], string $selection = 'prices { originType } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'priceListFixedPricesAdd', $args, ['priceListId' => 'ID!', 'prices' => '[PriceListPriceInput!]!'], $selection);
    }

    /**
     * Sets or removes fixed prices for all variants of a [`Product`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) on a [`PriceList`](https://shopify.dev/docs/api/admin-graphql/latest/o
     *
     * @param array<string,mixed> $args Variaveis GraphQL: pricesToAdd: [PriceListProductPriceInput!], pricesToDeleteByProductIds: [ID!], priceListId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function priceListFixedPricesByProductUpdate(array $args = [], string $selection = 'priceList { currency fixedPricesCount id name } pricesToAddProducts { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } pricesToDeleteProducts { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'priceListFixedPricesByProductUpdate', $args, ['pricesToAdd' => '[PriceListProductPriceInput!]', 'pricesToDeleteByProductIds' => '[ID!]', 'priceListId' => 'ID!'], $selection);
    }

    /**
     * Deletes specific fixed prices from a price list using a product variant ID. You can use the `priceListFixedPricesDelete` mutation to delete a set of fixed prices from a price list. After deleting the
     *
     * @param array<string,mixed> $args Variaveis GraphQL: priceListId: ID!, variantIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function priceListFixedPricesDelete(array $args = [], string $selection = 'deletedFixedPriceVariantIds userErrors { field message }'): array
    {
        return $this->execute('mutation', 'priceListFixedPricesDelete', $args, ['priceListId' => 'ID!', 'variantIds' => '[ID!]!'], $selection);
    }

    /**
     * Updates fixed prices on a [`PriceList`](https://shopify.dev/docs/api/admin-graphql/latest/objects/PriceList). This mutation lets you add new fixed prices for specific [`ProductVariant`](https://shopif
     *
     * @param array<string,mixed> $args Variaveis GraphQL: priceListId: ID!, pricesToAdd: [PriceListPriceInput!]!, variantIdsToDelete: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function priceListFixedPricesUpdate(array $args = [], string $selection = 'deletedFixedPriceVariantIds priceList { currency fixedPricesCount id name } pricesAdded { originType } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'priceListFixedPricesUpdate', $args, ['priceListId' => 'ID!', 'pricesToAdd' => '[PriceListPriceInput!]!', 'variantIdsToDelete' => '[ID!]!'], $selection);
    }

    /**
     * Updates a [`PriceList`](https://shopify.dev/docs/api/admin-graphql/latest/objects/PriceList)'s configuration, including its name, currency, [`Catalog`](https://shopify.dev/docs/api/admin-graphql/lates
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: PriceListUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function priceListUpdate(array $args = [], string $selection = 'priceList { currency fixedPricesCount id name } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'priceListUpdate', $args, ['id' => 'ID!', 'input' => 'PriceListUpdateInput!'], $selection);
    }

    /**
     * Updates quantity pricing on a [`PriceList`](https://shopify.dev/docs/api/admin-graphql/latest/objects/PriceList) for specific [`ProductVariant`](https://shopify.dev/docs/api/admin-graphql/latest/objec
     *
     * @param array<string,mixed> $args Variaveis GraphQL: priceListId: ID!, input: QuantityPricingByVariantUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function quantityPricingByVariantUpdate(array $args = [], string $selection = 'productVariants { availableForSale barcode compareAtPrice createdAt defaultCursor displayName id inventoryPolicy inventoryQuantity legacyResourceId position price publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresComponents sellableOnlineQuantity sellingPlanGroupCount showUnitPrice sku storefrontId taxCode taxable title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'quantityPricingByVariantUpdate', $args, ['priceListId' => 'ID!', 'input' => 'QuantityPricingByVariantUpdateInput!'], $selection);
    }

    /**
     * Creates or updates existing quantity rules on a price list. You can use the `quantityRulesAdd` mutation to set order level minimums, maximumums and increments for specific product variants.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: priceListId: ID!, quantityRules: [QuantityRuleInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function quantityRulesAdd(array $args = [], string $selection = 'quantityRules { increment isDefault maximum minimum originType } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'quantityRulesAdd', $args, ['priceListId' => 'ID!', 'quantityRules' => '[QuantityRuleInput!]!'], $selection);
    }

    /**
     * Deletes specific quantity rules from a price list using a product variant ID. You can use the `quantityRulesDelete` mutation to delete a set of quantity rules from a price list.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: priceListId: ID!, variantIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function quantityRulesDelete(array $args = [], string $selection = 'deletedQuantityRulesVariantIds userErrors { field message }'): array
    {
        return $this->execute('mutation', 'quantityRulesDelete', $args, ['priceListId' => 'ID!', 'variantIds' => '[ID!]!'], $selection);
    }
}

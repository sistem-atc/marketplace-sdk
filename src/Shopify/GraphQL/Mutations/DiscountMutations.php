<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Discount.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class DiscountMutations extends BaseOperations
{
    /**
     * Activates an automatic discount.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountAutomaticActivate(array $args = [], string $selection = 'automaticDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountAutomaticActivate', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Creates an automatic discount that's managed by an app. Use this mutation with [Shopify Functions](https://shopify.dev/docs/apps/build/functions) when you need advanced, custom, or dynamic discount ca
     *
     * @param array<string,mixed> $args Variaveis GraphQL: automaticAppDiscount: DiscountAutomaticAppInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountAutomaticAppCreate(array $args = [], string $selection = 'automaticAppDiscount { appliesOnOneTimePurchase appliesOnSubscription asyncUsageCount createdAt discountClass discountClasses discountId endsAt recurringCycleLimit startsAt status tags title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountAutomaticAppCreate', $args, ['automaticAppDiscount' => 'DiscountAutomaticAppInput!'], $selection);
    }

    /**
     * Updates an existing automatic discount that's managed by an app using [Shopify Functions](https://shopify.dev/docs/apps/build/functions). Use this mutation when you need advanced, custom, or dynamic d
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, automaticAppDiscount: DiscountAutomaticAppInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountAutomaticAppUpdate(array $args = [], string $selection = 'automaticAppDiscount { appliesOnOneTimePurchase appliesOnSubscription asyncUsageCount createdAt discountClass discountClasses discountId endsAt recurringCycleLimit startsAt status tags title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountAutomaticAppUpdate', $args, ['id' => 'ID!', 'automaticAppDiscount' => 'DiscountAutomaticAppInput!'], $selection);
    }

    /**
     * Creates an [amount off discount](https://help.shopify.com/manual/discounts/discount-types/percentage-fixed-amount) that's automatically applied on a cart and at checkout. > Note: > To create code dis
     *
     * @param array<string,mixed> $args Variaveis GraphQL: automaticBasicDiscount: DiscountAutomaticBasicInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountAutomaticBasicCreate(array $args = [], string $selection = 'automaticDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountAutomaticBasicCreate', $args, ['automaticBasicDiscount' => 'DiscountAutomaticBasicInput!'], $selection);
    }

    /**
     * Updates an existing [amount off discount](https://help.shopify.com/manual/discounts/discount-types/percentage-fixed-amount) that's automatically applied on a cart and at checkout. > Note: > To update
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, automaticBasicDiscount: DiscountAutomaticBasicInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountAutomaticBasicUpdate(array $args = [], string $selection = 'automaticDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountAutomaticBasicUpdate', $args, ['id' => 'ID!', 'automaticBasicDiscount' => 'DiscountAutomaticBasicInput!'], $selection);
    }

    /**
     * Deletes multiple automatic discounts in a single operation, providing efficient bulk management for stores with extensive discount catalogs. This mutation processes deletions asynchronously to handle
     *
     * @param array<string,mixed> $args Variaveis GraphQL: search: String, savedSearchId: ID, ids: [ID!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountAutomaticBulkDelete(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountAutomaticBulkDelete', $args, ['search' => 'String', 'savedSearchId' => 'ID', 'ids' => '[ID!]'], $selection);
    }

    /**
     * Creates a [buy X get Y discount (BXGY)](https://help.shopify.com/manual/discounts/discount-types/buy-x-get-y) that's automatically applied on a cart and at checkout. > Note: > To create code discount
     *
     * @param array<string,mixed> $args Variaveis GraphQL: automaticBxgyDiscount: DiscountAutomaticBxgyInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountAutomaticBxgyCreate(array $args = [], string $selection = 'automaticDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountAutomaticBxgyCreate', $args, ['automaticBxgyDiscount' => 'DiscountAutomaticBxgyInput!'], $selection);
    }

    /**
     * Updates an existing [buy X get Y discount (BXGY)](https://help.shopify.com/manual/discounts/discount-types/buy-x-get-y) that's automatically applied on a cart and at checkout. > Note: > To update cod
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, automaticBxgyDiscount: DiscountAutomaticBxgyInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountAutomaticBxgyUpdate(array $args = [], string $selection = 'automaticDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountAutomaticBxgyUpdate', $args, ['id' => 'ID!', 'automaticBxgyDiscount' => 'DiscountAutomaticBxgyInput!'], $selection);
    }

    /**
     * Deactivates an automatic discount.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountAutomaticDeactivate(array $args = [], string $selection = 'automaticDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountAutomaticDeactivate', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Deletes an existing automatic discount from the store, permanently removing it from all future order calculations. This mutation provides a clean way to remove promotional campaigns that are no longer
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountAutomaticDelete(array $args = [], string $selection = 'deletedAutomaticDiscountId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountAutomaticDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Creates automatic free shipping discounts that apply to qualifying orders without requiring discount codes. These promotions automatically activate when customers meet specified criteria, streamlining
     *
     * @param array<string,mixed> $args Variaveis GraphQL: freeShippingAutomaticDiscount: DiscountAutomaticFreeShippingInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountAutomaticFreeShippingCreate(array $args = [], string $selection = 'automaticDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountAutomaticFreeShippingCreate', $args, ['freeShippingAutomaticDiscount' => 'DiscountAutomaticFreeShippingInput!'], $selection);
    }

    /**
     * Updates existing automatic free shipping discounts, allowing merchants to modify promotion criteria, shipping destinations, and eligibility requirements without recreating the entire discount structur
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, freeShippingAutomaticDiscount: DiscountAutomaticFreeShippingInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountAutomaticFreeShippingUpdate(array $args = [], string $selection = 'automaticDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountAutomaticFreeShippingUpdate', $args, ['id' => 'ID!', 'freeShippingAutomaticDiscount' => 'DiscountAutomaticFreeShippingInput!'], $selection);
    }

    /**
     * Adds tags to multiple [discounts](https://help.shopify.com/manual/discounts/discount-types) asynchronously using one of the following: - A search query - A saved search ID - A list of discount IDs Fo
     *
     * @param array<string,mixed> $args Variaveis GraphQL: search: String, savedSearchId: ID, ids: [ID!], tags: [String!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountBulkTagsAdd(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountBulkTagsAdd', $args, ['search' => 'String', 'savedSearchId' => 'ID', 'ids' => '[ID!]', 'tags' => '[String!]!'], $selection);
    }

    /**
     * Removes tags from multiple [discounts](https://help.shopify.com/manual/discounts/discount-types) asynchronously using one of the following: - A search query - A saved search ID - A list of discount ID
     *
     * @param array<string,mixed> $args Variaveis GraphQL: search: String, savedSearchId: ID, ids: [ID!], tags: [String!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountBulkTagsRemove(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountBulkTagsRemove', $args, ['search' => 'String', 'savedSearchId' => 'ID', 'ids' => '[ID!]', 'tags' => '[String!]!'], $selection);
    }

    /**
     * Activates a previously created code discount, making it available for customers to use during checkout. This mutation transitions inactive discount codes into an active state where they can be applied
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodeActivate(array $args = [], string $selection = 'codeDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountCodeActivate', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Creates a code discount. The discount type must be provided by an app extension that uses [Shopify Functions](https://shopify.dev/docs/apps/build/functions). Functions can implement [order](https://sh
     *
     * @param array<string,mixed> $args Variaveis GraphQL: codeAppDiscount: DiscountCodeAppInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodeAppCreate(array $args = [], string $selection = 'codeAppDiscount { appliesOnOneTimePurchase appliesOnSubscription appliesOncePerCustomer asyncUsageCount createdAt discountClass discountClasses discountId endsAt hasTimelineComment recurringCycleLimit startsAt status tags title updatedAt usageLimit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountCodeAppCreate', $args, ['codeAppDiscount' => 'DiscountCodeAppInput!'], $selection);
    }

    /**
     * Updates a code discount, where the discount type is provided by an app extension that uses [Shopify Functions](https://shopify.dev/docs/apps/build/functions). Use this mutation when you need advanced,
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, codeAppDiscount: DiscountCodeAppInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodeAppUpdate(array $args = [], string $selection = 'codeAppDiscount { appliesOnOneTimePurchase appliesOnSubscription appliesOncePerCustomer asyncUsageCount createdAt discountClass discountClasses discountId endsAt hasTimelineComment recurringCycleLimit startsAt status tags title updatedAt usageLimit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountCodeAppUpdate', $args, ['id' => 'ID!', 'codeAppDiscount' => 'DiscountCodeAppInput!'], $selection);
    }

    /**
     * Creates an [amount off discount](https://help.shopify.com/manual/discounts/discount-types/percentage-fixed-amount) that's applied on a cart and at checkout when a customer enters a code. Amount off di
     *
     * @param array<string,mixed> $args Variaveis GraphQL: basicCodeDiscount: DiscountCodeBasicInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodeBasicCreate(array $args = [], string $selection = 'codeDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountCodeBasicCreate', $args, ['basicCodeDiscount' => 'DiscountCodeBasicInput!'], $selection);
    }

    /**
     * Updates an [amount off discount](https://help.shopify.com/manual/discounts/discount-types/percentage-fixed-amount) that's applied on a cart and at checkout when a customer enters a code. Amount off di
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, basicCodeDiscount: DiscountCodeBasicInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodeBasicUpdate(array $args = [], string $selection = 'codeDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountCodeBasicUpdate', $args, ['id' => 'ID!', 'basicCodeDiscount' => 'DiscountCodeBasicInput!'], $selection);
    }

    /**
     * Activates multiple [code discounts](https://help.shopify.com/manual/discounts/discount-types#discount-codes) asynchronously using one of the following: - A search query - A saved search ID - A list of
     *
     * @param array<string,mixed> $args Variaveis GraphQL: search: String, savedSearchId: ID, ids: [ID!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodeBulkActivate(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountCodeBulkActivate', $args, ['search' => 'String', 'savedSearchId' => 'ID', 'ids' => '[ID!]'], $selection);
    }

    /**
     * Deactivates multiple [code-based discounts](https://help.shopify.com/manual/discounts/discount-types#discount-codes) asynchronously using one of the following: - A search query - A saved search ID - A
     *
     * @param array<string,mixed> $args Variaveis GraphQL: search: String, savedSearchId: ID, ids: [ID!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodeBulkDeactivate(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountCodeBulkDeactivate', $args, ['search' => 'String', 'savedSearchId' => 'ID', 'ids' => '[ID!]'], $selection);
    }

    /**
     * Deletes multiple [code-based discounts](https://help.shopify.com/manual/discounts/discount-types#discount-codes) asynchronously using one of the following: - A search query - A saved search ID - A lis
     *
     * @param array<string,mixed> $args Variaveis GraphQL: search: String, savedSearchId: ID, ids: [ID!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodeBulkDelete(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountCodeBulkDelete', $args, ['search' => 'String', 'savedSearchId' => 'ID', 'ids' => '[ID!]'], $selection);
    }

    /**
     * Creates a [buy X get Y discount (BXGY)](https://help.shopify.com/manual/discounts/discount-types/buy-x-get-y) that's applied on a cart and at checkout when a customer enters a code. > Note: > To crea
     *
     * @param array<string,mixed> $args Variaveis GraphQL: bxgyCodeDiscount: DiscountCodeBxgyInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodeBxgyCreate(array $args = [], string $selection = 'codeDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountCodeBxgyCreate', $args, ['bxgyCodeDiscount' => 'DiscountCodeBxgyInput!'], $selection);
    }

    /**
     * Updates a [buy X get Y discount (BXGY)](https://help.shopify.com/manual/discounts/discount-types/buy-x-get-y) that's applied on a cart and at checkout when a customer enters a code. > Note: > To upda
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, bxgyCodeDiscount: DiscountCodeBxgyInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodeBxgyUpdate(array $args = [], string $selection = 'codeDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountCodeBxgyUpdate', $args, ['id' => 'ID!', 'bxgyCodeDiscount' => 'DiscountCodeBxgyInput!'], $selection);
    }

    /**
     * Temporarily suspends a code discount without permanently removing it from the store. Deactivation allows merchants to pause promotional campaigns while preserving the discount configuration for potent
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodeDeactivate(array $args = [], string $selection = 'codeDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountCodeDeactivate', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Removes a code discount from the store, making it permanently unavailable for customer use. This mutation provides a clean way to eliminate discount codes that are no longer needed or have been replac
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodeDelete(array $args = [], string $selection = 'deletedCodeDiscountId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountCodeDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Creates an [free shipping discount](https://help.shopify.com/manual/discounts/discount-types/free-shipping) that's applied on a cart and at checkout when a customer enters a code. > Note: > To create
     *
     * @param array<string,mixed> $args Variaveis GraphQL: freeShippingCodeDiscount: DiscountCodeFreeShippingInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodeFreeShippingCreate(array $args = [], string $selection = 'codeDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountCodeFreeShippingCreate', $args, ['freeShippingCodeDiscount' => 'DiscountCodeFreeShippingInput!'], $selection);
    }

    /**
     * Updates a [free shipping discount](https://help.shopify.com/manual/discounts/discount-types/free-shipping) that's applied on a cart and at checkout when a customer enters a code. > Note: > To update
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, freeShippingCodeDiscount: DiscountCodeFreeShippingInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodeFreeShippingUpdate(array $args = [], string $selection = 'codeDiscountNode { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountCodeFreeShippingUpdate', $args, ['id' => 'ID!', 'freeShippingCodeDiscount' => 'DiscountCodeFreeShippingInput!'], $selection);
    }

    /**
     * Asynchronously delete [discount codes](https://help.shopify.com/manual/discounts/discount-types#discount-codes) in bulk that customers can use to redeem a discount.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: discountId: ID!, search: String, savedSearchId: ID, ids: [ID!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountCodeRedeemCodeBulkDelete(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountCodeRedeemCodeBulkDelete', $args, ['discountId' => 'ID!', 'search' => 'String', 'savedSearchId' => 'ID', 'ids' => '[ID!]'], $selection);
    }

    /**
     * Asynchronously add [discount codes](https://help.shopify.com/manual/discounts/discount-types#discount-codes) in bulk that customers can use to redeem a discount. You can use the `discountRedeemCodeBul
     *
     * @param array<string,mixed> $args Variaveis GraphQL: discountId: ID!, codes: [DiscountRedeemCodeInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function discountRedeemCodeBulkAdd(array $args = [], string $selection = 'bulkCreation { codesCount createdAt done failedCount id importedCount } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'discountRedeemCodeBulkAdd', $args, ['discountId' => 'ID!', 'codes' => '[DiscountRedeemCodeInput!]!'], $selection);
    }
}

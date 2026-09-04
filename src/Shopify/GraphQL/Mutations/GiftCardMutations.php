<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio GiftCard.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class GiftCardMutations extends BaseOperations
{
    /**
     * Creates a new [`GiftCard`](https://shopify.dev/docs/api/admin-graphql/latest/objects/GiftCard) with a specified initial value. You can assign the gift card to a [`Customer`](https://shopify.dev/docs/a
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: GiftCardCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function giftCardCreate(array $args = [], string $selection = 'giftCard { createdAt crossCurrencyRedemptionStrategy deactivatedAt enabled expiresOn id isRedeemable lastCharacters maskedCode note templateSuffix updatedAt } giftCardCode userErrors { field message }'): array
    {
        return $this->execute('mutation', 'giftCardCreate', $args, ['input' => 'GiftCardCreateInput!'], $selection);
    }

    /**
     * Adds funds to an existing gift card, increasing its available balance. Use this when a merchant wants to top up a customer's gift card — for example, as a promotional bonus, a customer service gesture
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, creditInput: GiftCardCreditInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function giftCardCredit(array $args = [], string $selection = 'giftCardCreditTransaction { id note processedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'giftCardCredit', $args, ['id' => 'ID!', 'creditInput' => 'GiftCardCreditInput!'], $selection);
    }

    /**
     * Deactivate a gift card. A deactivated gift card cannot be used by a customer. A deactivated gift card cannot be re-enabled.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function giftCardDeactivate(array $args = [], string $selection = 'giftCard { createdAt crossCurrencyRedemptionStrategy deactivatedAt enabled expiresOn id isRedeemable lastCharacters maskedCode note templateSuffix updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'giftCardDeactivate', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Removes funds from a gift card, decreasing its available balance. Use this for manual balance adjustments — for example, correcting an accidental over-credit or applying a fee.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, debitInput: GiftCardDebitInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function giftCardDebit(array $args = [], string $selection = 'giftCardDebitTransaction { id note processedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'giftCardDebit', $args, ['id' => 'ID!', 'debitInput' => 'GiftCardDebitInput!'], $selection);
    }

    /**
     * Creates or updates a [`gift card`](https://shopify.dev/docs/api/admin-graphql/latest/objects/GiftCard) product. This mutation is specifically designed for gift card products and automatically sets the
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: GiftCardProductSetInput!, synchronous: Boolean, identifier: ProductSetIdentifiers
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function giftCardProductSet(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } productSetOperation { id status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'giftCardProductSet', $args, ['input' => 'GiftCardProductSetInput!', 'synchronous' => 'Boolean', 'identifier' => 'ProductSetIdentifiers'], $selection);
    }

    /**
     * Sends a notification to the customer who purchased a gift card, including the gift card details and code. The notification is delivered using the customer's available contact method. Use this to resen
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function giftCardSendNotificationToCustomer(array $args = [], string $selection = 'giftCard { createdAt crossCurrencyRedemptionStrategy deactivatedAt enabled expiresOn id isRedeemable lastCharacters maskedCode note templateSuffix updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'giftCardSendNotificationToCustomer', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Sends a notification to the designated recipient of a gift card, delivering the gift card code and redemption instructions. The notification is delivered using the recipient's available contact method
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function giftCardSendNotificationToRecipient(array $args = [], string $selection = 'giftCard { createdAt crossCurrencyRedemptionStrategy deactivatedAt enabled expiresOn id isRedeemable lastCharacters maskedCode note templateSuffix updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'giftCardSendNotificationToRecipient', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates the properties of an existing gift card, such as its expiration date, note, or template suffix. Use this to modify gift card details — for example, extending an expiration date for a loyal cus
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: GiftCardUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function giftCardUpdate(array $args = [], string $selection = 'giftCard { createdAt crossCurrencyRedemptionStrategy deactivatedAt enabled expiresOn id isRedeemable lastCharacters maskedCode note templateSuffix updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'giftCardUpdate', $args, ['id' => 'ID!', 'input' => 'GiftCardUpdateInput!'], $selection);
    }
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio StoreCredit.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class StoreCreditMutations extends BaseOperations
{
    /**
     * Adds funds to a [`StoreCreditAccount`](https://shopify.dev/docs/api/admin-graphql/latest/objects/StoreCreditAccount) by creating a [`StoreCreditAccountCreditTransaction`](https://shopify.dev/docs/api/
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, creditInput: StoreCreditAccountCreditInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function storeCreditAccountCredit(array $args = [], string $selection = 'storeCreditAccountTransaction { createdAt event expiresAt id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'storeCreditAccountCredit', $args, ['id' => 'ID!', 'creditInput' => 'StoreCreditAccountCreditInput!'], $selection);
    }

    /**
     * Creates a debit transaction that decreases the store credit account balance by the given amount.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, debitInput: StoreCreditAccountDebitInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function storeCreditAccountDebit(array $args = [], string $selection = 'storeCreditAccountTransaction { createdAt event id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'storeCreditAccountDebit', $args, ['id' => 'ID!', 'debitInput' => 'StoreCreditAccountDebitInput!'], $selection);
    }

    /**
     * Creates a storefront access token that delegates unauthenticated access scopes to clients using the [Storefront API](https://shopify.dev/docs/api/storefront). The token provides public access to store
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: StorefrontAccessTokenInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function storefrontAccessTokenCreate(array $args = [], string $selection = 'shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } storefrontAccessToken { accessToken createdAt id title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'storefrontAccessTokenCreate', $args, ['input' => 'StorefrontAccessTokenInput!'], $selection);
    }

    /**
     * Deletes a storefront access token.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: StorefrontAccessTokenDeleteInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function storefrontAccessTokenDelete(array $args = [], string $selection = 'deletedStorefrontAccessTokenId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'storefrontAccessTokenDelete', $args, ['input' => 'StorefrontAccessTokenDeleteInput!'], $selection);
    }
}

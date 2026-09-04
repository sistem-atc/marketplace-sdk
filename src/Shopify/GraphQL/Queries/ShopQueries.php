<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Shop.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class ShopQueries extends BaseOperations
{
    /**
     * Returns the Shop resource corresponding to the access token used in the request. The Shop resource contains business and store management settings for the shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shop(array $args = [], string $selection = 'analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit'): array
    {
        return $this->execute('query', 'shop', $args, [], $selection);
    }

    /**
     * The shop's billing preferences, including the currency for paying for apps and services. Use this to create [app charges in the merchant's local billing currency](https://shopify.dev/docs/apps/launch/
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shopBillingPreferences(array $args = [], string $selection = 'currency'): array
    {
        return $this->execute('query', 'shopBillingPreferences', $args, [], $selection);
    }

    /**
     * Returns the locales enabled on a shop. Each locale represents a language for translations and determines how content displays to customers in different markets. Use the optional `published` argument
     *
     * @param array<string,mixed> $args Variaveis GraphQL: published: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shopLocales(array $args = [], string $selection = 'locale name primary published'): array
    {
        return $this->execute('query', 'shopLocales', $args, ['published' => 'Boolean'], $selection);
    }

    /**
     * Returns a single Shop Pay payment request receipt by its ID. Payment request receipts document completed Shop Pay transactions, including the amount, customer details, and payment status. Use this to
     *
     * @param array<string,mixed> $args Variaveis GraphQL: token: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shopPayPaymentRequestReceipt(array $args = [], string $selection = 'createdAt sourceIdentifier token'): array
    {
        return $this->execute('query', 'shopPayPaymentRequestReceipt', $args, ['token' => 'String!'], $selection);
    }

    /**
     * Returns a paginated list of Shop Pay payment request receipts for the shop. Each receipt documents a completed Shop Pay transaction. Use this to review Shop Pay transaction history, generate reports,
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: ShopPayPaymentRequestReceiptsSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shopPayPaymentRequestReceipts(array $args = [], string $selection = 'edges { node { createdAt sourceIdentifier token } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'shopPayPaymentRequestReceipts', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'ShopPayPaymentRequestReceiptsSortKeys', 'query' => 'String'], $selection);
    }
}

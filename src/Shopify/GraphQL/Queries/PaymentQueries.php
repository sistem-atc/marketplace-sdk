<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Payment.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class PaymentQueries extends BaseOperations
{
    /**
     * Returns a `ShopifyPaymentsDispute` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function dispute(array $args = [], string $selection = 'evidenceDueBy evidenceSentOn finalizedOn id initiatedAt legacyResourceId status type'): array
    {
        return $this->execute('query', 'dispute', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a `ShopifyPaymentsDisputeEvidence` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function disputeEvidence(array $args = [], string $selection = 'accessActivityLog cancellationPolicyDisclosure cancellationRebuttal customerEmailAddress customerFirstName customerLastName customerPurchaseIp id productDescription refundPolicyDisclosure refundRefusalExplanation submitted uncategorizedText'): array
    {
        return $this->execute('query', 'disputeEvidence', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a paginated list of all Shopify Payments disputes for the shop. Disputes occur when a buyer files a complaint with their payments provider, and the merchant must provide evidence to contest it
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function disputes(array $args = [], string $selection = 'edges { node { evidenceDueBy evidenceSentOn finalizedOn id initiatedAt legacyResourceId status type } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'disputes', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'query' => 'String'], $selection);
    }

    /**
     * The payment customization.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function paymentCustomization(array $args = [], string $selection = 'enabled functionId id title'): array
    {
        return $this->execute('query', 'paymentCustomization', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * The payment customizations.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function paymentCustomizations(array $args = [], string $selection = 'edges { node { enabled functionId id title } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'paymentCustomizations', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'query' => 'String'], $selection);
    }

    /**
     * The list of payment terms templates eligible for all shops and users.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: paymentTermsType: PaymentTermsType
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function paymentTermsTemplates(array $args = [], string $selection = 'description dueInDays id name paymentTermsType translatedName'): array
    {
        return $this->execute('query', 'paymentTermsTemplates', $args, ['paymentTermsType' => 'PaymentTermsType'], $selection);
    }

    /**
     * Returns the Shopify Payments account information for the shop. Includes current balances across all currencies, payout schedules, and bank account configurations. The account includes [`ShopifyPaymen
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shopifyPaymentsAccount(array $args = [], string $selection = 'accountOpenerName activated chargeStatementDescriptor country defaultCurrency id onboardable payoutStatementDescriptor'): array
    {
        return $this->execute('query', 'shopifyPaymentsAccount', $args, [], $selection);
    }

    /**
     * Transactions representing a movement of money between customers and the shop. Each transaction records the amount, payment method, processing details, and the associated [`Order`](https://shopify.dev/
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function tenderTransactions(array $args = [], string $selection = 'edges { node { id paymentMethod processedAt remoteReference test } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'tenderTransactions', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'query' => 'String'], $selection);
    }
}

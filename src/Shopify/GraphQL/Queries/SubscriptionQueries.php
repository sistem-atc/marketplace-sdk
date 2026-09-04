<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Subscription.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class SubscriptionQueries extends BaseOperations
{
    /**
     * Returns a `SubscriptionBillingAttempt` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingAttempt(array $args = [], string $selection = 'completedAt createdAt errorCode errorMessage id idempotencyKey nextActionUrl originTime paymentGroupId paymentSessionId ready respectInventoryPolicy'): array
    {
        return $this->execute('query', 'subscriptionBillingAttempt', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns subscription billing attempts on a store.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: SubscriptionBillingAttemptsSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingAttempts(array $args = [], string $selection = 'edges { node { completedAt createdAt errorCode errorMessage id idempotencyKey nextActionUrl originTime paymentGroupId paymentSessionId ready respectInventoryPolicy } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'subscriptionBillingAttempts', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'SubscriptionBillingAttemptsSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * Returns a subscription billing cycle found either by cycle index or date.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: billingCycleInput: SubscriptionBillingCycleInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingCycle(array $args = [], string $selection = 'billingAttemptExpectedDate cycleEndAt cycleIndex cycleStartAt edited skipped status'): array
    {
        return $this->execute('query', 'subscriptionBillingCycle', $args, ['billingCycleInput' => 'SubscriptionBillingCycleInput!'], $selection);
    }

    /**
     * Retrieves the results of the asynchronous job for the subscription billing cycle bulk action based on the specified job ID. This query can be used to obtain the billing cycles that match the criteria
     *
     * @param array<string,mixed> $args Variaveis GraphQL: jobId: ID!, first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingCycleBulkResults(array $args = [], string $selection = 'edges { node { billingAttemptExpectedDate cycleEndAt cycleIndex cycleStartAt edited skipped status } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'subscriptionBillingCycleBulkResults', $args, ['jobId' => 'ID!', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Returns subscription billing cycles for a contract ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: contractId: ID!, billingCyclesDateRangeSelector: SubscriptionBillingCyclesDateRangeSelector, billingCyclesIndexRangeSelector: SubscriptionBillingCyclesIndexRangeSelector, first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: SubscriptionBillingCyclesSortKeys
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingCycles(array $args = [], string $selection = 'edges { node { billingAttemptExpectedDate cycleEndAt cycleIndex cycleStartAt edited skipped status } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'subscriptionBillingCycles', $args, ['contractId' => 'ID!', 'billingCyclesDateRangeSelector' => 'SubscriptionBillingCyclesDateRangeSelector', 'billingCyclesIndexRangeSelector' => 'SubscriptionBillingCyclesIndexRangeSelector', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'SubscriptionBillingCyclesSortKeys'], $selection);
    }

    /**
     * Retrieves a [`SubscriptionContract`](https://shopify.dev/docs/api/customer/latest/objects/SubscriptionContract) by ID. The contract tracks the subscription's lifecycle through various [statuses](http
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionContract(array $args = [], string $selection = 'appAdminUrl createdAt currencyCode id lastBillingAttemptErrorType lastPaymentStatus lineCount nextBillingDate note revisionId status updatedAt'): array
    {
        return $this->execute('query', 'subscriptionContract', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a [`SubscriptionContractConnection`](https://shopify.dev/docs/api/admin-graphql/latest/objects/SubscriptionContractConnection) containing [subscription contracts](https://shopify.dev/docs/api/
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: SubscriptionContractsSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionContracts(array $args = [], string $selection = 'edges { node { appAdminUrl createdAt currencyCode id lastBillingAttemptErrorType lastPaymentStatus lineCount nextBillingDate note revisionId status updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'subscriptionContracts', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'SubscriptionContractsSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * Returns a Subscription Draft resource by ID.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionDraft(array $args = [], string $selection = 'currencyCode id nextBillingDate note status'): array
    {
        return $this->execute('query', 'subscriptionDraft', $args, ['id' => 'ID!'], $selection);
    }
}

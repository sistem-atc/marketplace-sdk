<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Customer.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class CustomerQueries extends BaseOperations
{
    /**
     * Returns a `Customer` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customer(array $args = [], string $selection = 'canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail'): array
    {
        return $this->execute('query', 'customer', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a `CustomerAccountPage` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerAccountPage(array $args = [], string $selection = 'defaultCursor handle id title'): array
    {
        return $this->execute('query', 'customerAccountPage', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * List of the shop's customer account pages.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerAccountPages(array $args = [], string $selection = 'edges { node { defaultCursor handle id title } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'customerAccountPages', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Return a customer by an identifier.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: identifier: CustomerIdentifierInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerByIdentifier(array $args = [], string $selection = 'canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail'): array
    {
        return $this->execute('query', 'customerByIdentifier', $args, ['identifier' => 'CustomerIdentifierInput!'], $selection);
    }

    /**
     * Returns the status of a customer merge request job.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: jobId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerMergeJobStatus(array $args = [], string $selection = 'jobId resultingCustomerId status'): array
    {
        return $this->execute('query', 'customerMergeJobStatus', $args, ['jobId' => 'ID!'], $selection);
    }

    /**
     * Returns a preview of a customer merge request. The `customerOneId` and `customerTwoId` arguments don't guarantee which customer is kept. Shopify selects the resulting customer in this order: 1. If `o
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerOneId: ID!, customerTwoId: ID!, overrideFields: CustomerMergeOverrideFields
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerMergePreview(array $args = [], string $selection = 'resultingCustomerId'): array
    {
        return $this->execute('query', 'customerMergePreview', $args, ['customerOneId' => 'ID!', 'customerTwoId' => 'ID!', 'overrideFields' => 'CustomerMergeOverrideFields'], $selection);
    }

    /**
     * Returns a vaulted customer payment method by its ID, including the instrument type (credit card, PayPal, etc.), billing address, and current status. Optionally includes revoked payment methods. Use th
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, showRevoked: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerPaymentMethod(array $args = [], string $selection = 'id revokedAt revokedReason'): array
    {
        return $this->execute('query', 'customerPaymentMethod', $args, ['id' => 'ID!', 'showRevoked' => 'Boolean'], $selection);
    }

    /**
     * List of the shop's customer saved searches.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: CustomerSavedSearchSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerSavedSearches(array $args = [], string $selection = 'edges { node { id legacyResourceId name query resourceType searchTerms } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'customerSavedSearches', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'CustomerSavedSearchSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * A paginated list of customers that belong to an individual [`Segment`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Segment). Segments group customers based on criteria defined through [S
     *
     * @param array<string,mixed> $args Variaveis GraphQL: segmentId: ID, query: String, queryId: ID, timezone: String, reverse: Boolean, sortKey: String, first: Int, after: String, last: Int, before: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerSegmentMembers(array $args = [], string $selection = 'edges { node { displayName firstName id lastName lastOrderId note numberOfOrders } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'customerSegmentMembers', $args, ['segmentId' => 'ID', 'query' => 'String', 'queryId' => 'ID', 'timezone' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'String', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String'], $selection);
    }

    /**
     * Returns a `CustomerSegmentMembersQuery` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerSegmentMembersQuery(array $args = [], string $selection = 'currentCount done id'): array
    {
        return $this->execute('query', 'customerSegmentMembersQuery', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Whether a member, which is a customer, belongs to a segment.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: segmentIds: [ID!]!, customerId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerSegmentMembership(array $args = [], string $selection = '__typename'): array
    {
        return $this->execute('query', 'customerSegmentMembership', $args, ['segmentIds' => '[ID!]!', 'customerId' => 'ID!'], $selection);
    }

    /**
     * Returns a list of [customers](https://shopify.dev/api/admin-graphql/latest/objects/Customer) in your Shopify store, including key information such as name, email, location, and purchase history. Use t
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: CustomerSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customers(array $args = [], string $selection = 'edges { node { canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'customers', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'CustomerSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * The number of customers. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customersCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'customersCount', $args, ['query' => 'String', 'limit' => 'Int'], $selection);
    }
}

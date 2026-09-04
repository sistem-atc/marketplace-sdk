<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Misc.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class MiscQueries extends BaseOperations
{
    /**
     * Returns all locales that Shopify supports. Each [`Locale`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Locale) includes an ISO code and human-readable name. Use this query to discover wh
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function availableLocales(array $args = [], string $selection = 'isoCode name'): array
    {
        return $this->execute('query', 'availableLocales', $args, [], $selection);
    }

    /**
     * Returns the list of [business entities](https://shopify.dev/docs/api/admin-graphql/latest/objects/BusinessEntity) associated with the shop. Use this query to retrieve business entities for assigning t
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function businessEntities(array $args = [], string $selection = 'archived companyName displayName id primary'): array
    {
        return $this->execute('query', 'businessEntities', $args, [], $selection);
    }

    /**
     * Returns a Business Entity by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function businessEntity(array $args = [], string $selection = 'archived companyName displayName id primary'): array
    {
        return $this->execute('query', 'businessEntity', $args, ['id' => 'ID'], $selection);
    }

    /**
     * Returns a [`Channel`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Channel) by ID. The channel must belong to the calling application.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function channel(array $args = [], string $selection = 'accountId accountName activeRegions handle hasCollection id name overviewPath specificationHandle supportsFuturePublishing'): array
    {
        return $this->execute('query', 'channel', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a [`Channel`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Channel) by its unique string handle. The handle is either set explicitly during [`channelCreate`](https://shopify.dev/d
     *
     * @param array<string,mixed> $args Variaveis GraphQL: handle: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function channelByHandle(array $args = [], string $selection = 'accountId accountName activeRegions handle hasCollection id name overviewPath specificationHandle supportsFuturePublishing'): array
    {
        return $this->execute('query', 'channelByHandle', $args, ['handle' => 'String!'], $selection);
    }

    /**
     * The list of [`Channel`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Channel) objects on the shop. When the calling application supports multi-channel, only channels established by the ca
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function channels(array $args = [], string $selection = 'edges { node { accountId accountName activeRegions handle hasCollection id name overviewPath specificationHandle supportsFuturePublishing } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'channels', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Returns the customer privacy consent policies of a shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID, countryCode: PrivacyCountryCode, regionCode: String, consentRequired: Boolean, dataSaleOptOutRequired: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function consentPolicy(array $args = [], string $selection = 'consentRequired countryCode dataSaleOptOutRequired id regionCode shopId'): array
    {
        return $this->execute('query', 'consentPolicy', $args, ['id' => 'ID', 'countryCode' => 'PrivacyCountryCode', 'regionCode' => 'String', 'consentRequired' => 'Boolean', 'dataSaleOptOutRequired' => 'Boolean'], $selection);
    }

    /**
     * List of countries and regions for which consent policies can be created or updated.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function consentPolicyRegions(array $args = [], string $selection = 'countryCode regionCode'): array
    {
        return $this->execute('query', 'consentPolicyRegions', $args, [], $selection);
    }

    /**
     * The paginated list of deletion events.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: subjectTypes: [DeletionEventSubjectType!], first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: DeletionEventSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deletionEvents(array $args = [], string $selection = 'edges { node { occurredAt subjectId subjectType } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'deletionEvents', $args, ['subjectTypes' => '[DeletionEventSubjectType!]', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'DeletionEventSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * Returns a `Domain` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function domain(array $args = [], string $selection = 'host id sslEnabled url'): array
    {
        return $this->execute('query', 'domain', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns the access policy for a finance app .
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function financeAppAccessPolicy(array $args = [], string $selection = 'access'): array
    {
        return $this->execute('query', 'financeAppAccessPolicy', $args, [], $selection);
    }

    /**
     * Returns Know Your Customer (KYC) information for the shop's Shopify Payments account. KYC data includes verified identity and business details collected during onboarding. This is primarily used by em
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function financeKycInformation(array $args = [], string $selection = 'businessType legalName'): array
    {
        return $this->execute('query', 'financeKycInformation', $args, [], $selection);
    }

    /**
     * Returns a Job resource by ID. Used to check the status of internal jobs and any applicable changes.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function job(array $args = [], string $selection = 'done id'): array
    {
        return $this->execute('query', 'job', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Return a mobile platform application by its ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function mobilePlatformApplication(array $args = [], string $selection = '__typename'): array
    {
        return $this->execute('query', 'mobilePlatformApplication', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * List the mobile platform applications.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function mobilePlatformApplications(array $args = [], string $selection = 'edges { node { __typename } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'mobilePlatformApplications', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Returns a specific node (any object that implements the [Node](https://shopify.dev/api/admin-graphql/latest/interfaces/Node) interface) by ID, in accordance with the [Relay specification](https://rela
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function node(array $args = [], string $selection = 'id'): array
    {
        return $this->execute('query', 'node', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns the list of nodes (any objects that implement the [Node](https://shopify.dev/api/admin-graphql/latest/interfaces/Node) interface) with the given IDs, in accordance with the [Relay specificatio
     *
     * @param array<string,mixed> $args Variaveis GraphQL: ids: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function nodes(array $args = [], string $selection = 'id'): array
    {
        return $this->execute('query', 'nodes', $args, ['ids' => '[ID!]!'], $selection);
    }

    /**
     * Privacy related settings for a shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function privacySettings(array $args = [], string $selection = '__typename'): array
    {
        return $this->execute('query', 'privacySettings', $args, [], $selection);
    }

    /**
     * The list of publicly-accessible Admin API versions, including supported versions, the release candidate, and unstable versions.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function publicApiVersions(array $args = [], string $selection = 'displayName handle supported'): array
    {
        return $this->execute('query', 'publicApiVersions', $args, [], $selection);
    }

    /**
     * <div class="note"><h4>Theme app extensions</h4> <p>If your app integrates with a Shopify theme and you plan to submit it to the Shopify App Store, you must use theme app extensions instead of Script
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function scriptTag(array $args = [], string $selection = 'cache createdAt displayScope id legacyResourceId src updatedAt'): array
    {
        return $this->execute('query', 'scriptTag', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * <div class="note"><h4>Theme app extensions</h4> <p>If your app integrates with a Shopify theme and you plan to submit it to the Shopify App Store, you must use theme app extensions instead of Script
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, query: String, src: URL
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function scriptTags(array $args = [], string $selection = 'edges { node { cache createdAt displayScope id legacyResourceId src updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'scriptTags', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'query' => 'String', 'src' => 'URL'], $selection);
    }

    /**
     * The server pixel configured by the app.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function serverPixel(array $args = [], string $selection = 'id status webhookEndpointAddress'): array
    {
        return $this->execute('query', 'serverPixel', $args, [], $selection);
    }

    /**
     * Returns a Shopify Function by its ID. [Functions](https://shopify.dev/apps/build/functions) enable you to customize Shopify's backend logic at defined parts of the commerce loop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shopifyFunction(array $args = [], string $selection = 'apiType apiVersion appKey description handle id inputQuery title useCreationUi'): array
    {
        return $this->execute('query', 'shopifyFunction', $args, ['id' => 'String!'], $selection);
    }

    /**
     * Returns Shopify Functions owned by the querying API client installed on the shop. [Functions](https://shopify.dev/docs/apps/build/functions) enable you to customize Shopify's backend logic at specific
     *
     * @param array<string,mixed> $args Variaveis GraphQL: apiType: String, useCreationUi: Boolean, first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shopifyFunctions(array $args = [], string $selection = 'edges { node { apiType apiVersion appKey description handle id inputQuery title useCreationUi } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'shopifyFunctions', $args, ['apiType' => 'String', 'useCreationUi' => 'Boolean', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Executes a [ShopifyQL query](https://shopify.dev/docs/apps/build/shopifyql) to analyze store data and returns results in a tabular format. The response includes column metadata with names, data types
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shopifyqlQuery(array $args = [], string $selection = 'parseErrors'): array
    {
        return $this->execute('query', 'shopifyqlQuery', $args, ['query' => 'String!'], $selection);
    }

    /**
     * Access to Shopify's [standardized product taxonomy](https://shopify.github.io/product-taxonomy/releases/unstable/?categoryId=sg-4-17-2-17) for categorizing products. The [`Taxonomy`](https://shopify.d
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function taxonomy(array $args = [], string $selection = '__typename'): array
    {
        return $this->execute('query', 'taxonomy', $args, [], $selection);
    }
}

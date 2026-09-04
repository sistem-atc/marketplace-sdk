<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Checkout.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class CheckoutQueries extends BaseOperations
{
    /**
     * Returns a list of abandoned checkouts. A checkout is considered abandoned when a customer adds contact information but doesn't complete their purchase. Includes both abandoned and recovered checkouts.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: AbandonedCheckoutSortKeys, query: String, savedSearchId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function abandonedCheckouts(array $args = [], string $selection = 'edges { node { abandonedCheckoutUrl completedAt createdAt defaultCursor discountCodes id lineItemsQuantity name note taxesIncluded updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'abandonedCheckouts', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'AbandonedCheckoutSortKeys', 'query' => 'String', 'savedSearchId' => 'ID'], $selection);
    }

    /**
     * Returns the count of abandoned checkouts for the given shop. Limited to a maximum of 10000 by default.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String, savedSearchId: ID, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function abandonedCheckoutsCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'abandonedCheckoutsCount', $args, ['query' => 'String', 'savedSearchId' => 'ID', 'limit' => 'Int'], $selection);
    }

    /**
     * Returns a `Abandonment` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function abandonment(array $args = [], string $selection = 'abandonmentType cartUrl createdAt customerHasNoDraftOrderSinceAbandonment customerHasNoOrderSinceAbandonment daysSinceLastAbandonmentEmail emailSentAt emailState hoursSinceLastAbandonedCheckout id inventoryAvailable isFromCustomStorefront isFromOnlineStore isFromShopApp isFromShopPay isMostSignificantAbandonment lastBrowseAbandonmentDate lastCartAbandonmentDate lastCheckoutAbandonmentDate mostRecentStep visitStartedAt'): array
    {
        return $this->execute('query', 'abandonment', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns an Abandonment by the Abandoned Checkout ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: abandonedCheckoutId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function abandonmentByAbandonedCheckoutId(array $args = [], string $selection = 'abandonmentType cartUrl createdAt customerHasNoDraftOrderSinceAbandonment customerHasNoOrderSinceAbandonment daysSinceLastAbandonmentEmail emailSentAt emailState hoursSinceLastAbandonedCheckout id inventoryAvailable isFromCustomStorefront isFromOnlineStore isFromShopApp isFromShopPay isMostSignificantAbandonment lastBrowseAbandonmentDate lastCartAbandonmentDate lastCheckoutAbandonmentDate mostRecentStep visitStartedAt'): array
    {
        return $this->execute('query', 'abandonmentByAbandonedCheckoutId', $args, ['abandonedCheckoutId' => 'ID!'], $selection);
    }

    /**
     * A checkout and accounts configuration for a shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function checkoutAndAccountsConfiguration(array $args = [], string $selection = 'createdAt editedAt id isPublished name updatedAt'): array
    {
        return $this->execute('query', 'checkoutAndAccountsConfiguration', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * List of checkout and accounts configurations on a shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: CheckoutAndAccountsConfigurationsGraphQLSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function checkoutAndAccountsConfigurations(array $args = [], string $selection = 'edges { node { createdAt editedAt id isPublished name updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'checkoutAndAccountsConfigurations', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'CheckoutAndAccountsConfigurationsGraphQLSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * Returns the visual customizations for checkout for a given [checkout profile](https://shopify.dev/docs/api/admin-graphql/latest/objects/CheckoutProfile). To update checkout branding settings, use the
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: checkoutProfileId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function checkoutBranding(array $args = [], string $selection = '__typename'): array
    {
        return $this->execute('query', 'checkoutBranding', $args, ['checkoutProfileId' => 'ID!'], $selection);
    }

    /**
     * Returns a [`CheckoutProfile`](https://shopify.dev/docs/api/admin-graphql/latest/objects/CheckoutProfile). Checkout profiles define the branding settings and UI extensions for a store's checkout experi
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function checkoutProfile(array $args = [], string $selection = 'createdAt editedAt id isPublished name typOspPagesActive updatedAt'): array
    {
        return $this->execute('query', 'checkoutProfile', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * List of checkout profiles on a shop.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: CheckoutProfileSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function checkoutProfiles(array $args = [], string $selection = 'edges { node { createdAt editedAt id isPublished name typOspPagesActive updatedAt } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'checkoutProfiles', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'CheckoutProfileSortKeys', 'query' => 'String'], $selection);
    }
}

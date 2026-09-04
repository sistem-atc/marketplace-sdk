<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio App.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class AppMutations extends BaseOperations
{
    /**
     * Creates a one-time charge for app features or services that don't require recurring billing. This mutation is ideal for apps that sell individual features, premium content, or services on a per-use ba
     *
     * @param array<string,mixed> $args Variaveis GraphQL: name: String!, price: MoneyInput!, returnUrl: URL!, test: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function appPurchaseOneTimeCreate(array $args = [], string $selection = 'appPurchaseOneTime { createdAt id name status test } confirmationUrl userErrors { field message }'): array
    {
        return $this->execute('mutation', 'appPurchaseOneTimeCreate', $args, ['name' => 'String!', 'price' => 'MoneyInput!', 'returnUrl' => 'URL!', 'test' => 'Boolean'], $selection);
    }

    /**
     * Revokes previously granted access scopes from an app installation, allowing merchants to reduce an app's permissions without completely uninstalling it. This provides granular control over what data a
     *
     * @param array<string,mixed> $args Variaveis GraphQL: scopes: [String!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function appRevokeAccessScopes(array $args = [], string $selection = 'revoked { description handle } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'appRevokeAccessScopes', $args, ['scopes' => '[String!]!'], $selection);
    }

    /**
     * Cancels an active app subscription, stopping future billing cycles. The cancellation behavior depends on the `replacementBehavior` setting - it can either disable auto-renewal (allowing the subscripti
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, prorate: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function appSubscriptionCancel(array $args = [], string $selection = 'appSubscription { createdAt currentPeriodEnd id name returnUrl status test trialDays } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'appSubscriptionCancel', $args, ['id' => 'ID!', 'prorate' => 'Boolean'], $selection);
    }

    /**
     * Creates a recurring or usage-based [`AppSubscription`](https://shopify.dev/docs/api/admin-graphql/latest/objects/AppSubscription) that charges merchants for app features and services. The subscription
     *
     * @param array<string,mixed> $args Variaveis GraphQL: name: String!, lineItems: [AppSubscriptionLineItemInput!]!, test: Boolean, trialDays: Int, returnUrl: URL!, replacementBehavior: AppSubscriptionReplacementBehavior
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function appSubscriptionCreate(array $args = [], string $selection = 'appSubscription { createdAt currentPeriodEnd id name returnUrl status test trialDays } confirmationUrl userErrors { field message }'): array
    {
        return $this->execute('mutation', 'appSubscriptionCreate', $args, ['name' => 'String!', 'lineItems' => '[AppSubscriptionLineItemInput!]!', 'test' => 'Boolean', 'trialDays' => 'Int', 'returnUrl' => 'URL!', 'replacementBehavior' => 'AppSubscriptionReplacementBehavior'], $selection);
    }

    /**
     * Updates the capped amount on usage-based billing for an [`AppSubscriptionLineItem`](https://shopify.dev/docs/api/admin-graphql/latest/objects/AppSubscriptionLineItem). Enables you to modify the maximu
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, cappedAmount: MoneyInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function appSubscriptionLineItemUpdate(array $args = [], string $selection = 'appSubscription { createdAt currentPeriodEnd id name returnUrl status test trialDays } confirmationUrl userErrors { field message }'): array
    {
        return $this->execute('mutation', 'appSubscriptionLineItemUpdate', $args, ['id' => 'ID!', 'cappedAmount' => 'MoneyInput!'], $selection);
    }

    /**
     * Extends the trial period for an existing app subscription. Trial extensions give merchants additional time to use the app before committing to paid billing. Requires the subscription ID and the numbe
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, days: Int!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function appSubscriptionTrialExtend(array $args = [], string $selection = 'appSubscription { createdAt currentPeriodEnd id name returnUrl status test trialDays } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'appSubscriptionTrialExtend', $args, ['id' => 'ID!', 'days' => 'Int!'], $selection);
    }

    /**
     * Uninstalls an [`App`](https://shopify.dev/docs/api/admin-graphql/latest/objects/App) from a shop. Apps use this mutation to uninstall themselves programmatically, removing their [`AppInstallation`](ht
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function appUninstall(array $args = [], string $selection = 'app { apiKey appStoreAppUrl appStoreDeveloperUrl description developerName developerType developerUrl embedded features handle id installUrl isPostPurchaseAppInUse launchUrl previouslyInstalled pricingDetails pricingDetailsSummary privacyPolicyUrl publicCategory published shopifyDeveloped title uninstallMessage uninstallUrl webhookApiVersion } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'appUninstall', $args, [], $selection);
    }

    /**
     * Creates a usage charge for an app subscription with usage-based pricing. The charge counts toward the capped amount limit set when creating the subscription. Usage records track consumption of app fe
     *
     * @param array<string,mixed> $args Variaveis GraphQL: subscriptionLineItemId: ID!, price: MoneyInput!, description: String!, idempotencyKey: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function appUsageRecordCreate(array $args = [], string $selection = 'appUsageRecord { createdAt description id idempotencyKey } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'appUsageRecordCreate', $args, ['subscriptionLineItemId' => 'ID!', 'price' => 'MoneyInput!', 'description' => 'String!', 'idempotencyKey' => 'String'], $selection);
    }
}

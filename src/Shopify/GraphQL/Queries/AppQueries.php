<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio App.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class AppQueries extends BaseOperations
{
    /**
     * Retrieves an [`App`](https://shopify.dev/docs/api/admin-graphql/latest/objects/App) by its ID. If no ID is provided, returns details about the currently authenticated app. The query provides access to
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function app(array $args = [], string $selection = 'apiKey appStoreAppUrl appStoreDeveloperUrl description developerName developerType developerUrl embedded features handle id installUrl isPostPurchaseAppInUse launchUrl previouslyInstalled pricingDetails pricingDetailsSummary privacyPolicyUrl publicCategory published shopifyDeveloped title uninstallMessage uninstallUrl webhookApiVersion'): array
    {
        return $this->execute('query', 'app', $args, ['id' => 'ID'], $selection);
    }

    /**
     * Retrieves an app by its unique handle. The handle is a URL-friendly identifier for the app. Returns the [`App`](https://shopify.dev/docs/api/admin-graphql/latest/objects/App) if found, or `null` if n
     *
     * @param array<string,mixed> $args Variaveis GraphQL: handle: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function appByHandle(array $args = [], string $selection = 'apiKey appStoreAppUrl appStoreDeveloperUrl description developerName developerType developerUrl embedded features handle id installUrl isPostPurchaseAppInUse launchUrl previouslyInstalled pricingDetails pricingDetailsSummary privacyPolicyUrl publicCategory published shopifyDeveloped title uninstallMessage uninstallUrl webhookApiVersion'): array
    {
        return $this->execute('query', 'appByHandle', $args, ['handle' => 'String!'], $selection);
    }

    /**
     * Retrieves an [`App`](https://shopify.dev/docs/api/admin-graphql/latest/objects/App) by its client ID (API key). Returns the app's configuration, installation status, [`AccessScope`](https://shopify.de
     *
     * @param array<string,mixed> $args Variaveis GraphQL: apiKey: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function appByKey(array $args = [], string $selection = 'apiKey appStoreAppUrl appStoreDeveloperUrl description developerName developerType developerUrl embedded features handle id installUrl isPostPurchaseAppInUse launchUrl previouslyInstalled pricingDetails pricingDetailsSummary privacyPolicyUrl publicCategory published shopifyDeveloped title uninstallMessage uninstallUrl webhookApiVersion'): array
    {
        return $this->execute('query', 'appByKey', $args, ['apiKey' => 'String!'], $selection);
    }

    /**
     * An app discount type.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: functionId: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function appDiscountType(array $args = [], string $selection = 'appKey description discountClass discountClasses functionId targetType title'): array
    {
        return $this->execute('query', 'appDiscountType', $args, ['functionId' => 'String!'], $selection);
    }

    /**
     * A list of app discount types installed by apps.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function appDiscountTypes(array $args = [], string $selection = 'appKey description discountClass discountClasses functionId targetType title'): array
    {
        return $this->execute('query', 'appDiscountTypes', $args, [], $selection);
    }

    /**
     * A list of app discount types installed by apps.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function appDiscountTypesNodes(array $args = [], string $selection = 'edges { node { appKey description discountClass discountClasses functionId targetType title } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'appDiscountTypesNodes', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean'], $selection);
    }

    /**
     * Retrieves an [`AppInstallation`](https://shopify.dev/docs/api/admin-graphql/latest/objects/AppInstallation) by ID. If no ID is provided, returns the installation for the currently authenticated [`App`
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function appInstallation(array $args = [], string $selection = 'id launchUrl uninstallUrl'): array
    {
        return $this->execute('query', 'appInstallation', $args, ['id' => 'ID'], $selection);
    }

    /**
     * A paginated list of [`AppInstallation`](https://shopify.dev/docs/api/admin-graphql/latest/objects/AppInstallation) objects across multiple stores where your app is installed. Use this query to monitor
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: AppInstallationSortKeys, category: AppInstallationCategory, privacy: AppInstallationPrivacy
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function appInstallations(array $args = [], string $selection = 'edges { node { id launchUrl uninstallUrl } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'appInstallations', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'AppInstallationSortKeys', 'category' => 'AppInstallationCategory', 'privacy' => 'AppInstallationPrivacy'], $selection);
    }

    /**
     * Returns the [`AppInstallation`](https://shopify.dev/docs/api/admin-graphql/latest/objects/AppInstallation) for the currently authenticated app. Provides access to granted access scopes, active [`AppSu
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function currentAppInstallation(array $args = [], string $selection = 'id launchUrl uninstallUrl'): array
    {
        return $this->execute('query', 'currentAppInstallation', $args, [], $selection);
    }
}

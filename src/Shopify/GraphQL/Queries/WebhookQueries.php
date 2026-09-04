<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Webhook.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class WebhookQueries extends BaseOperations
{
    /**
     * Returns a webhook subscription by ID. Building an app? If you only use app-specific webhooks, you won't need this. App-specific webhook subscriptions specified in your `shopify.app.toml` may be easie
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function webhookSubscription(array $args = [], string $selection = 'callbackUrl createdAt filter format id includeFields legacyResourceId metafieldNamespaces name topic updatedAt uri'): array
    {
        return $this->execute('query', 'webhookSubscription', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Retrieves a paginated list of webhook subscriptions created using the API for the current app and shop. > Note: Returns only shop-scoped subscriptions, not app-scoped subscriptions configured in TOML
     *
     * @param array<string,mixed> $args Variaveis GraphQL: uri: String, format: WebhookSubscriptionFormat, topics: [WebhookSubscriptionTopic!], first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: WebhookSubscriptionSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function webhookSubscriptions(array $args = [], string $selection = 'edges { node { callbackUrl createdAt filter format id includeFields legacyResourceId metafieldNamespaces name topic updatedAt uri } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'webhookSubscriptions', $args, ['uri' => 'String', 'format' => 'WebhookSubscriptionFormat', 'topics' => '[WebhookSubscriptionTopic!]', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'WebhookSubscriptionSortKeys', 'query' => 'String'], $selection);
    }

    /**
     * The count of webhook subscriptions. Building an app? If you only use app-specific webhooks, you won't need this. App-specific webhook subscriptions specified in your `shopify.app.toml` may be easier.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: query: String, limit: Int
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function webhookSubscriptionsCount(array $args = [], string $selection = 'count precision'): array
    {
        return $this->execute('query', 'webhookSubscriptionsCount', $args, ['query' => 'String', 'limit' => 'Int'], $selection);
    }
}

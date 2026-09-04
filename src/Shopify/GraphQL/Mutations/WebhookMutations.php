<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Webhook.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class WebhookMutations extends BaseOperations
{
    /**
     * Creates a webhook subscription that notifies your [`App`](https://shopify.dev/docs/api/admin-graphql/latest/objects/App) when specific events occur in a shop. Webhooks push event data to your endpoint
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: topic: WebhookSubscriptionTopic!, webhookSubscription: EventBridgeWebhookSubscriptionInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function eventBridgeWebhookSubscriptionCreate(array $args = [], string $selection = 'webhookSubscription { callbackUrl createdAt filter format id includeFields legacyResourceId metafieldNamespaces name topic updatedAt uri } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'eventBridgeWebhookSubscriptionCreate', $args, ['topic' => 'WebhookSubscriptionTopic!', 'webhookSubscription' => 'EventBridgeWebhookSubscriptionInput!'], $selection);
    }

    /**
     * Updates an Amazon EventBridge webhook subscription. Building an app? If you only use app-specific webhooks, you won't need this. App-specific webhook subscriptions specified in your `shopify.app.toml
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, webhookSubscription: EventBridgeWebhookSubscriptionInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function eventBridgeWebhookSubscriptionUpdate(array $args = [], string $selection = 'webhookSubscription { callbackUrl createdAt filter format id includeFields legacyResourceId metafieldNamespaces name topic updatedAt uri } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'eventBridgeWebhookSubscriptionUpdate', $args, ['id' => 'ID!', 'webhookSubscription' => 'EventBridgeWebhookSubscriptionInput!'], $selection);
    }

    /**
     * Creates a webhook subscription that notifies your [`App`](https://shopify.dev/docs/api/admin-graphql/latest/objects/App) when specific events occur in a shop. Webhooks push event data to your endpoint
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: topic: WebhookSubscriptionTopic!, webhookSubscription: PubSubWebhookSubscriptionInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pubSubWebhookSubscriptionCreate(array $args = [], string $selection = 'webhookSubscription { callbackUrl createdAt filter format id includeFields legacyResourceId metafieldNamespaces name topic updatedAt uri } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'pubSubWebhookSubscriptionCreate', $args, ['topic' => 'WebhookSubscriptionTopic!', 'webhookSubscription' => 'PubSubWebhookSubscriptionInput!'], $selection);
    }

    /**
     * Updates a Google Cloud Pub/Sub webhook subscription. Building an app? If you only use app-specific webhooks, you won't need this. App-specific webhook subscriptions specified in your `shopify.app.tom
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, webhookSubscription: PubSubWebhookSubscriptionInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pubSubWebhookSubscriptionUpdate(array $args = [], string $selection = 'webhookSubscription { callbackUrl createdAt filter format id includeFields legacyResourceId metafieldNamespaces name topic updatedAt uri } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'pubSubWebhookSubscriptionUpdate', $args, ['id' => 'ID!', 'webhookSubscription' => 'PubSubWebhookSubscriptionInput!'], $selection);
    }

    /**
     * Creates a webhook subscription that notifies your [`App`](https://shopify.dev/docs/api/admin-graphql/latest/objects/App) when specific events occur in a shop. Webhooks push event data to your endpoint
     *
     * @param array<string,mixed> $args Variaveis GraphQL: topic: WebhookSubscriptionTopic!, webhookSubscription: WebhookSubscriptionInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function webhookSubscriptionCreate(array $args = [], string $selection = 'webhookSubscription { callbackUrl createdAt filter format id includeFields legacyResourceId metafieldNamespaces name topic updatedAt uri } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'webhookSubscriptionCreate', $args, ['topic' => 'WebhookSubscriptionTopic!', 'webhookSubscription' => 'WebhookSubscriptionInput!'], $selection);
    }

    /**
     * Deletes a [`WebhookSubscription`](https://shopify.dev/docs/api/admin-graphql/latest/objects/WebhookSubscription) and stops all future webhooks to its endpoint. Returns the deleted subscription's ID fo
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function webhookSubscriptionDelete(array $args = [], string $selection = 'deletedWebhookSubscriptionId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'webhookSubscriptionDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates a webhook subscription's configuration. Modify the endpoint URL, event filters, included fields, or metafield namespaces without recreating the subscription. The mutation accepts a [`WebhookS
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, webhookSubscription: WebhookSubscriptionInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function webhookSubscriptionUpdate(array $args = [], string $selection = 'webhookSubscription { callbackUrl createdAt filter format id includeFields legacyResourceId metafieldNamespaces name topic updatedAt uri } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'webhookSubscriptionUpdate', $args, ['id' => 'ID!', 'webhookSubscription' => 'WebhookSubscriptionInput!'], $selection);
    }
}

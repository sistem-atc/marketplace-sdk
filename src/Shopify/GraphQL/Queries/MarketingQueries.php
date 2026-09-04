<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Queries;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Queries da Shopify Admin API GraphQL (schema 2026-07) — dominio Marketing.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `query <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class MarketingQueries extends BaseOperations
{
    /**
     * A list of marketing activities associated with the marketing app.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: marketingActivityIds: [ID!], remoteIds: [String!], utm: UTMInput, first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: MarketingActivitySortKeys, query: String, savedSearchId: ID
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketingActivities(array $args = [], string $selection = 'edges { node { activityListUrl createdAt formData hierarchyLevel id inMainWorkflowVersion isExternal marketingChannel marketingChannelType parentActivityId parentRemoteId sourceAndMedium status statusBadgeType statusBadgeTypeV2 statusLabel statusTransitionedAt tactic targetStatus title updatedAt urlParameterValue } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'marketingActivities', $args, ['marketingActivityIds' => '[ID!]', 'remoteIds' => '[String!]', 'utm' => 'UTMInput', 'first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'MarketingActivitySortKeys', 'query' => 'String', 'savedSearchId' => 'ID'], $selection);
    }

    /**
     * Returns a `MarketingActivity` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketingActivity(array $args = [], string $selection = 'activityListUrl createdAt formData hierarchyLevel id inMainWorkflowVersion isExternal marketingChannel marketingChannelType parentActivityId parentRemoteId sourceAndMedium status statusBadgeType statusBadgeTypeV2 statusLabel statusTransitionedAt tactic targetStatus title updatedAt urlParameterValue'): array
    {
        return $this->execute('query', 'marketingActivity', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Returns a `MarketingEvent` resource by ID.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketingEvent(array $args = [], string $selection = 'channel channelHandle description endedAt id legacyResourceId manageUrl marketingChannelType previewUrl remoteId scheduledToEndAt sourceAndMedium startedAt targetTypeDisplayText type utmCampaign utmMedium utmSource'): array
    {
        return $this->execute('query', 'marketingEvent', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * A list of marketing events associated with the marketing app.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: first: Int, after: String, last: Int, before: String, reverse: Boolean, sortKey: MarketingEventSortKeys, query: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketingEvents(array $args = [], string $selection = 'edges { node { channel channelHandle description endedAt id legacyResourceId manageUrl marketingChannelType previewUrl remoteId scheduledToEndAt sourceAndMedium startedAt targetTypeDisplayText type utmCampaign utmMedium utmSource } } pageInfo { hasNextPage endCursor }'): array
    {
        return $this->execute('query', 'marketingEvents', $args, ['first' => 'Int', 'after' => 'String', 'last' => 'Int', 'before' => 'String', 'reverse' => 'Boolean', 'sortKey' => 'MarketingEventSortKeys', 'query' => 'String'], $selection);
    }
}

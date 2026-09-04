<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Marketing.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class MarketingMutations extends BaseOperations
{
    /**
     * Deletes all external marketing activities. Deletion is performed by a background job, as it may take a bit of time to complete if a large number of activities are to be deleted. Attempting to create o
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketingActivitiesDeleteAllExternal(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketingActivitiesDeleteAllExternal', $args, [], $selection);
    }

    /**
     * Create new marketing activity. Marketing activity app extensions are deprecated and will be removed in the near future.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: MarketingActivityCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketingActivityCreate(array $args = [], string $selection = 'marketingActivity { activityListUrl createdAt formData hierarchyLevel id inMainWorkflowVersion isExternal marketingChannel marketingChannelType parentActivityId parentRemoteId sourceAndMedium status statusBadgeType statusBadgeTypeV2 statusLabel statusTransitionedAt tactic targetStatus title updatedAt urlParameterValue } redirectPath userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketingActivityCreate', $args, ['input' => 'MarketingActivityCreateInput!'], $selection);
    }

    /**
     * Creates a new external marketing activity.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: input: MarketingActivityCreateExternalInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketingActivityCreateExternal(array $args = [], string $selection = 'marketingActivity { activityListUrl createdAt formData hierarchyLevel id inMainWorkflowVersion isExternal marketingChannel marketingChannelType parentActivityId parentRemoteId sourceAndMedium status statusBadgeType statusBadgeTypeV2 statusLabel statusTransitionedAt tactic targetStatus title updatedAt urlParameterValue } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketingActivityCreateExternal', $args, ['input' => 'MarketingActivityCreateExternalInput!'], $selection);
    }

    /**
     * Deletes an external marketing activity.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: marketingActivityId: ID, remoteId: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketingActivityDeleteExternal(array $args = [], string $selection = 'deletedMarketingActivityId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketingActivityDeleteExternal', $args, ['marketingActivityId' => 'ID', 'remoteId' => 'String'], $selection);
    }

    /**
     * Updates a marketing activity with the latest information. Marketing activity app extensions are deprecated and will be removed in the near future.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: MarketingActivityUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketingActivityUpdate(array $args = [], string $selection = 'marketingActivity { activityListUrl createdAt formData hierarchyLevel id inMainWorkflowVersion isExternal marketingChannel marketingChannelType parentActivityId parentRemoteId sourceAndMedium status statusBadgeType statusBadgeTypeV2 statusLabel statusTransitionedAt tactic targetStatus title updatedAt urlParameterValue } redirectPath userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketingActivityUpdate', $args, ['input' => 'MarketingActivityUpdateInput!'], $selection);
    }

    /**
     * Update an external marketing activity.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: MarketingActivityUpdateExternalInput!, marketingActivityId: ID, remoteId: String, utm: UTMInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketingActivityUpdateExternal(array $args = [], string $selection = 'marketingActivity { activityListUrl createdAt formData hierarchyLevel id inMainWorkflowVersion isExternal marketingChannel marketingChannelType parentActivityId parentRemoteId sourceAndMedium status statusBadgeType statusBadgeTypeV2 statusLabel statusTransitionedAt tactic targetStatus title updatedAt urlParameterValue } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketingActivityUpdateExternal', $args, ['input' => 'MarketingActivityUpdateExternalInput!', 'marketingActivityId' => 'ID', 'remoteId' => 'String', 'utm' => 'UTMInput'], $selection);
    }

    /**
     * Creates a new external marketing activity or updates an existing one. When optional fields are absent or null, associated information will be removed from an existing marketing activity.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: MarketingActivityUpsertExternalInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketingActivityUpsertExternal(array $args = [], string $selection = 'marketingActivity { activityListUrl createdAt formData hierarchyLevel id inMainWorkflowVersion isExternal marketingChannel marketingChannelType parentActivityId parentRemoteId sourceAndMedium status statusBadgeType statusBadgeTypeV2 statusLabel statusTransitionedAt tactic targetStatus title updatedAt urlParameterValue } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketingActivityUpsertExternal', $args, ['input' => 'MarketingActivityUpsertExternalInput!'], $selection);
    }

    /**
     * Creates a new marketing engagement for a marketing activity or a marketing channel.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: marketingActivityId: ID, remoteId: String, channelHandle: String, marketingEngagement: MarketingEngagementInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketingEngagementCreate(array $args = [], string $selection = 'marketingEngagement { allConversions channelHandle clicksCount commentsCount complaintsCount failsCount favoritesCount firstTimeCustomers impressionsCount isCumulative occurredOn orders primaryConversions returningCustomers sendsCount sessionsCount sharesCount uniqueClicksCount uniqueViewsCount unsubscribesCount utcOffset viewsCount } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketingEngagementCreate', $args, ['marketingActivityId' => 'ID', 'remoteId' => 'String', 'channelHandle' => 'String', 'marketingEngagement' => 'MarketingEngagementInput!'], $selection);
    }

    /**
     * Marks channel-level engagement data such that it no longer appears in reports. Activity-level data cannot be deleted directly, instead the MarketingActivity itself should be deleted to
     *
     * @param array<string,mixed> $args Variaveis GraphQL: channelHandle: String, deleteEngagementsForAllChannels: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function marketingEngagementsDelete(array $args = [], string $selection = 'result userErrors { field message }'): array
    {
        return $this->execute('mutation', 'marketingEngagementsDelete', $args, ['channelHandle' => 'String', 'deleteEngagementsForAllChannels' => 'Boolean'], $selection);
    }
}

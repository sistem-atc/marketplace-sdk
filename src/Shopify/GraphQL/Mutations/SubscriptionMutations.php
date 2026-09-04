<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Subscription.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class SubscriptionMutations extends BaseOperations
{
    /**
     * Creates a billing attempt to charge for a [`SubscriptionContract`](https://shopify.dev/docs/api/admin-graphql/latest/objects/SubscriptionContract). The mutation processes either the payment for the cu
     *
     * @param array<string,mixed> $args Variaveis GraphQL: subscriptionContractId: ID!, subscriptionBillingAttemptInput: SubscriptionBillingAttemptInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingAttemptCreate(array $args = [], string $selection = 'subscriptionBillingAttempt { completedAt createdAt errorCode errorMessage id idempotencyKey nextActionUrl originTime paymentGroupId paymentSessionId ready respectInventoryPolicy } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionBillingAttemptCreate', $args, ['subscriptionContractId' => 'ID!', 'subscriptionBillingAttemptInput' => 'SubscriptionBillingAttemptInput!'], $selection);
    }

    /**
     * Asynchronously queries and charges all subscription billing cycles whose [billingAttemptExpectedDate](https://shopify.dev/api/admin-graphql/latest/objects/SubscriptionBillingCycle#field-billingattempt
     *
     * @param array<string,mixed> $args Variaveis GraphQL: billingAttemptExpectedDateRange: SubscriptionBillingCyclesDateRangeSelector!, filters: SubscriptionBillingCycleBulkFilters, actor: SubscriptionActor, inventoryPolicy: SubscriptionBillingAttemptInventoryPolicy, paymentProcessingPolicy: SubscriptionBillingAttemptPaymentProcessingPolicy
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingCycleBulkCharge(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionBillingCycleBulkCharge', $args, ['billingAttemptExpectedDateRange' => 'SubscriptionBillingCyclesDateRangeSelector!', 'filters' => 'SubscriptionBillingCycleBulkFilters', 'actor' => 'SubscriptionActor', 'inventoryPolicy' => 'SubscriptionBillingAttemptInventoryPolicy', 'paymentProcessingPolicy' => 'SubscriptionBillingAttemptPaymentProcessingPolicy'], $selection);
    }

    /**
     * Asynchronously queries all subscription billing cycles whose [billingAttemptExpectedDate](https://shopify.dev/api/admin-graphql/latest/objects/SubscriptionBillingCycle#field-billingattemptexpecteddate
     *
     * @param array<string,mixed> $args Variaveis GraphQL: billingAttemptExpectedDateRange: SubscriptionBillingCyclesDateRangeSelector!, filters: SubscriptionBillingCycleBulkFilters
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingCycleBulkSearch(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionBillingCycleBulkSearch', $args, ['billingAttemptExpectedDateRange' => 'SubscriptionBillingCyclesDateRangeSelector!', 'filters' => 'SubscriptionBillingCycleBulkFilters'], $selection);
    }

    /**
     * Creates a new subscription billing attempt for a specified billing cycle. This is the alternative mutation for [subscriptionBillingAttemptCreate](https://shopify.dev/docs/api/admin-graphql/latest/muta
     *
     * @param array<string,mixed> $args Variaveis GraphQL: actor: SubscriptionActor, subscriptionContractId: ID!, billingCycleSelector: SubscriptionBillingCycleSelector!, inventoryPolicy: SubscriptionBillingAttemptInventoryPolicy, paymentProcessingPolicy: SubscriptionBillingAttemptPaymentProcessingPolicy
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingCycleCharge(array $args = [], string $selection = 'subscriptionBillingAttempt { completedAt createdAt errorCode errorMessage id idempotencyKey nextActionUrl originTime paymentGroupId paymentSessionId ready respectInventoryPolicy } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionBillingCycleCharge', $args, ['actor' => 'SubscriptionActor', 'subscriptionContractId' => 'ID!', 'billingCycleSelector' => 'SubscriptionBillingCycleSelector!', 'inventoryPolicy' => 'SubscriptionBillingAttemptInventoryPolicy', 'paymentProcessingPolicy' => 'SubscriptionBillingAttemptPaymentProcessingPolicy'], $selection);
    }

    /**
     * Commits the updates of a Subscription Billing Cycle Contract draft.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: draftId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingCycleContractDraftCommit(array $args = [], string $selection = 'contract { appAdminUrl createdAt currencyCode lineCount note updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionBillingCycleContractDraftCommit', $args, ['draftId' => 'ID!'], $selection);
    }

    /**
     * Concatenates a contract to a Subscription Draft.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: draftId: ID!, concatenatedBillingCycleContracts: [SubscriptionBillingCycleInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingCycleContractDraftConcatenate(array $args = [], string $selection = 'draft { currencyCode id nextBillingDate note status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionBillingCycleContractDraftConcatenate', $args, ['draftId' => 'ID!', 'concatenatedBillingCycleContracts' => '[SubscriptionBillingCycleInput!]!'], $selection);
    }

    /**
     * Edit the contents of a subscription contract for the specified billing cycle.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: actor: SubscriptionActor, billingCycleInput: SubscriptionBillingCycleInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingCycleContractEdit(array $args = [], string $selection = 'draft { currencyCode id nextBillingDate note status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionBillingCycleContractEdit', $args, ['actor' => 'SubscriptionActor', 'billingCycleInput' => 'SubscriptionBillingCycleInput!'], $selection);
    }

    /**
     * Delete the schedule and contract edits of the selected subscription billing cycle.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: billingCycleInput: SubscriptionBillingCycleInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingCycleEditDelete(array $args = [], string $selection = 'billingCycles { billingAttemptExpectedDate cycleEndAt cycleIndex cycleStartAt edited skipped status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionBillingCycleEditDelete', $args, ['billingCycleInput' => 'SubscriptionBillingCycleInput!'], $selection);
    }

    /**
     * Delete the current and future schedule and contract edits of a list of subscription billing cycles.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: contractId: ID!, targetSelection: SubscriptionBillingCyclesTargetSelection!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingCycleEditsDelete(array $args = [], string $selection = 'billingCycles { billingAttemptExpectedDate cycleEndAt cycleIndex cycleStartAt edited skipped status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionBillingCycleEditsDelete', $args, ['contractId' => 'ID!', 'targetSelection' => 'SubscriptionBillingCyclesTargetSelection!'], $selection);
    }

    /**
     * Modify the schedule of a specific billing cycle.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: billingCycleInput: SubscriptionBillingCycleInput!, input: SubscriptionBillingCycleScheduleEditInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingCycleScheduleEdit(array $args = [], string $selection = 'billingCycle { billingAttemptExpectedDate cycleEndAt cycleIndex cycleStartAt edited skipped status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionBillingCycleScheduleEdit', $args, ['billingCycleInput' => 'SubscriptionBillingCycleInput!', 'input' => 'SubscriptionBillingCycleScheduleEditInput!'], $selection);
    }

    /**
     * Skips a Subscription Billing Cycle.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: billingCycleInput: SubscriptionBillingCycleInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingCycleSkip(array $args = [], string $selection = 'billingCycle { billingAttemptExpectedDate cycleEndAt cycleIndex cycleStartAt edited skipped status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionBillingCycleSkip', $args, ['billingCycleInput' => 'SubscriptionBillingCycleInput!'], $selection);
    }

    /**
     * Unskips a Subscription Billing Cycle.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: billingCycleInput: SubscriptionBillingCycleInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionBillingCycleUnskip(array $args = [], string $selection = 'billingCycle { billingAttemptExpectedDate cycleEndAt cycleIndex cycleStartAt edited skipped status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionBillingCycleUnskip', $args, ['billingCycleInput' => 'SubscriptionBillingCycleInput!'], $selection);
    }

    /**
     * Activates a Subscription Contract. Contract status must be either active, paused, or failed.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: actor: SubscriptionActor, subscriptionContractId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionContractActivate(array $args = [], string $selection = 'contract { appAdminUrl createdAt currencyCode id lastBillingAttemptErrorType lastPaymentStatus lineCount nextBillingDate note revisionId status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionContractActivate', $args, ['actor' => 'SubscriptionActor', 'subscriptionContractId' => 'ID!'], $selection);
    }

    /**
     * Creates a Subscription Contract.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: SubscriptionContractAtomicCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionContractAtomicCreate(array $args = [], string $selection = 'contract { appAdminUrl createdAt currencyCode id lastBillingAttemptErrorType lastPaymentStatus lineCount nextBillingDate note revisionId status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionContractAtomicCreate', $args, ['input' => 'SubscriptionContractAtomicCreateInput!'], $selection);
    }

    /**
     * Cancels a Subscription Contract.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: actor: SubscriptionActor, subscriptionContractId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionContractCancel(array $args = [], string $selection = 'contract { appAdminUrl createdAt currencyCode id lastBillingAttemptErrorType lastPaymentStatus lineCount nextBillingDate note revisionId status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionContractCancel', $args, ['actor' => 'SubscriptionActor', 'subscriptionContractId' => 'ID!'], $selection);
    }

    /**
     * Creates a subscription contract draft, which is an intention to create a new subscription. The draft lets you incrementally build and modify subscription details before committing them to create the a
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: input: SubscriptionContractCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionContractCreate(array $args = [], string $selection = 'draft { currencyCode id nextBillingDate note status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionContractCreate', $args, ['input' => 'SubscriptionContractCreateInput!'], $selection);
    }

    /**
     * Expires a Subscription Contract.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: actor: SubscriptionActor, subscriptionContractId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionContractExpire(array $args = [], string $selection = 'contract { appAdminUrl createdAt currencyCode id lastBillingAttemptErrorType lastPaymentStatus lineCount nextBillingDate note revisionId status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionContractExpire', $args, ['actor' => 'SubscriptionActor', 'subscriptionContractId' => 'ID!'], $selection);
    }

    /**
     * Fails a Subscription Contract.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: actor: SubscriptionActor, subscriptionContractId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionContractFail(array $args = [], string $selection = 'contract { appAdminUrl createdAt currencyCode id lastBillingAttemptErrorType lastPaymentStatus lineCount nextBillingDate note revisionId status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionContractFail', $args, ['actor' => 'SubscriptionActor', 'subscriptionContractId' => 'ID!'], $selection);
    }

    /**
     * Pauses a Subscription Contract.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: actor: SubscriptionActor, subscriptionContractId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionContractPause(array $args = [], string $selection = 'contract { appAdminUrl createdAt currencyCode id lastBillingAttemptErrorType lastPaymentStatus lineCount nextBillingDate note revisionId status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionContractPause', $args, ['actor' => 'SubscriptionActor', 'subscriptionContractId' => 'ID!'], $selection);
    }

    /**
     * Allows for the easy change of a Product in a Contract or a Product price change.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: actor: SubscriptionActor, subscriptionContractId: ID!, lineId: ID!, input: SubscriptionContractProductChangeInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionContractProductChange(array $args = [], string $selection = 'contract { appAdminUrl createdAt currencyCode id lastBillingAttemptErrorType lastPaymentStatus lineCount nextBillingDate note revisionId status updatedAt } lineUpdated { id productId quantity requiresShipping sellingPlanId sellingPlanName sku taxable title variantId variantTitle } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionContractProductChange', $args, ['actor' => 'SubscriptionActor', 'subscriptionContractId' => 'ID!', 'lineId' => 'ID!', 'input' => 'SubscriptionContractProductChangeInput!'], $selection);
    }

    /**
     * Sets the next billing date of a Subscription Contract. This field is managed by the apps. Alternatively you can utilize our [Billing Cycles APIs](https://shopify.dev/docs/apps/selling-
     *
     * @param array<string,mixed> $args Variaveis GraphQL: contractId: ID!, date: DateTime!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionContractSetNextBillingDate(array $args = [], string $selection = 'contract { appAdminUrl createdAt currencyCode id lastBillingAttemptErrorType lastPaymentStatus lineCount nextBillingDate note revisionId status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionContractSetNextBillingDate', $args, ['contractId' => 'ID!', 'date' => 'DateTime!'], $selection);
    }

    /**
     * Creates a draft of an existing [`SubscriptionContract`](https://shopify.dev/docs/api/admin-graphql/latest/objects/SubscriptionContract). The draft captures the current state of the contract and allows
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: actor: SubscriptionActor, contractId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionContractUpdate(array $args = [], string $selection = 'draft { currencyCode id nextBillingDate note status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionContractUpdate', $args, ['actor' => 'SubscriptionActor', 'contractId' => 'ID!'], $selection);
    }

    /**
     * Commits the updates of a Subscription Contract draft.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: draftId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionDraftCommit(array $args = [], string $selection = 'contract { appAdminUrl createdAt currencyCode id lastBillingAttemptErrorType lastPaymentStatus lineCount nextBillingDate note revisionId status updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionDraftCommit', $args, ['draftId' => 'ID!'], $selection);
    }

    /**
     * Adds a subscription discount to a subscription draft.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: draftId: ID!, input: SubscriptionManualDiscountInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionDraftDiscountAdd(array $args = [], string $selection = 'discountAdded { id recurringCycleLimit rejectionReason targetType title type usageCount } draft { currencyCode id nextBillingDate note status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionDraftDiscountAdd', $args, ['draftId' => 'ID!', 'input' => 'SubscriptionManualDiscountInput!'], $selection);
    }

    /**
     * Applies a code discount on the subscription draft.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: draftId: ID!, redeemCode: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionDraftDiscountCodeApply(array $args = [], string $selection = 'appliedDiscount { id redeemCode rejectionReason } draft { currencyCode id nextBillingDate note status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionDraftDiscountCodeApply', $args, ['draftId' => 'ID!', 'redeemCode' => 'String!'], $selection);
    }

    /**
     * Removes a subscription discount from a subscription draft.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: draftId: ID!, discountId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionDraftDiscountRemove(array $args = [], string $selection = 'discountRemoved { __typename } draft { currencyCode id nextBillingDate note status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionDraftDiscountRemove', $args, ['draftId' => 'ID!', 'discountId' => 'ID!'], $selection);
    }

    /**
     * Updates a subscription discount on a subscription draft.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: draftId: ID!, discountId: ID!, input: SubscriptionManualDiscountInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionDraftDiscountUpdate(array $args = [], string $selection = 'discountUpdated { id recurringCycleLimit rejectionReason targetType title type usageCount } draft { currencyCode id nextBillingDate note status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionDraftDiscountUpdate', $args, ['draftId' => 'ID!', 'discountId' => 'ID!', 'input' => 'SubscriptionManualDiscountInput!'], $selection);
    }

    /**
     * Adds a subscription free shipping discount to a subscription draft.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: draftId: ID!, input: SubscriptionFreeShippingDiscountInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionDraftFreeShippingDiscountAdd(array $args = [], string $selection = 'discountAdded { id recurringCycleLimit rejectionReason targetType title type usageCount } draft { currencyCode id nextBillingDate note status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionDraftFreeShippingDiscountAdd', $args, ['draftId' => 'ID!', 'input' => 'SubscriptionFreeShippingDiscountInput!'], $selection);
    }

    /**
     * Updates a subscription free shipping discount on a subscription draft.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: draftId: ID!, discountId: ID!, input: SubscriptionFreeShippingDiscountInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionDraftFreeShippingDiscountUpdate(array $args = [], string $selection = 'discountUpdated { id recurringCycleLimit rejectionReason targetType title type usageCount } draft { currencyCode id nextBillingDate note status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionDraftFreeShippingDiscountUpdate', $args, ['draftId' => 'ID!', 'discountId' => 'ID!', 'input' => 'SubscriptionFreeShippingDiscountInput!'], $selection);
    }

    /**
     * Adds a subscription line to a subscription draft.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: draftId: ID!, input: SubscriptionLineInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionDraftLineAdd(array $args = [], string $selection = 'draft { currencyCode id nextBillingDate note status } lineAdded { id productId quantity requiresShipping sellingPlanId sellingPlanName sku taxable title variantId variantTitle } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionDraftLineAdd', $args, ['draftId' => 'ID!', 'input' => 'SubscriptionLineInput!'], $selection);
    }

    /**
     * Removes a subscription line from a subscription draft.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: draftId: ID!, lineId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionDraftLineRemove(array $args = [], string $selection = 'discountsUpdated { id recurringCycleLimit rejectionReason targetType title type usageCount } draft { currencyCode id nextBillingDate note status } lineRemoved { id productId quantity requiresShipping sellingPlanId sellingPlanName sku taxable title variantId variantTitle } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionDraftLineRemove', $args, ['draftId' => 'ID!', 'lineId' => 'ID!'], $selection);
    }

    /**
     * Updates a subscription line on a subscription draft.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: draftId: ID!, lineId: ID!, input: SubscriptionLineUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionDraftLineUpdate(array $args = [], string $selection = 'draft { currencyCode id nextBillingDate note status } lineUpdated { id productId quantity requiresShipping sellingPlanId sellingPlanName sku taxable title variantId variantTitle } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionDraftLineUpdate', $args, ['draftId' => 'ID!', 'lineId' => 'ID!', 'input' => 'SubscriptionLineUpdateInput!'], $selection);
    }

    /**
     * Updates a Subscription Draft.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: draftId: ID!, input: SubscriptionDraftInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function subscriptionDraftUpdate(array $args = [], string $selection = 'draft { currencyCode id nextBillingDate note status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'subscriptionDraftUpdate', $args, ['draftId' => 'ID!', 'input' => 'SubscriptionDraftInput!'], $selection);
    }
}

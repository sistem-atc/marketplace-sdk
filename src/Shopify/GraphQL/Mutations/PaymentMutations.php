<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Payment.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class PaymentMutations extends BaseOperations
{
    /**
     * Updates the evidence package for a Shopify Payments dispute. Merchants submit evidence — such as shipping confirmations, customer communications, and refund policies — to contest a dispute filed by a
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: ShopifyPaymentsDisputeEvidenceUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function disputeEvidenceUpdate(array $args = [], string $selection = 'disputeEvidence { accessActivityLog cancellationPolicyDisclosure cancellationRebuttal customerEmailAddress customerFirstName customerLastName customerPurchaseIp id productDescription refundPolicyDisclosure refundRefusalExplanation submitted uncategorizedText } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'disputeEvidenceUpdate', $args, ['id' => 'ID!', 'input' => 'ShopifyPaymentsDisputeEvidenceUpdateInput!'], $selection);
    }

    /**
     * Activates or deactivates payment customizations for the shop. Payment customizations allow apps to hide, reorder, or rename payment methods at checkout based on cart contents, customer attributes, or
     *
     * @param array<string,mixed> $args Variaveis GraphQL: ids: [ID!]!, enabled: Boolean!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function paymentCustomizationActivation(array $args = [], string $selection = 'ids userErrors { field message }'): array
    {
        return $this->execute('mutation', 'paymentCustomizationActivation', $args, ['ids' => '[ID!]!', 'enabled' => 'Boolean!'], $selection);
    }

    /**
     * Creates a new payment customization for the shop. Payment customizations let apps modify the payment methods shown at checkout — hiding, reordering, or renaming options based on cart contents, custome
     *
     * @param array<string,mixed> $args Variaveis GraphQL: paymentCustomization: PaymentCustomizationInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function paymentCustomizationCreate(array $args = [], string $selection = 'paymentCustomization { enabled functionId id title } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'paymentCustomizationCreate', $args, ['paymentCustomization' => 'PaymentCustomizationInput!'], $selection);
    }

    /**
     * Permanently deletes a payment customization. Once deleted, the customization will no longer affect which payment methods appear at checkout.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function paymentCustomizationDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'paymentCustomizationDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates an existing payment customization, modifying its configuration for how payment methods are displayed at checkout. Use this to change the customization's title or enabled state. The customizati
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, paymentCustomization: PaymentCustomizationInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function paymentCustomizationUpdate(array $args = [], string $selection = 'paymentCustomization { enabled functionId id title } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'paymentCustomizationUpdate', $args, ['id' => 'ID!', 'paymentCustomization' => 'PaymentCustomizationInput!'], $selection);
    }

    /**
     * Sends an email payment reminder for a payment schedule.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: paymentScheduleId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function paymentReminderSend(array $args = [], string $selection = 'success userErrors { field message }'): array
    {
        return $this->execute('mutation', 'paymentReminderSend', $args, ['paymentScheduleId' => 'ID!'], $selection);
    }

    /**
     * Captures payment for a due [`PaymentSchedule`](https://shopify.dev/docs/api/admin-graphql/latest/objects/PaymentSchedule) using the vaulted payment method associated with the order. The mutation reso
     *
     * @param array<string,mixed> $args Variaveis GraphQL: paymentScheduleId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function paymentScheduleCapture(array $args = [], string $selection = 'job { done id } jobResult { done id status } paymentReferenceId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'paymentScheduleCapture', $args, ['paymentScheduleId' => 'ID!'], $selection);
    }

    /**
     * Create payment terms on an order. To create payment terms on a draft order, use a draft order mutation and include the request with the `DraftOrderInput`.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: referenceId: ID!, paymentTermsAttributes: PaymentTermsCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function paymentTermsCreate(array $args = [], string $selection = 'paymentTerms { due dueInDays id overdue paymentTermsName paymentTermsType translatedName } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'paymentTermsCreate', $args, ['referenceId' => 'ID!', 'paymentTermsAttributes' => 'PaymentTermsCreateInput!'], $selection);
    }

    /**
     * Delete payment terms for an order. To delete payment terms on a draft order, use a draft order mutation and include the request with the `DraftOrderInput`.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: PaymentTermsDeleteInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function paymentTermsDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'paymentTermsDelete', $args, ['input' => 'PaymentTermsDeleteInput!'], $selection);
    }

    /**
     * Update payment terms on an order. To update payment terms on a draft order, use a draft order mutation and include the request with the `DraftOrderInput`.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: PaymentTermsUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function paymentTermsUpdate(array $args = [], string $selection = 'paymentTerms { due dueInDays id overdue paymentTermsName paymentTermsType translatedName } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'paymentTermsUpdate', $args, ['input' => 'PaymentTermsUpdateInput!'], $selection);
    }

    /**
     * Creates an alternate currency payout for a Shopify Payments account.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: accountId: ID, currency: CurrencyCode!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shopifyPaymentsPayoutAlternateCurrencyCreate(array $args = [], string $selection = 'payout { arrivalDate createdAt currency remoteId } success userErrors { field message }'): array
    {
        return $this->execute('mutation', 'shopifyPaymentsPayoutAlternateCurrencyCreate', $args, ['accountId' => 'ID', 'currency' => 'CurrencyCode!'], $selection);
    }

    /**
     * Trigger the voiding of an uncaptured authorization transaction.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: parentTransactionId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function transactionVoid(array $args = [], string $selection = 'transaction { accountNumber amount authorizationCode authorizationExpiresAt createdAt errorCode formattedGateway gateway id kind manualPaymentGateway manuallyCapturable maximumRefundable multiCapturable paymentId paymentMethod processedAt receiptJson settlementCurrency settlementCurrencyRate status test totalUnsettled } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'transactionVoid', $args, ['parentTransactionId' => 'ID!'], $selection);
    }
}

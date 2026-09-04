<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Checkout.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class CheckoutMutations extends BaseOperations
{
    /**
     * Updates the email state value for an abandonment.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, emailState: AbandonmentEmailState!, emailSentAt: DateTime, emailStateChangeReason: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function abandonmentEmailStateUpdate(array $args = [], string $selection = 'abandonment { abandonmentType cartUrl createdAt customerHasNoDraftOrderSinceAbandonment customerHasNoOrderSinceAbandonment daysSinceLastAbandonmentEmail emailSentAt emailState hoursSinceLastAbandonedCheckout id inventoryAvailable isFromCustomStorefront isFromOnlineStore isFromShopApp isFromShopPay isMostSignificantAbandonment lastBrowseAbandonmentDate lastCartAbandonmentDate lastCheckoutAbandonmentDate mostRecentStep visitStartedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'abandonmentEmailStateUpdate', $args, ['id' => 'ID!', 'emailState' => 'AbandonmentEmailState!', 'emailSentAt' => 'DateTime', 'emailStateChangeReason' => 'String'], $selection);
    }

    /**
     * Updates the marketing activities delivery statuses for an abandonment.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: abandonmentId: ID!, marketingActivityId: ID!, deliveryStatus: AbandonmentDeliveryState!, deliveredAt: DateTime, deliveryStatusChangeReason: String
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function abandonmentUpdateActivitiesDeliveryStatuses(array $args = [], string $selection = 'abandonment { abandonmentType cartUrl createdAt customerHasNoDraftOrderSinceAbandonment customerHasNoOrderSinceAbandonment daysSinceLastAbandonmentEmail emailSentAt emailState hoursSinceLastAbandonedCheckout id inventoryAvailable isFromCustomStorefront isFromOnlineStore isFromShopApp isFromShopPay isMostSignificantAbandonment lastBrowseAbandonmentDate lastCartAbandonmentDate lastCheckoutAbandonmentDate mostRecentStep visitStartedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'abandonmentUpdateActivitiesDeliveryStatuses', $args, ['abandonmentId' => 'ID!', 'marketingActivityId' => 'ID!', 'deliveryStatus' => 'AbandonmentDeliveryState!', 'deliveredAt' => 'DateTime', 'deliveryStatusChangeReason' => 'String'], $selection);
    }

    /**
     * Updates a checkout and accounts configuration.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, configuration: CheckoutAndAccountsConfigurationInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function checkoutAndAccountsConfigurationUpdate(array $args = [], string $selection = 'configuration { createdAt editedAt id isPublished name updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'checkoutAndAccountsConfigurationUpdate', $args, ['id' => 'ID!', 'configuration' => 'CheckoutAndAccountsConfigurationInput!'], $selection);
    }

    /**
     * Updates the visual branding for a [`CheckoutProfile`](https://shopify.dev/docs/api/admin-graphql/latest/objects/CheckoutProfile), customizing how checkout displays to customers. Creates new branding s
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: checkoutProfileId: ID!, checkoutBrandingInput: CheckoutBrandingInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function checkoutBrandingUpsert(array $args = [], string $selection = 'userErrors { field message }'): array
    {
        return $this->execute('mutation', 'checkoutBrandingUpsert', $args, ['checkoutProfileId' => 'ID!', 'checkoutBrandingInput' => 'CheckoutBrandingInput'], $selection);
    }
}

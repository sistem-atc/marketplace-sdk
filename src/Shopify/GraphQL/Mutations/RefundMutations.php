<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Refund.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class RefundMutations extends BaseOperations
{
    /**
     * Creates a refund for an order, allowing you to process returns and issue payments back to customers. Use the `refundCreate` mutation to programmatically process refunds in scenarios where you need to
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: RefundInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function refundCreate(array $args = [], string $selection = 'order { billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt } refund { createdAt id legacyResourceId note processedAt updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'refundCreate', $args, ['input' => 'RefundInput!'], $selection);
    }
}

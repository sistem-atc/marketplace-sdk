<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents payment method configuration and restrictions for an order. Controls which payment
 * methods are allowed or excluded, sets defaults, and configures installment limits and cost
 * absorption rules.
 */
final class PaymentMethodConfig implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** List of payment method IDs excluded from the checkout (e.g., ["amex", "visa"]). */
        public readonly ?array $notAllowedIds = null,

        /** List of payment method types excluded from the checkout (e.g., ["credit_card"]). */
        public readonly ?array $notAllowedTypes = null,

        /** Default payment method ID pre-selected in the checkout. */
        public readonly ?string $defaultId = null,

        /** Default payment method type pre-selected in the checkout. */
        public readonly ?string $defaultType = null,

        /** Maximum number of installments allowed for the order. */
        public readonly ?int $maxInstallments = null,

        /** Default number of installments pre-selected in the checkout. */
        public readonly ?int $defaultInstallments = null,

        /** Minimum number of installments allowed for the order. */
        public readonly ?int $minInstallments = null,

        /** Who absorbs the installment interest cost (e.g., "seller", "buyer"). */
        public readonly ?string $installmentsCost = null,

        /** Detailed installment configuration including promotions. */
        public readonly ?Installments $installments = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a discount rule applied to a payment method in the MercadoPago API. Defines
 * early-payment discounts (e.g. for boleto) that incentivize payers to complete payment before a
 * specified date. Nested within PaymentMethodRules.
 */
final class PaymentDiscounts implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Discount type (e.g. "fixed", "percentage"). */
        public readonly ?string $type = null,

        /** Discount value (absolute amount or percentage depending on type). */
        public readonly ?float $value = null,

        /** ISO 8601 date until which the discount is valid. */
        public readonly ?string $limitDate = null,
    ) {}
}

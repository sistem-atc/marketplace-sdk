<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a discount rule associated with a specific payment method type. Used within
 * order-level discounts to offer reduced pricing when the buyer pays with a particular payment
 * method.
 */
final class PaymentMethodDiscount implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Payment method type this discount applies to (e.g., "credit_card", "debit_card"). */
        public readonly ?string $type = null,

        /** Discounted total amount when this payment method is used. */
        public readonly ?string $newTotalAmount = null,
    ) {}
}

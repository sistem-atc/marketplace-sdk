<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a discount applied to an individual payment within an order. Tracks the type of
 * discount (e.g., campaign, coupon) that reduced the effective amount charged on a specific
 * payment.
 */
final class PaymentDiscount implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Discount classification (e.g., "campaign", "coupon"). */
        public readonly ?string $type = null,
    ) {}
}

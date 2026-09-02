<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Merchant Order Shipping Speed resource. Represents the time breakdown for a shipping option,
 * splitting the total delivery time into handling (preparation) and shipping (transit)
 * components.
 */
final class ShippingSpeed implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Handling time. */
        public readonly ?int $handling = null,

        /** Shipping time. */
        public readonly ?int $shipping = null,
    ) {}
}

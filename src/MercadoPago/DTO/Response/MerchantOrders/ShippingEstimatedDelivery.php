<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Merchant Order Shipping Estimated Delivery resource. Represents the estimated delivery window
 * for a shipping option, including the expected delivery date and the time range within that day.
 */
final class ShippingEstimatedDelivery implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Estimated delivery date. */
        public readonly ?string $date = null,

        /** Estimated lower delivery time. */
        public readonly ?string $timeFrom = null,

        /** Estimated upper delivery time. */
        public readonly ?string $timeTo = null,
    ) {}
}

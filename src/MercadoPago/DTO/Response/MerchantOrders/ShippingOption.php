<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Merchant Order Shipping Option resource. Represents a selectable shipping option for a merchant
 * order shipment. Includes cost, carrier method, estimated delivery time, and shipping speed
 * details. Fields are mapped to nested DTOs: - estimated_delivery -> ShippingEstimatedDelivery -
 * speed -> ShippingSpeed
 */
final class ShippingOption implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Shipping option ID. */
        public readonly ?int $id = null,

        /** Net cost absorbed by the receiver. */
        public readonly ?float $cost = null,

        /** Currency ID. */
        public readonly ?string $currencyId = null,

        /** Estimated delivery time information. */
        public readonly ?ShippingEstimatedDelivery $estimatedDelivery = null,

        /** Net cost of the shipping. */
        public readonly ?float $listCost = null,

        /** Option name. */
        public readonly ?string $name = null,

        /** Shipping method ID. */
        public readonly ?int $shippingMethodId = null,

        /** Shipping time information. */
        public readonly ?ShippingSpeed $speed = null,
    ) {}
}

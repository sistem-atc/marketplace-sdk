<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents shipment information associated with a payment in the MercadoPago API. Contains the
 * delivery address for the purchased items. Nested within AdditionalInfo.
 */
final class Shipments implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Delivery address where the purchased items will be shipped. */
        public readonly ?ReceiverAddress $receiverAddress = null,

        /** Whether the shipment uses express delivery. */
        public readonly ?bool $expressShipment = null,

        /** Whether the buyer picks up the item locally. */
        public readonly ?bool $localPickup = null,
    ) {}
}

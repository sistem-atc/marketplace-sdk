<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Address;

/**
 * Represents shipping information for a MercadoPago order. Contains shipping mode, cost, free
 * shipping options, and delivery address for physical goods in the order.
 */
final class Shipment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Shipping mode. "custom": seller-defined shipping. "not_specified": no specification. */
        public readonly ?string $mode = null,

        /** Whether the buyer can pick up the product in person. When true, disables shipping cost calculation. */
        public readonly ?bool $localPickup = null,

        /** Shipping cost when mode is "custom". Must be >= 0. */
        public readonly ?string $cost = null,

        /** When true, shipping is free for the buyer. Cannot be combined with cost > 0. */
        public readonly ?bool $freeShipping = null,

        /** List of free shipping method IDs available to the buyer. */
        public readonly ?array $freeMethods = null,

        /** Delivery address for the shipment. */
        public readonly ?Address $address = null,
    ) {}
}

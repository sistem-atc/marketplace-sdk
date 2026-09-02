<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference Shipments configuration resource. Configures the shipping options for a checkout
 * preference. Supports multiple modes: "me2" (MercadoEnvios managed shipping), "custom"
 * (seller-managed shipping), and "not_specified". Includes cost, free shipping options,
 * dimensions, and the receiver address. Fields are mapped to nested DTOs: - free_methods ->
 * FreeMethod - receiver_address -> ReceiverAddress
 */
final class Shipments implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Shipment mode. */
        public readonly ?string $mode = null,

        /** The payer have the option to pick up the shipment in your store (mode:me2 only). */
        public readonly ?bool $localPickup = null,

        /** Dimensions of the shipment in cm x cm x cm, gr (mode:me2 only). */
        public readonly ?string $dimensions = null,

        /** Select default shipping method in checkout (mode:me2 only). */
        public readonly ?int $defaultShippingMethod = null,

        /** Offer a shipping method as free shipping (mode:me2 only). @var list<FreeMethod>|null */
        #[ArrayOf(FreeMethod::class)]
        public readonly ?array $freeMethods = null,

        /** Shipment cost (mode:custom only). */
        public readonly ?float $cost = null,

        /** Free shipping for mode:custom. */
        public readonly ?bool $freeShipping = null,

        /** Shipping address. */
        public readonly ?ReceiverAddress $receiverAddress = null,

        /** If use express shipment. */
        public readonly ?bool $expressShipment = null,
    ) {}
}

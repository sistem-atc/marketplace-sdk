<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences\Item;

/**
 * Represents additional information attached to a payment in the MercadoPago API. Provides
 * supplementary data about the transaction including item details, payer information, and
 * shipment data. Improving fraud prevention by sending accurate additional info is recommended by
 * MercadoPago.
 */
final class AdditionalInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** IP address of the buyer (required for bank transfer payments). */
        public readonly ?string $ipAddress = null,

        /** Tracking identifier for the payment flow. */
        public readonly ?string $trackingId = null,

        /** List of items being purchased in this payment. @var list<Item>|null */
        #[ArrayOf(Item::class)]
        public readonly ?array $items = null,

        /** Extended payer details (name, phone, address, registration date). */
        public readonly ?AdditionalInfoPayer $payer = null,

        /** Shipping details including receiver address. */
        public readonly ?Shipments $shipments = null,

        /** Available balance in the payer's MercadoPago account at the time of payment. */
        public readonly ?float $availableBalance = null,

        /** Unique Sequential Number assigned by the payment processor. */
        public readonly ?string $nsuProcessadora = null,

        /** Authentication code returned by the payment processor. */
        public readonly ?string $authenticationCode = null,
    ) {}
}

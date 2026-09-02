<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Merchant Order Shipment resource. Represents a shipment associated with a merchant order.
 * Tracks shipping logistics including mode, status, sender/receiver details, and the chosen
 * shipping option with its delivery address. Fields are mapped to nested DTOs: - receiver_address
 * -> ReceiverAddress - shipping_option -> ShippingOption
 */
final class Shipment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Shipping ID. */
        public readonly ?int $id = null,

        /** Shipping type. */
        public readonly ?string $shippingType = null,

        /** Shipping mode. */
        public readonly ?string $shippingMode = null,

        /** Shipping picking type. */
        public readonly ?string $pickingType = null,

        /** Shipping status. */
        public readonly ?string $status = null,

        /** Shipping sub status. */
        public readonly ?string $shippingSubstatus = null,

        /** Shipping items. */
        public readonly ?array $items = null,

        /** Date of creation. */
        public readonly ?string $dateCreated = null,

        /** Last modified date. */
        public readonly ?string $lastModified = null,

        /** First printed date. */
        public readonly ?string $dateFirstPrinted = null,

        /** Shipping service ID. */
        public readonly ?string $serviceId = null,

        /** Sender ID. */
        public readonly ?int $senderId = null,

        /** Receiver ID. */
        public readonly ?int $receiverId = null,

        /** Shipping address. */
        public readonly ?ReceiverAddress $receiverAddress = null,

        /** Shipping options. */
        public readonly ?ShippingOption $shippingOption = null,
    ) {}
}

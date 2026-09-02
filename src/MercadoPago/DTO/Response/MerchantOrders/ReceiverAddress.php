<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Merchant Order Receiver Address resource. Represents the shipping destination address for a
 * merchant order shipment. Includes full address details such as street, city, state, country,
 * and geographic coordinates. Fields are mapped to nested DTOs: - city -> ReceiverAddressCity -
 * state -> ReceiverAddressState - country -> ReceiverAddressCountry
 */
final class ReceiverAddress implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Receiver address ID. */
        public readonly ?int $id = null,

        /** Street name and number of receiver address. */
        public readonly ?string $addressLine = null,

        /** Apartment. */
        public readonly ?string $apartment = null,

        /** City information. */
        public readonly ?ReceiverAddressCity $city = null,

        /** State information. */
        public readonly ?ReceiverAddressState $state = null,

        /** Country information. */
        public readonly ?ReceiverAddressCountry $country = null,

        /** Comment about receiver address. */
        public readonly ?string $comment = null,

        /** Contact information. */
        public readonly ?string $contact = null,

        /** Postal code. */
        public readonly ?string $zipCode = null,

        /** Street name. */
        public readonly ?string $streetName = null,

        /** Street number. */
        public readonly ?string $streetNumber = null,

        /** Floor. */
        public readonly ?string $floor = null,

        /** Phone. */
        public readonly ?string $phone = null,

        /** Latitude. */
        public readonly ?string $latitude = null,

        /** Longitude. */
        public readonly ?string $longitude = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\City;

/**
 * Represents the shipping destination address for a payment in the MercadoPago API. Extends the
 * base Address with additional fields specific to shipment delivery (apartment, floor, city
 * name). Nested within Shipments.
 */
final class ReceiverAddress implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Name of the street at the delivery address. */
        public readonly ?string $streetName = null,

        /** Name of the state/province at the delivery address. */
        public readonly ?string $stateName = null,

        /** Apartment or unit number at the delivery address. */
        public readonly ?string $apartment = null,

        /** City name at the delivery address. */
        public readonly ?string $cityName = null,

        /** Floor number within the building at the delivery address. */
        public readonly ?string $floor = null,

        /** Unique identifier of the address. */
        public readonly ?string $id = null,

        /** Postal/ZIP code of the address. */
        public readonly ?string $zipCode = null,

        /** House or building number on the street. */
        public readonly ?string $streetNumber = null,

        /** Neighborhood or district name. */
        public readonly ?string $neighborhood = null,

        /** State or province name. */
        public readonly ?string $state = null,

        /** Additional address details (e.g. apartment, suite, floor). */
        public readonly ?string $complement = null,

        /** City information associated with this address. */
        public readonly ?City $city = null,
    ) {}
}

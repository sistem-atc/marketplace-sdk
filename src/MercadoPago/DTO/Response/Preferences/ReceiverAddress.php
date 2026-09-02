<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\City;

/**
 * Preference Receiver Address resource. Represents the shipping destination address within a
 * preference's shipment configuration. Extends the common Address with additional fields for
 * country name, state name, floor, apartment, and city name.
 */
final class ReceiverAddress implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Country. */
        public readonly ?string $countryName = null,

        /** State. */
        public readonly ?string $stateName = null,

        /** Floor. */
        public readonly ?string $floor = null,

        /** Apartment. */
        public readonly ?string $apartment = null,

        /** City name. */
        public readonly ?string $cityName = null,

        /** Unique identifier of the address. */
        public readonly ?string $id = null,

        /** Postal/ZIP code of the address. */
        public readonly ?string $zipCode = null,

        /** Name of the street. */
        public readonly ?string $streetName = null,

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

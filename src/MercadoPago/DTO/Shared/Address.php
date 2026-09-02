<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Shared;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a physical address associated with a payer or shipment in the MercadoPago API. Used
 * as a nested DTO within payer information, additional info, and shipment details to describe
 * street-level location data.
 */
final class Address implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
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

        /** Floor number within a building. */
        public readonly ?string $floor = null,

        /** Apartment or unit identifier within a floor. */
        public readonly ?string $apartment = null,

        /** City information associated with this address. */
        public readonly ?City $city = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Identification;

/**
 * Preference Passenger resource. Represents a passenger associated with a travel-related
 * preference item. Fields are mapped to common DTOs: - identification -> Identification
 */
final class Passenger implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Passenger's first name. */
        public readonly ?string $firstName = null,

        /** Passenger's last name. */
        public readonly ?string $lastName = null,

        /** Passenger's identification document. */
        public readonly ?Identification $identification = null,
    ) {}
}

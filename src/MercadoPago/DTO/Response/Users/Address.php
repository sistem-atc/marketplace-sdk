<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Address resource. Represents the registered address of a MercadoPago/MercadoLibre user
 * account, including street address, city, state code, and postal code.
 */
final class Address implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The user's address. */
        public readonly ?string $address = null,

        /** The city where the user is located. */
        public readonly ?string $city = null,

        /** The state code where the user is located (e.g., "BR-SP" for São Paulo, Brazil). */
        public readonly ?string $state = null,

        /** The ZIP code of the user's location. */
        public readonly ?string $zipCode = null,
    ) {}
}

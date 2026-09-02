<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Shared;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a phone number associated with a payer or contact in the MercadoPago API. Used as a
 * nested DTO within payer and additional info structures to capture contact telephone details.
 */
final class Phone implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Country or regional area code (e.g. "11" for Sao Paulo). */
        public readonly ?string $areaCode = null,

        /** Phone number without area code. */
        public readonly ?string $number = null,

        /** Phone extension number, if applicable. */
        public readonly ?string $extension = null,
    ) {}
}

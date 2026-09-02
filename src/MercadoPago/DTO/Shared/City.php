<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Shared;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a city within the MercadoPago API. Used as a nested DTO inside Address to identify
 * the city portion of a physical address.
 */
final class City implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** MercadoPago internal identifier for the city. */
        public readonly ?string $id = null,

        /** Human-readable name of the city. */
        public readonly ?string $name = null,
    ) {}
}

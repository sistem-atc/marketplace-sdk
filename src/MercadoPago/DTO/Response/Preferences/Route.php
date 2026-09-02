<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference Route resource. Represents a travel route associated with a travel-related
 * preference item.
 */
final class Route implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Departure location. */
        public readonly ?string $departure = null,

        /** Destination location. */
        public readonly ?string $destination = null,

        /** ISO 8601 date and time of departure. */
        public readonly ?string $departureDateTime = null,

        /** ISO 8601 date and time of arrival. */
        public readonly ?string $arrivalDateTime = null,

        /** Carrier or company name. */
        public readonly ?string $company = null,
    ) {}
}

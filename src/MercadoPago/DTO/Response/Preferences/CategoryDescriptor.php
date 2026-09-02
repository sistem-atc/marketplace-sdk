<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference Category Descriptor resource. Provides additional category-specific metadata for a
 * preference item, typically used for travel or event-related purchases (e.g. passenger name,
 * route, event date). Fields are mapped to typed DTOs: - passenger -> Passenger - route -> Route
 */
final class CategoryDescriptor implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Event date. */
        public readonly ?string $eventDate = null,

        /** Passenger information. */
        public readonly ?Passenger $passenger = null,

        /** Route information. */
        public readonly ?Route $route = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the application that originated the payment interaction. Nested within
 * PointOfInteraction to identify which MercadoPago application or integration generated the
 * payment (e.g. QR code app).
 */
final class ApplicationData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Name of the originating application. */
        public readonly ?string $name = null,

        /** Version of the originating application. */
        public readonly ?string $version = null,
    ) {}
}

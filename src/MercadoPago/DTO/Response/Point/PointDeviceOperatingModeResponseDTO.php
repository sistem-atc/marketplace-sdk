<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Point Device Operating Mode resource. Represents the operating mode configuration of a Point
 * smart terminal device. The operating mode determines how the device processes transactions
 * (e.g. "PDV" for point-of-sale integration, "STANDALONE" for independent use).
 */
final class PointDeviceOperatingModeResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Operating mode. */
        public readonly ?string $operatingMode = null,
    ) {}
}

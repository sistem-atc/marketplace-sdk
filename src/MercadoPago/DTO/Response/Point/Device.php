<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Point Device resource. Represents a MercadoPago Point smart terminal device. Each device is
 * identified by its unique ID and can be associated with a point-of-sale (POS) and store. The
 * operating mode determines how the device processes payment intents.
 */
final class Device implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Device ID. */
        public readonly ?string $id = null,

        /** POS ID. */
        public readonly ?int $posId = null,

        /** Store ID. */
        public readonly int|string|null $storeId = null,

        /** External POS ID. */
        public readonly ?string $externalPosId = null,

        /** Operating mode. */
        public readonly ?string $operatingMode = null,
    ) {}
}

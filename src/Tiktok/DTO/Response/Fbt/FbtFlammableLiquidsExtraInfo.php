<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Liquido inflamavel (hazmat tipo FLAMMABLE_LIQUID). Volume e' STRING. */
final class FbtFlammableLiquidsExtraInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // PAINT | PERFUMERY | ALCOHOLIC | OTHER
        public readonly ?string $flammableLiquidsType = null,
        public readonly ?string $flammableLiquidsVolume = null,
        public readonly ?string $volumeUnit = null,
    ) {}
}

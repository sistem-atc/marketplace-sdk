<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Dimensoes do goods — STRINGs com ate' 3 casas, unidade em `unit`
 * (MILLIMETER|CENTIMETER|METER|FOOT|INCH|MICROMETER).
 */
final class FbtDimension implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $width = null,
        public readonly ?string $length = null,
        public readonly ?string $height = null,
        public readonly ?string $unit = null,
    ) {}
}

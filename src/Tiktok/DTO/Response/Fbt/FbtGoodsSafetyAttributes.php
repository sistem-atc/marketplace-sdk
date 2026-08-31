<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Atributos de manuseio (fragil/liquido/cortante) exigidos na embalagem. */
final class FbtGoodsSafetyAttributes implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $isFragile = null,
        public readonly ?bool $isLiquid = null,
        public readonly ?bool $isSharp = null,
    ) {}
}

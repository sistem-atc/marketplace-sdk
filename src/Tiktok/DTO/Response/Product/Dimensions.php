<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Dimensões (`package_dimensions` e `skus[].sku_dimensions` — mesma shape).
 * Medidas são STRING no TikTok (padrão do MP).
 */
final class Dimensions implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $length = null,
        public readonly ?string $width = null,
        public readonly ?string $height = null,
        public readonly ?string $unit = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Dimensoes da embalagem do produto global.
 *
 * Sao STRING na API ("10", "10.5") — tipar float perderia o formato e
 * quebraria o roundtrip. `unit` e' CENTIMETER ou INCH.
 */
final class GlobalPackageDimensions implements DTOInterface
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

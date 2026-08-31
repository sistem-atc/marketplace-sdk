<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado da otimizacao de uma imagem.
 *
 * `optimizeStatus` PROCESSING significa que ainda esta' rodando — chame de novo.
 * IGNORE = ja' tinha sido otimizada antes. `optimizedUri/Url` so' valem em SUCCESS.
 */
final class OptimizedImageResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $height = null,
        public readonly ?int $width = null,
        public readonly ?string $originalUri = null,
        public readonly ?string $originalUrl = null,
        public readonly ?string $optimizedUri = null,
        public readonly ?string $optimizedUrl = null,
        public readonly ?string $optimizeStatus = null,
    ) {}
}

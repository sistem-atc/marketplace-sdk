<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Dimensoes do pacote. Medidas sao STRING pelo mesmo motivo do peso. */
final class PackageDimension implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $length = null,
        public readonly ?string $width = null,
        public readonly ?string $height = null,
        /** CM | INCH */
        public readonly ?string $unit = null,
    ) {}
}

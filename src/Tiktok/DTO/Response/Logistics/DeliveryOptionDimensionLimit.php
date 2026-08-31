<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Limite dimensional da opção de entrega. `unit` = CM ou INCH — sempre leia. */
final class DeliveryOptionDimensionLimit implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $maxHeight = null,
        public readonly ?int $maxLength = null,
        public readonly ?int $maxWidth = null,
        public readonly ?string $unit = null,
    ) {}
}

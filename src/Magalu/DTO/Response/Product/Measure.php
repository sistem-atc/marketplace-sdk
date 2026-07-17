<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Medida física (`dimensions[].height|length|width|weight`).
 * `value` é `mixed`: vem ora double (medidas) ora integer (peso) — mixed
 * preserva o tipo original no roundtrip (float truncaria int→float).
 */
final class Measure implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $unit = null,
        public readonly mixed $value = null,
    ) {}
}

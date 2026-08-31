<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Medidas da CAIXA de inbound — todas STRING.
 *
 * Shape diferente do peso/dimensao do goods (FbtGoodsMeasurement): aqui e'
 * plano, com `weightUnit` e `lengthUnit` separados, e as unidades tem valores
 * PROPRIOS: peso usa POUNDS (plural) enquanto o goods usa POUND (singular), e
 * comprimento aceita MICRON aqui contra MICROMETER la'. Nao reaproveite enum
 * entre os dois.
 */
final class FbtBoxMeasurements implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $weight = null,
        // KILOGRAM | GRAM | MILLIGRAM | POUNDS | OUNCE
        public readonly ?string $weightUnit = null,
        public readonly ?string $width = null,
        public readonly ?string $height = null,
        public readonly ?string $length = null,
        // METER | CENTIMETER | MILLIMETER | FOOT | MICRON | INCH
        public readonly ?string $lengthUnit = null,
    ) {}
}

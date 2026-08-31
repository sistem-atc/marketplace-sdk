<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Faixa recomendada (min/max) do webhook TYPE 62.
 *
 * O TikTok manda os numeros como STRING ("450", "12000") — tipar float aqui
 * comeria o formato no roundtrip. A unidade nao mora nesta faixa: vem no `unit`
 * do bloco pai (GRAM ou CENTIMETER).
 */
final class PackageMeasurementRange implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $minValue = null,
        public readonly ?string $maxValue = null,
    ) {}
}

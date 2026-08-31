<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Peso do goods. `value` e' STRING com ate' 3 casas ("1.122") — mesmo motivo do
 * dinheiro: float comeria o zero final e quebraria o roundtrip.
 *
 * A unidade VARIA por regiao (GRAM nos exemplos, POUND nos EUA). Nunca assuma
 * grama: leia `unit` (MILLIGRAM|GRAM|KILOGRAM|POUND|OUNCE).
 */
final class FbtWeight implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $value = null,
        public readonly ?string $unit = null,
    ) {}
}

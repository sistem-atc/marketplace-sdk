<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Beneficio da loja atrelado ao score SPS. `unlockScore` e' 0..5 STRING; compare
 * com `SpsOverview.spsScore`, nunca com o valor bruto de uma metrica.
 */
final class SpsBenefit implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $benefitName = null,
        public readonly ?bool $isUnlocked = null,
        public readonly ?string $unlockScore = null,
    ) {}
}

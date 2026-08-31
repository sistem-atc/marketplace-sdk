<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Comissao do produto na vitrine, em centesimos de por cento (3000 = 30%).
 * `rewardRate` e' o bonus por cima da taxa padrao.
 */
final class ShowcaseCommission implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $rate = null,
        public readonly ?int $rewardRate = null,
    ) {}
}

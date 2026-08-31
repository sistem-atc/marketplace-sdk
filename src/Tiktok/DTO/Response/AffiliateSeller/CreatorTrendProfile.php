<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Ponto da serie temporal do creator (GMV, unidades, seguidores, engajamento)
 * numa janela `startTimestamp`..`endTimestamp`.
 *
 * O `gmv` daqui vem com `symbol` ("$") no lugar de `currency`.
 * `engagementRate` x10.000: 558 = 5,58%.
 */
final class CreatorTrendProfile implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $startTimestamp = null,
        public readonly ?int $endTimestamp = null,
        public readonly ?AffiliateMoney $gmv = null,
        public readonly ?int $unitsSold = null,
        public readonly ?int $follower = null,
        // x10.000: 558 = 5,58%
        public readonly ?int $engagementRate = null,
    ) {}
}

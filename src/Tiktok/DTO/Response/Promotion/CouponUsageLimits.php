<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Tetos de uso do cupom. `redemptionLimit` só existe em ID/MY/PH/TH/SG/VN —
 * no BR vem nulo, e isso não significa ilimitado, significa não aplicável.
 */
final class CouponUsageLimits implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $singleBuyerClaimLimit = null,
        public readonly ?int $totalClaimLimit = null,
        public readonly ?int $redemptionLimit = null,
    ) {}
}

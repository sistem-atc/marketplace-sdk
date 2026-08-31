<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Estatística de uso. RESGATADO (`claimedCount`) != USADO (`redeemedCount`):
 * só o segundo virou desconto em pedido. Só o Get Coupon traz esse bloco — o
 * Search Coupon List NÃO devolve usage_stats.
 */
final class CouponUsageStats implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $claimedCount = null,
        public readonly ?int $redeemedCount = null,
    ) {}
}

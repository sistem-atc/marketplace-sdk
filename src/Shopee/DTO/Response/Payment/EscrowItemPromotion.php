<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Payment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Promoção aplicada no item do escrow (`items[].promotion_list[]`). */
final class EscrowItemPromotion implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $promotionId = null,
        public readonly ?string $promotionType = null,
        public readonly ?float $promotionalDiscount = null,
        public readonly ?string $ruleDisplayName = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Gatilho mínimo de compra. `type` NONE = sem mínimo; MIN_SPEND usa `minSpend`. */
final class CouponThreshold implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?CouponMoney $minSpend = null,
    ) {}
}

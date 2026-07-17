<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Faixa de desconto da assinatura (`subscribe_info.subscribe_promotion_config[]`). */
final class SubscribePromotionConfig implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $discountLevel = null,
        public readonly ?int $minDiscount = null,
        public readonly ?int $maxDiscount = null,
    ) {}
}

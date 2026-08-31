<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Como um creator esta promovendo um produto do convite dirigido.
 */
final class CreatorPromotionProductDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?CreatorPromotionProductBaseInfo $productBaseInfo = null,
        // o creator adicionou o produto ou nao
        public readonly ?bool $productAddStatus = null,
        public readonly ?int $videoCount = null,
        public readonly ?int $liveCount = null,
        public readonly ?bool $isCommissionEffective = null,
    ) {}
}

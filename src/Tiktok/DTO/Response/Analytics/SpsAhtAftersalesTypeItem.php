<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `aht_aftersales_type_items[]` — produtos que mais pesam no tempo de
 * tratativa de pos-venda.
 */
final class SpsAhtAftersalesTypeItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $handleDurationHours = null,
        public readonly ?string $productId = null,
        public readonly ?string $productName = null,
        public readonly ?int $returnOrderCount = null,
        public readonly ?string $topAftersalesTypeName = null,
        public readonly ?int $topTypeOrderCount = null,
        public readonly ?string $imageUrl = null,
    ) {}
}

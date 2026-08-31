<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto vinculado a colaboracao aberta.
 */
final class OpenCollaborationProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        public readonly ?string $mainImageUrl = null,
        // LIVE | OUT_OF_STOCK | SELLER_DEACTIVATE | PLATFORM_DEACTIVATE | GNE_REJECT | DELETE | OTHER
        public readonly ?string $status = null,
        public readonly ?int $inventory = null,
        public readonly ?AffiliatePriceRange $originalPrice = null,
    ) {}
}

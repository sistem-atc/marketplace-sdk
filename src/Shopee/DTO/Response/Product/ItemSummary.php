<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `/api/v2/product/get_item_list` — só identificador + status.
 * Pra dados do anúncio, passe os `itemId` daqui pro `getItemBaseInfo()`.
 */
final class ItemSummary implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $itemId = null,
        public readonly ?string $itemStatus = null,
        public readonly ?int $updateTime = null,
        public readonly ?ItemTag $tag = null,
    ) {}
}

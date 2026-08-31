<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /fbt/202601/mcf/goods/inventory/search`.
 *
 * @property list<FbtMcfGoodsInventory>|null $goodsInventoryList
 */
final class FbtMcfGoodsInventoryResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(FbtMcfGoodsInventory::class)]
        public readonly ?array $goodsInventoryList = null,
    ) {}
}

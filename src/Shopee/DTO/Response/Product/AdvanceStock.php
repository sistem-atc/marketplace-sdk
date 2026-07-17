<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Estoque adiantado do Full/FBS (`stock_info_v2.advance_stock`). */
final class AdvanceStock implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $sellableAdvanceStock = null,
        public readonly ?int $inTransitAdvanceStock = null,
    ) {}
}

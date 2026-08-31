<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Fatia de GMV por tipo de conteudo (VIDEO / LIVE / PRODUCT_CARD).
 * Carrega o proprio objeto monetario — diferente de `ShopGrossRevenueBreakdown`,
 * que traz apenas o PERCENTUAL.
 */
final class ShopGmvBreakdown implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?string $type = null,
    ) {}
}

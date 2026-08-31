<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.skus[]` do Get Shop SKU Performance List.
 *
 * `id` e `productId` sao declarados INT aqui (na listagem), enquanto o endpoint de
 * detalhe declara `sku_id` como STRING. Seguimos a doc de cada endpoint.
 * Note tambem `unitsSold` — o detalhe chama o mesmo numero de `items_sold`.
 */
final class ShopSkuPerformanceSummary implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $productId = null,
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?int $skuOrders = null,
        public readonly ?int $unitsSold = null,
    ) {}
}

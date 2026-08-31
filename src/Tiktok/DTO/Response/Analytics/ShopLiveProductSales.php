<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `sales` de um produto dentro de UMA live (Shop LIVE Products Performance).
 * `paymentRate` = pedidos principais pagos / criados, fracao STRING.
 */
final class ShopLiveProductSales implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?MonetaryValue $directGmv = null,
        public readonly ?int $itemsSold = null,
        public readonly ?int $customers = null,
        public readonly ?int $createdSkuOrders = null,
        public readonly ?MonetaryValue $avgPrice = null,
        public readonly ?int $skuOrders = null,
        public readonly ?int $mainOrders = null,
        public readonly ?string $paymentRate = null,
    ) {}
}

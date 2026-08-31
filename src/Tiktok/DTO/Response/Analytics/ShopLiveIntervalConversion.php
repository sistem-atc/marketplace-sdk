<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conversao do intervalo. `avgPrice` e' AOV por pedido PRINCIPAL (nao por item).
 * `createdSkuOrders` conta pedidos CRIADOS (inclui nao pagos), ao contrario de
 * `ShopLiveIntervalSales.skuOrders`, que so' conta pagos.
 */
final class ShopLiveIntervalConversion implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?MonetaryValue $avgPrice = null,
        public readonly ?LiveClickToOrderRate $clickToOrderRate = null,
        public readonly ?LiveGpm $gpm = null,
        public readonly ?int $createdSkuOrders = null,
        public readonly ?string $skuOrderRate = null,
    ) {}
}

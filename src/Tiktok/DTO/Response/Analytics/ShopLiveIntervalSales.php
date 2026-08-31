<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Venda do intervalo de minutos.
 *
 * Tres contagens que parecem a mesma coisa: `skuOrders` (pedidos de SKU PAGOS),
 * `mainOrders` (pedidos principais pagos — 1 compra do cliente) e `customers`
 * (clientes unicos, inclui quem devolveu/estornou). `itemsSold` sao unidades.
 */
final class ShopLiveIntervalSales implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?int $itemsSold = null,
        public readonly ?int $customers = null,
        public readonly ?int $skuOrders = null,
        public readonly ?int $mainOrders = null,
    ) {}
}

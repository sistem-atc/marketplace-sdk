<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Bloco de vendas do intervalo da loja.
 *
 * `avgCustomersCount` e' media DIARIA de clientes unicos — nao o total do periodo;
 * somar intervalos nao da' o total de clientes.
 */
final class ShopPerformanceSales implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?ShopGmvWithBreakdowns $gmv = null,
        public readonly ?ShopGrossRevenue $grossRevenue = null,
        public readonly ?int $itemsSold = null,
        public readonly ?int $skuOrdersCount = null,
        public readonly ?int $ordersCount = null,
        public readonly ?int $avgCustomersCount = null,
        public readonly ?MonetaryValue $refunds = null,
    ) {}
}

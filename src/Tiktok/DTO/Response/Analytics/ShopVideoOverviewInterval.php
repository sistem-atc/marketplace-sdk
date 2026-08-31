<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Intervalo do Get Shop Video Performance Overview (todos os videos da loja
 * somados). `avgCustomers` e' media DIARIA.
 *
 * `clickThroughRate` e' documentado como fracao ("0.0528" = 5,28%) mas o exemplo
 * oficial mostra "12.5%" ja' formatado — trate como string opaca e confira o
 * formato antes de multiplicar por 100.
 */
final class ShopVideoOverviewInterval implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $startDate = null,
        public readonly ?string $endDate = null,
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?string $clickThroughRate = null,
        public readonly ?int $skuOrders = null,
        public readonly ?int $productClicks = null,
        public readonly ?int $avgCustomers = null,
        public readonly ?int $productImpressions = null,
    ) {}
}

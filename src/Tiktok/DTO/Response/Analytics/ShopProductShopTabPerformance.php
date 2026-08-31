<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `shop_tab_performance`: o produto na aba Shop. Aqui a API repete o prefixo
 * `shop_tab_` em TODOS os campos — mantido igual a' chave crua pra nao criar
 * ambiguidade com os outros canais.
 */
final class ShopProductShopTabPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $shopTabProductImpressions = null,
        public readonly ?int $shopTabProductClicks = null,
        public readonly ?int $uniqueShopTabProductClicks = null,
        public readonly ?int $estimatedShopTabCustomers = null,
        public readonly ?string $shopTabCtr = null,
        public readonly ?string $shopTabCtorSku = null,
        public readonly ?MonetaryValue $shopTabGmv = null,
        public readonly ?int $shopTabSoldItems = null,
    ) {}
}

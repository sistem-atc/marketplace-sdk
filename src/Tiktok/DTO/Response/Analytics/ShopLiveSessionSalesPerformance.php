<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `sales_performance` de uma sessao de LIVE na listagem da loja.
 *
 * `gmv24hLive` NAO e' o GMV da live: e' o total pago em ate' 24h APOS assistir,
 * janela de atribuicao mais larga que a do `gmv`. A chave crua comeca com digito
 * (`24h_live_gmv`), por isso o #[JsonKey] explicito.
 *
 * `clickToOrderRate` aqui vem como STRING JA' PERCENTUAL ("18%"), diferente dos
 * endpoints de minuto/produto, que mandam fracao ("0.0187"). Nao normalize os dois
 * do mesmo jeito.
 */
final class ShopLiveSessionSalesPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?MonetaryValue $gmv = null,
        public readonly ?int $productsAdded = null,
        public readonly ?int $differentProductsSold = null,
        public readonly ?int $createdSkuOrders = null,
        public readonly ?int $skuOrders = null,
        public readonly ?int $itemsSold = null,
        public readonly ?int $customers = null,
        public readonly ?MonetaryValue $avgPrice = null,
        public readonly ?string $clickToOrderRate = null,
        #[JsonKey('24h_live_gmv')]
        public readonly ?MonetaryValue $gmv24hLive = null,
    ) {}
}

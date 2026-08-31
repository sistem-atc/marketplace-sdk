<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Trafego do intervalo. `impressions` sao impressoes da LIVE;
 * `productImpressions`, dos PRODUTOS dentro dela — nao intercambiaveis.
 * `ctr` e `enterRoomRate` sao fracao STRING.
 */
final class ShopLiveIntervalTraffic implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $viewers = null,
        public readonly ?int $views = null,
        public readonly ?int $productImpressions = null,
        public readonly ?string $ctr = null,
        public readonly ?string $enterRoomRate = null,
        public readonly ?int $productClicks = null,
        public readonly ?int $impressions = null,
    ) {}
}

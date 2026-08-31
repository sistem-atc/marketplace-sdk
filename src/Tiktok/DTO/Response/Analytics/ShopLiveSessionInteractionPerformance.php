<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `interaction_performance` de uma sessao de LIVE. So' vem quando o
 * `account_type` consultado e' OFFICIAL_ACCOUNTS ou MARKETING_ACCOUNTS.
 *
 * `acu`/`pcu` = espectadores simultaneos medio/pico. `avgViewingDuration` e'
 * STRING em SEGUNDOS ("46"). `clickThroughRate` vem percentual ("13.99%").
 */
final class ShopLiveSessionInteractionPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $acu = null,
        public readonly ?int $pcu = null,
        public readonly ?int $viewers = null,
        public readonly ?int $views = null,
        public readonly ?string $avgViewingDuration = null,
        public readonly ?int $comments = null,
        public readonly ?int $shares = null,
        public readonly ?int $likes = null,
        public readonly ?int $newFollowers = null,
        public readonly ?int $productImpressions = null,
        public readonly ?int $productClicks = null,
        public readonly ?string $clickThroughRate = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.videos[]` do /analytics/{v}/videos/bestselling.
 *
 * `views`/`likes`/`comments`/`shares` sao ACUMULADOS desde a publicacao do video,
 * nao a janela consultada — diferente das metricas de `ShopVideoPerformanceSummary`,
 * que sao do periodo.
 */
final class BestsellingVideo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $rank = null,
        public readonly ?string $id = null,
        public readonly ?string $nickName = null,
        public readonly ?string $gmvRange = null,
        public readonly ?int $views = null,
        public readonly ?int $likes = null,
        public readonly ?int $comments = null,
        public readonly ?int $shares = null,
        #[ArrayOf(BestsellingVideoProductInfo::class)]
        public readonly ?array $productInfos = null,
        public readonly ?int $publishTime = null,
        public readonly ?int $duration = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Metricas diarias de e-commerce de um video (API 202403).
 * `anchorDisplayRate`/`clickThroughRate` sao fracao STRING com 2 casas.
 */
final class VideoMetrics implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $anchorDisplayRate = null,
        public readonly ?string $clickThroughRate = null,
        public readonly ?int $orderCount = null,
        public readonly ?int $itemSoldCount = null,
        public readonly ?MonetaryValue $gmv = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Um dia da serie de `performances[]`, em ordem crescente de `start_time`.
 */
final class VideoPerformanceEntry implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?VideoTimeRange $timeRange = null,
        public readonly ?VideoMetrics $metrics = null,
    ) {}
}

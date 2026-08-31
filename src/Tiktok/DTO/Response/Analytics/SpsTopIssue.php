<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Ofensor do SPS ranqueado (`rank` 1 = pior). `percentile` e' 0..100 comparando com
 * lojas pares; note que a janela de avaliacao do ISSUE (30d no exemplo) e'
 * diferente da janela do score geral (90d).
 */
final class SpsTopIssue implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $description = null,
        public readonly ?int $evaluateDurationDays = null,
        public readonly ?int $endEvaluationTime = null,
        public readonly ?int $startEvaluationTime = null,
        public readonly ?string $metricCode = null,
        public readonly ?string $metricName = null,
        public readonly ?string $percentile = null,
        public readonly ?int $rank = null,
    ) {}
}

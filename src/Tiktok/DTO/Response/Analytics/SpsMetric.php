<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.metrics[]` do Get SPS Metrics — a metrica SPS no nivel RESUMO
 * (sem diagnostico). O conjunto e' fixo pra loja autorizada.
 *
 * `topReasonText` so' vem preenchido quando a metrica e' apontada como principal
 * alavanca do SPS.
 */
final class SpsMetric implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?SpsBenchmarks $benchmarks = null,
        public readonly ?string $dimension = null,
        public readonly ?int $evaluateDurationDays = null,
        public readonly ?int $endEvaluationTime = null,
        public readonly ?int $startEvaluationTime = null,
        public readonly ?string $metricCode = null,
        public readonly ?string $metricName = null,
        public readonly ?string $score = null,
        public readonly ?string $status = null,
        public readonly ?string $statusText = null,
        public readonly ?string $value = null,
        public readonly ?string $valueUnit = null,
        public readonly ?string $topReasonText = null,
    ) {}
}

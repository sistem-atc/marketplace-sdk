<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do /analytics/{v}/shop_performances/metrics/{metric_code}/diagnosis —
 * o drill-down de UMA metrica SPS.
 *
 * Nao confunda `value` com `score`: `score` e' 0..5 (a nota que compoe o SPS) e
 * `value` e' a metrica bruta, cuja unidade vem em `valueUnit` (PERCENT = 0..100,
 * ou fracao 0..1 quando a metrica e' razao; HOURS = horas). Ambos STRING.
 */
final class SpsMetricDiagnosis implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?SpsBenchmarks $benchmarks = null,
        public readonly ?SpsCalculationRule $calculationRule = null,
        public readonly ?string $dimension = null,
        #[ArrayOf(SpsDistributionDetail::class)]
        public readonly ?array $distributionDetails = null,
        public readonly ?int $evaluateDurationDays = null,
        public readonly ?int $endEvaluationTime = null,
        public readonly ?int $startEvaluationTime = null,
        // NRR | NBFR | SFCR | OTDR | AHT | IM_DSAT
        public readonly ?string $metricCode = null,
        public readonly ?string $score = null,
        // EXCELLENT | GOOD | POOR | CRITICAL | NIL
        public readonly ?string $status = null,
        public readonly ?string $statusText = null,
        public readonly ?SpsMetricTrend $trend = null,
        public readonly ?string $value = null,
        public readonly ?string $valueUnit = null,
        #[ArrayOf(SpsMetricAnalysis::class)]
        public readonly ?array $analyses = null,
    ) {}
}

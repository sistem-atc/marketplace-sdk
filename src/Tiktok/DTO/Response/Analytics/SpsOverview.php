<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do /analytics/{v}/shop_performances/overview — o painel SPS da loja.
 *
 * `spsScore` e' 0..5 com uma casa; `peerPercentile` e' 0..100. `spsTier` e' o enum
 * (EXCELLENT/GOOD/POOR/CRITICAL/NIL) e `spsTierText` o rotulo ja' traduzido — no
 * exemplo oficial os dois DIVERGEM (GOOD vs "Excellent"), entao nunca derive um do
 * outro. Datas em epoch de SEGUNDOS.
 */
final class SpsOverview implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(SpsBenefit::class)]
        public readonly ?array $benefits = null,
        #[ArrayOf(SpsDimension::class)]
        public readonly ?array $dimensions = null,
        public readonly ?int $evaluateDurationDays = null,
        public readonly ?int $endEvaluationTime = null,
        public readonly ?int $startEvaluationTime = null,
        public readonly ?string $peerPercentile = null,
        public readonly ?SpsPrimaryCategory $primaryCategory = null,
        public readonly ?string $spsScore = null,
        public readonly ?string $spsTier = null,
        public readonly ?string $spsTierText = null,
        public readonly ?SpsTopIssues $topIssues = null,
        public readonly ?int $updateTime = null,
    ) {}
}

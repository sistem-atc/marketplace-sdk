<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do .../metrics/{metric_code}/problem_details.
 *
 * So' vem preenchida a lista da metrica consultada (`metricCode`); as outras cinco
 * chegam ausentes. Nao itere as seis achando que somam — cada uma tem shape
 * proprio, por isso sao DTOs distintos.
 */
final class SpsMetricProblemDetails implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(SpsAhtProblemOrderItem::class)]
        public readonly ?array $ahtProblemOrderItems = null,
        #[ArrayOf(SpsImDsatChatItem::class)]
        public readonly ?array $imDsatChatItems = null,
        public readonly ?string $metricCode = null,
        #[ArrayOf(SpsNbfrProblemOrderItem::class)]
        public readonly ?array $nbfrProblemOrderItems = null,
        public readonly ?string $nextPageToken = null,
        #[ArrayOf(SpsNrrProblemOrderItem::class)]
        public readonly ?array $nrrProblemOrderItems = null,
        #[ArrayOf(SpsOtdrProblemOrderItem::class)]
        public readonly ?array $otdrProblemOrderItems = null,
        #[ArrayOf(SpsSfcrProblemOrderItem::class)]
        public readonly ?array $sfcrProblemOrderItems = null,
        public readonly ?int $totalCount = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do .../metrics/{metric_code}/top_items — os piores ofensores da metrica.
 *
 * Como no problem_details, so' a lista correspondente ao `metricCode` vem
 * preenchida. IM_DSAT e SFCR nao tem top items (a doc lista so' NRR, NBFR, OTDR e AHT).
 */
final class SpsMetricTopItems implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(SpsAhtAftersalesTypeItem::class)]
        public readonly ?array $ahtAftersalesTypeItems = null,
        public readonly ?string $metricCode = null,
        #[ArrayOf(SpsNbfrTopProductItem::class)]
        public readonly ?array $nbfrTopProductItems = null,
        #[ArrayOf(SpsNrrTopProductItem::class)]
        public readonly ?array $nrrTopProductItems = null,
        #[ArrayOf(SpsOtdrLogisticsItem::class)]
        public readonly ?array $otdrLogisticsItems = null,
        public readonly ?int $totalCount = null,
    ) {}
}

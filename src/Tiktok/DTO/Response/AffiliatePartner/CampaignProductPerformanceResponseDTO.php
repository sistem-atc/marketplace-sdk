<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Resposta de GET .../campaigns/{id}/products/performance. */
final class CampaignProductPerformanceResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $totalCount = null,
        #[ArrayOf(CampaignProductStatistic::class)]
        public readonly ?array $campaignProductStatistics = null,
        public readonly ?string $nextPageToken = null,
    ) {}
}

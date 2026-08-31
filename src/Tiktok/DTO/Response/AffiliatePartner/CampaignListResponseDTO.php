<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Resposta de GET /affiliate_partner/{v}/campaigns. */
final class CampaignListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(CampaignSummary::class)]
        public readonly ?array $campaigns = null,
        public readonly ?string $nextPageToken = null,
        /** Total no filtro, não na página. */
        public readonly ?int $totalCount = null,
    ) {}
}

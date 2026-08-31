<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Resposta de .../products/{product_id}/performance. */
final class CampaignCreatorPerformanceResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $totalCreatorCount = null,
        #[ArrayOf(PromotionCreator::class)]
        public readonly ?array $promotionCreators = null,
        public readonly ?string $nextPageToken = null,
    ) {}
}

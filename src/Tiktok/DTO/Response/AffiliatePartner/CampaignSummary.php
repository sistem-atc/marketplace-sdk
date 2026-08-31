<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Campanha na LISTAGEM — versão magra do detalhe.
 *
 * A lista não traz description/region/commission_rate/contact_info/target_*;
 * quem precisa disso chama getCampaignDetail() por id.
 */
final class CampaignSummary implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        /** READY | UPCOMING | ONGOING | CLOSED */
        public readonly ?string $status = null,
        public readonly ?int $registrationStartTime = null,
        public readonly ?int $registrationEndTime = null,
        public readonly ?int $campaignStartTime = null,
        public readonly ?int $campaignEndTime = null,
    ) {}
}

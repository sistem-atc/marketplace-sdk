<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Estatística de um produto da campanha.
 *
 * Os contadores do topo repetem, como int, campos que `indicator_data` traz
 * como string. Os do topo são o snapshot resumido; os de dentro, a série que
 * separa estimado de realizado.
 */
final class CampaignProductStatistic implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** STRING, epoch em MILISSEGUNDOS — os dados são consolidados com atraso. */
        public readonly ?string $dataUpdateTime = null,
        public readonly ?int $creatorSalesNum = null,
        public readonly ?int $collaboratedCreatorsNum = null,
        public readonly ?int $promotedCreatorNum = null,
        public readonly ?int $sampleRequestedCreatorNum = null,
        public readonly ?CampaignProductDetail $campaignProductDetail = null,
    ) {}
}

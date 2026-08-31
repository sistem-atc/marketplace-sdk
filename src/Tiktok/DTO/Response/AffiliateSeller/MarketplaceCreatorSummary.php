<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Creator na busca do marketplace.
 *
 * DADO EXATO E' OPCIONAL: se o creator nao autorizou compartilhar numero
 * preciso, `gmv`/`liveGmv`/`videoGmv` somem e sobra so' a FAIXA
 * (`gmvRange`/`unitsSoldRange`) — e faixa so' aparece com mercado alvo US.
 * Codigo que le apenas `gmv` enxerga zero num creator que vende bem.
 */
final class MarketplaceCreatorSummary implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $username = null,
        public readonly ?string $nickname = null,
        public readonly ?AffiliateImage $avatar = null,
        public readonly ?string $selectionRegion = null,
        public readonly ?array $categoryIds = null,
        public readonly ?int $avgEcLiveUv = null,
        public readonly ?int $avgEcVideoViewCount = null,
        public readonly ?int $followerCount = null,
        // omitido quando o creator nao autoriza dado exato
        public readonly ?AffiliateMoney $gmv = null,
        public readonly ?AffiliateMoney $liveGmv = null,
        public readonly ?AffiliateMoney $videoGmv = null,
        // fallback quando nao ha dado exato (mercado US)
        public readonly ?AffiliatePriceRange $gmvRange = null,
        public readonly ?AffiliateCountRange $unitsSoldRange = null,
        public readonly ?TopFollowerDemographics $topFollowerDemographics = null,
        public readonly ?string $creatorOpenId = null,
        public readonly ?MarketplaceCreatorSearchProfile $creatorProfile = null,
    ) {}
}

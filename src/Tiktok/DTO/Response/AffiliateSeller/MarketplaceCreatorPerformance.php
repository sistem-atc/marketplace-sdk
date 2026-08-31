<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Desempenho completo do creator — a ficha que sustenta a decisao de investir
 * comissao nele.
 *
 * TRES ARMADILHAS DE UNIDADE, todas presentes ao mesmo tempo:
 * 1. Dinheiro (`gmv`, `gpm`, `avgGmvPerBuyer`) e' STRING.
 * 2. Taxas com nome `*_engagement_rate`, `postRate` e `ecLiveEngagementRate`
 *    vem como STRING x10.000 ("6000" = 60%).
 * 3. As distribuicoes (`followerLocation`, `categoryGmvDistribution`...) vem
 *    como FRACAO em string ("0.3705" = 37,05%). Duas convencoes de
 *    porcentagem no MESMO objeto.
 *
 * DADO EXATO E' OPCIONAL: sem autorizacao do creator, os campos precisos
 * somem e sobram os `*Range` (e `*Range` so' existe pra mercado US).
 *
 * `gpm` = GMV por mil visualizacoes.
 */
final class MarketplaceCreatorPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $username = null,
        public readonly ?string $nickname = null,
        public readonly ?AffiliateImage $avatar = null,
        public readonly ?string $selectionRegion = null,
        public readonly ?string $bioDescription = null,
        public readonly ?int $followerCount = null,
        // deeplink aweme:// do perfil, nao URL http
        public readonly ?string $profileTtUri = null,
        public readonly ?array $categoryIds = null,
        // top 10 marcas com que ja colaborou
        public readonly ?array $topCollaboratedBrandIds = null,
        public readonly ?int $brandCollaborationCount = null,
        public readonly ?int $unitsSold = null,
        public readonly ?AffiliateCountRange $unitsSoldRange = null,
        public readonly ?AffiliateMoney $gmv = null,
        public readonly ?AffiliateMoney $videoGmv = null,
        public readonly ?AffiliateMoney $liveGmv = null,
        public readonly ?AffiliatePriceRange $gmvRange = null,
        // x10.000: "6000" = 60%
        public readonly ?string $ecLiveEngagementRate = null,
        #[ArrayOf(CategoryGmvShare::class)]
        public readonly ?array $categoryGmvDistribution = null,
        #[ArrayOf(ContentGmvShare::class)]
        public readonly ?array $contentGmvDistribution = null,
        public readonly ?AffiliatePriceRange $productOriginalPriceRange = null,
        // GMV por mil views
        public readonly ?AffiliateMoney $gpm = null,
        public readonly ?AffiliateMoney $liveGpm = null,
        public readonly ?AffiliateMoney $videoGpm = null,
        public readonly ?AffiliatePriceRange $gpmRange = null,
        public readonly ?AffiliatePriceRange $videoGpmRange = null,
        public readonly ?AffiliatePriceRange $liveGpmRange = null,
        public readonly ?int $promotedProductNum = null,
        public readonly ?int $ecLiveCount = null,
        public readonly ?int $ecVideoCount = null,
        public readonly ?int $avgEcVideoPlayCount = null,
        // centesimos de %: 6000 = 60%
        public readonly ?int $avgCommissionRate = null,
        public readonly ?AffiliateCountRange $avgCommissionRateRange = null,
        public readonly ?AffiliateMoney $avgGmvPerBuyer = null,
        public readonly ?AffiliatePriceRange $avgGmvPerBuyerRange = null,
        public readonly ?int $avgEcLiveViewCount = null,
        public readonly ?int $avgEcLiveLikeCount = null,
        public readonly ?int $avgEcLiveCommentCount = null,
        public readonly ?int $avgEcLiveShareCount = null,
        public readonly ?int $avgEcVideoLikeCount = null,
        public readonly ?int $avgEcVideoCommentCount = null,
        public readonly ?int $avgEcVideoShareCount = null,
        #[ArrayOf(FollowerDistributionEntry::class)]
        public readonly ?array $followerLocation = null,
        #[ArrayOf(FollowerDistributionEntry::class)]
        public readonly ?array $followerAge = null,
        #[ArrayOf(FollowerDistributionEntry::class)]
        public readonly ?array $followerGender = null,
        // Promotion Performance Score dos ultimos 90 dias, ex.: "4.5"
        public readonly ?string $pps = null,
        // nota dada por sellers que ja trabalharam com ele
        public readonly ?string $rating = null,
        // x10.000: "3000" = 30%
        public readonly ?string $ecVideoEngagementRate = null,
        // x10.000; chance de postar apos receber amostra
        public readonly ?string $postRate = null,
        public readonly ?MarketplaceCreatorAdvancedProfile $creatorProfile = null,
    ) {}
}

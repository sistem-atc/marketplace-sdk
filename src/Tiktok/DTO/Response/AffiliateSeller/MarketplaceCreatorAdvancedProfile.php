<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Perfil avancado do creator no endpoint de PERFORMANCE. Objeto vazio = filtro
 * nao liberado pra conta/regiao.
 *
 * Todos os `*_30d`/`*_90d` precisam de #[JsonKey]: o conversor camel->snake do
 * SDK nao insere `_` antes de digito (`liveEngagementRate30d` viraria
 * `live_engagement_rate30d`) e o campo sumiria em silencio.
 */
final class MarketplaceCreatorAdvancedProfile implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $isOfficialRecommend = null,
        public readonly ?bool $contactInfoAvailable = null,
        public readonly ?string $creatorBindMcnName = null,
        #[JsonKey('live_engagement_rate_30d')]
        public readonly ?int $liveEngagementRate30d = null,
        #[JsonKey('video_play_cnt_30d_med')]
        public readonly ?int $videoPlayCnt30dMed = null,
        #[JsonKey('live_view_cnt_30d_med')]
        public readonly ?int $liveViewCnt30dMed = null,
        #[JsonKey('live_like_cnt_30d')]
        public readonly ?int $liveLikeCnt30d = null,
        #[JsonKey('live_comment_cnt_30d_med')]
        public readonly ?int $liveCommentCnt30dMed = null,
        #[JsonKey('live_share_cnt_30d_med')]
        public readonly ?int $liveShareCnt30dMed = null,
        #[JsonKey('video_publish_cnt_30d_med')]
        public readonly ?int $videoPublishCnt30dMed = null,
        #[JsonKey('has_invited_90d')]
        public readonly ?bool $hasInvited90d = null,
        public readonly ?bool $isCreatorBlocked = null,
        public readonly ?int $creatorLevel = null,
        public readonly ?int $creatorFulfillmentReviews = null,
        public readonly ?bool $isEligibleFlatFee = null,
        public readonly ?bool $isRisingStar = null,
        #[JsonKey('creator_fulfillment_score_90d')]
        public readonly ?string $creatorFulfillmentScore90d = null,
        // credito de amostra: 75 = 75/100
        public readonly ?int $sampleScore = null,
        public readonly ?CreatorConnectInfo $creatorConnectInfo = null,
        #[ArrayOf(CreatorTrendProfile::class)]
        public readonly ?array $creatorTrendProfile = null,
    ) {}
}

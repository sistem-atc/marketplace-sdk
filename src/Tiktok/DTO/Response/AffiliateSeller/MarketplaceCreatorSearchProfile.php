<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Selos avancados do creator na BUSCA. Objeto vazio = o filtro nao esta
 * liberado pra sua conta/regiao (nao e' erro).
 *
 * Nao confundir com o `creator_profile` do endpoint de PERFORMANCE: os dois
 * usam a mesma chave com campos diferentes.
 */
final class MarketplaceCreatorSearchProfile implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $isOfficialRecommend = null,
        // agencia (MCN) ligada ao creator
        public readonly ?string $creatorBindMcnName = null,
        public readonly ?bool $isFastGrowing = null,
        public readonly ?bool $hasCollaborated = null,
        // 1=creator privado, 2=carve out
        public readonly ?array $userTypeTag = null,
        #[JsonKey('has_invited_90d')]
        public readonly ?bool $hasInvited90d = null,
        public readonly ?bool $isCreatorBlocked = null,
        // Promotion Performance Score, ex.: "4.3" de 5,0
        public readonly ?string $ppsScore = null,
        // so seller local US
        public readonly ?bool $isEligibleFlatFee = null,
        public readonly ?bool $isRisingStar = null,
        public readonly ?int $creatorLevel = null,
    ) {}
}

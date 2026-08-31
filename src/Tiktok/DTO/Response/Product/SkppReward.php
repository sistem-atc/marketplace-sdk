<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Recompensa do SKPP (Shop Key Product Program).
 *
 * `value` e' STRING e vem prefixado com a moeda quando o tipo e' ADS_CREDIT
 * ("$15"); em TOP_CREATOR_MATCHMAKING/SHOPTAB_PV vem numero puro.
 * `adCreditClaimStatus` so' aparece em ADS_CREDIT com status AVAILABLE — o
 * credito de anuncio precisa ser resgatado MANUALMENTE, nao cai sozinho.
 * `title`, `claimConditions` e `campaignId` so' vem no detalhe por produto.
 */
final class SkppReward implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $rewardType = null,
        public readonly ?string $title = null,
        public readonly ?string $status = null,
        public readonly ?string $adCreditClaimStatus = null,
        public readonly ?string $value = null,
        #[ArrayOf(SkppClaimCondition::class)]
        public readonly ?array $claimConditions = null,
        public readonly ?string $campaignId = null,
    ) {}
}

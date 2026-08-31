<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Detalhe de uma campanha de affiliate partner.
 *
 * `commission_rate` é CENTÉSIMO DE PONTO PERCENTUAL: 1000 = 10,00%. Quem
 * dividir por 100 achando que é percentual erra o valor por 100×.
 */
final class CampaignDetailResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        /** READY | UPCOMING | ONGOING | CLOSED */
        public readonly ?string $status = null,
        public readonly ?string $region = null,
        /** Epoch em SEGUNDOS. */
        public readonly ?int $registrationStartTime = null,
        public readonly ?int $registrationEndTime = null,
        public readonly ?int $campaignStartTime = null,
        public readonly ?int $campaignEndTime = null,
        /** Centésimos de %: 1000 = 10,00%. */
        public readonly ?int $commissionRate = null,
        public readonly ?ContactInfo $contactInfo = null,
        #[ArrayOf(TargetShop::class)]
        public readonly ?array $targetShops = null,
        /** LOCAL | CROSS_BORDER | ... — lista de strings cruas. */
        public readonly ?array $targetSellerTypes = null,
    ) {}
}

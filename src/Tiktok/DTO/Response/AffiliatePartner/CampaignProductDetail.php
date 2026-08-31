<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto da campanha na visão de performance.
 *
 * Aqui as comissões chamam `*_percent` e vêm como STRING — no Campaign Product
 * List os mesmos números chamam `*_rate` e vêm como int. Mesma unidade
 * (centésimo de %), nomes e tipos diferentes: não dá pra reusar o mesmo DTO.
 */
final class CampaignProductDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productId = null,
        /** PRODUCT_UNSPECIFIED | PRODUCT_PENDING | PRODUCT_APPROVED | PRODUCT_REJECTED | … */
        public readonly ?string $productStatus = null,
        public readonly ?string $productName = null,
        public readonly ?string $productStockCount = null,
        /** total = creator + partner, em centésimos de %. */
        public readonly ?string $totalCommissionPercent = null,
        public readonly ?string $creatorCommissionPercent = null,
        public readonly ?string $partnerCommissionPercent = null,
        /** Comissão da colaboração aberta — fora da soma acima. */
        public readonly ?string $planCommissionPercent = null,
        public readonly ?ProductPriceRange $productPrice = null,
        public readonly ?ProductThumbnail $productThumbnail = null,
        public readonly ?IndicatorData $indicatorData = null,
    ) {}
}

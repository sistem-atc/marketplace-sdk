<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pedido de afiliado no modelo TAP (TikTok Affiliate Partner).
 *
 * Mesma unidade e mesma lógica estimado × real do CAP, mas o eixo do rateio
 * muda: no CAP a comissão se divide entre criador e AGÊNCIA; aqui, entre
 * criador e PARCEIRO — e por isso os campos são `creator_*`/`partner_*` em vez
 * de `standard`/`agency`. Não são o mesmo dado com outro nome; os dois
 * endpoints cobrem programas diferentes.
 */
final class TapAffiliateSkuOrder implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Id do pedido PRINCIPAL da transação. */
        public readonly ?string $id = null,
        /** Epoch em SEGUNDOS, UTC+0. */
        public readonly ?int $createTime = null,
        public readonly ?int $deliveryTime = null,
        /** CUSTOMER UNPAID | PENDING | SETTLED | … — só SETTLED gera comissão paga. */
        public readonly ?string $settleStatus = null,
        public readonly ?string $skuId = null,
        /** Campanha TAP que originou a venda. */
        public readonly ?string $campaignId = null,
        public readonly ?string $creatorUsername = null,
        public readonly ?string $productName = null,
        public readonly ?string $productId = null,
        public readonly ?Money $price = null,
        public readonly ?int $quantity = null,
        /** SHOP | VIDEO | LIVE | PRE_LIVE | PROMOTION_P… */
        public readonly ?string $contentType = null,
        public readonly ?string $contentId = null,
        /** Centésimos de %. Entre TAP e criador. */
        public readonly ?int $creatorStandardCommissionRate = null,
        /** Entre vendedor e TAP. */
        public readonly ?int $partnerStandardCommissionRate = null,
        public readonly ?int $creatorShopAdsCommissionRate = null,
        public readonly ?int $partnerShopAdsCommissionRate = null,
        public readonly ?int $partnerTapBonusCommissionRate = null,
        public readonly ?int $creatorTapBonusCommissionRate = null,
        /** Preço × quantidade na criação do pedido. */
        public readonly ?Money $estimatedCommissionBase = null,
        public readonly ?Money $estimatedCreatorStandardCommission = null,
        public readonly ?Money $estimatedPartnerStandardCommission = null,
        public readonly ?Money $estimatedCreatorShopAdsCommission = null,
        public readonly ?Money $estimatedPartnerShopAdsCommission = null,
        public readonly ?Money $estimatedPartnerTapBonusCommission = null,
        public readonly ?Money $estimatedCreatorTapBonusCommission = null,
        /** Retenção só de parceiro no México. */
        public readonly ?Money $estimatedPartnerIsr = null,
        public readonly ?Money $estimatedPartnerIva = null,
        /** Já exclui devolvido/reembolsado. */
        public readonly ?Money $actualCommissionBase = null,
        public readonly ?Money $actualCreatorStandardCommission = null,
        public readonly ?Money $actualPartnerStandardCommission = null,
        public readonly ?Money $actualCreatorShopAdsCommission = null,
        public readonly ?Money $actualPartnerShopAdsCommission = null,
        public readonly ?Money $actualPartnerIsr = null,
        public readonly ?Money $actualPartnerIva = null,
        public readonly ?Money $actualPartnerTapBonusCommission = null,
        public readonly ?Money $actualCreatorTapBonusCommission = null,
        /** STRING "Yes"/"No", não bool. */
        public readonly ?string $fullyReturn = null,
        /** Epoch em SEGUNDOS, UTC+0. */
        public readonly ?int $settlementTime = null,
        public readonly ?int $lastUpdateTime = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pedido de afiliado no modelo CAP (Creator Affiliate Program).
 *
 * O objeto é uma linha de SKU, não o pedido inteiro: o mesmo `id` de pedido
 * aparece uma vez por SKU vendido.
 *
 * Duas regras valem pra TODO o objeto e explicam quase todo erro de apuração
 * aqui: dinheiro é STRING dentro de `Money`, e taxa é INT em centésimo de
 * ponto percentual (3000 = 30,00%). Além disso, cada valor existe em duas
 * versões — `estimated_*` (na criação do pedido, com devolvidos dentro) e
 * `actual_*` (depois de devolução/reembolso). Só `actual_*` fecha repasse.
 */
final class CapAffiliateSkuOrder implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Id do pedido PRINCIPAL da transação. */
        public readonly ?string $id = null,
        /** Rótulo livre que o parceiro grava no link pra atribuir performance. */
        public readonly ?string $tags = null,
        /** Epoch em SEGUNDOS, UTC+0. */
        public readonly ?int $createTime = null,
        public readonly ?int $deliveryTime = null,
        /** CUSTOMER UNPAID | PENDING | SETTLED | … — só SETTLED gera comissão paga. */
        public readonly ?string $settleStatus = null,
        public readonly ?string $skuId = null,
        public readonly ?string $openCollaborationId = null,
        public readonly ?string $targetCollaborationId = null,
        public readonly ?string $creatorUsername = null,
        public readonly ?string $productName = null,
        public readonly ?string $productId = null,
        /** Preço unitário do SKU. */
        public readonly ?Money $price = null,
        public readonly ?int $quantity = null,
        public readonly ?string $shopName = null,
        /** SHOP | VIDEO | LIVE | PRE_LIVE | PROMOTION_P… — por onde a venda entrou. */
        public readonly ?string $contentType = null,
        public readonly ?string $contentId = null,
        /** Direct (clicou no link do criador) ou Indirect — a taxa do Direct é maior. */
        public readonly ?string $attributionType = null,
        /** Fixed commission | Tiered commission. */
        public readonly ?string $commissionModel = null,
        /** Só faz sentido no modelo Tiered: as faixas que o vendedor configurou, em texto ("3.0 OR 5.0"). */
        public readonly ?string $commissionTierSetting = null,
        /** Centésimos de %: 3000 = 30,00%. */
        public readonly ?int $standardCommissionRate = null,
        public readonly ?int $commissionBonusRate = null,
        public readonly ?int $shopAdsCommissionRate = null,
        public readonly ?int $tapBonusCommissionRate = null,
        /** Divisão criador × agência. */
        public readonly ?int $revenueSharingPortionRate = null,
        /** Base ESTIMADA: preço × quantidade na criação do pedido, sem descontar devolução. */
        public readonly ?Money $estimatedCommissionBase = null,
        public readonly ?Money $estimatedStandardCommission = null,
        public readonly ?Money $estimatedBonusCommission = null,
        public readonly ?Money $estimatedShopAdsCommission = null,
        public readonly ?Money $estimatedTapBonusCommission = null,
        /** Retenção só de criador no México. */
        public readonly ?Money $estimatedCreatorTaxIsr = null,
        public readonly ?Money $estimatedCreatorTaxIva = null,
        /** Retenção de criador em VN/TH/PH/ID. */
        public readonly ?Money $estimatedCreatorTaxPit = null,
        public readonly ?Money $estimatedCreatorTotalEarningsAfterTax = null,
        public readonly ?Money $estimatedTotalAgencyEarnings = null,
        public readonly ?Money $estimatedAgencyIsr = null,
        public readonly ?Money $estimatedAgencyIva = null,
        /** Base REAL: já exclui devolvido/reembolsado. É esta que fecha o repasse. */
        public readonly ?Money $actualCommissionBase = null,
        public readonly ?Money $actualStandardCommission = null,
        public readonly ?Money $actualBonusCommission = null,
        public readonly ?Money $actualShopAdsCommission = null,
        public readonly ?Money $actualTapBonusCommission = null,
        public readonly ?Money $actualCreatorTaxIsr = null,
        public readonly ?Money $actualCreatorTaxIva = null,
        public readonly ?Money $actualCreatorTaxPit = null,
        public readonly ?Money $actualCreatorTotalEarningsAfterTax = null,
        /** Repare no nome: aqui é `commission`, mas o par estimado chama `earnings`. */
        public readonly ?Money $actualTotalAgencyCommission = null,
        public readonly ?Money $actualAgencyIsr = null,
        public readonly ?Money $actualAgencyIva = null,
        /** STRING "Yes"/"No", não bool. "Yes" = devolução total, comissão zero. */
        public readonly ?string $fullyReturn = null,
        /** Epoch em SEGUNDOS, UTC+0. */
        public readonly ?int $settlementTime = null,
        public readonly ?int $lastUpdateTime = null,
    ) {}
}

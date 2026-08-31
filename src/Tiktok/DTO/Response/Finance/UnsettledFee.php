<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `fee_tax_breakdown.fee` — as TAXAS (sem imposto) estimadas da transacao nao
 * liquidada. Junto com UnsettledTax somam o `estFeeTaxAmount`.
 *
 * Frete NAO entra aqui: custo de envio vive em `shipping_cost_breakdown`.
 *
 * Vem quase tudo NEGATIVO (e' cobranca); o que e' devolucao pro vendedor vem
 * positivo (ex.: `pitWithheldFromAdsCommissionAmount`). Nao aplicar abs().
 *
 * A maioria destes campos e' de mercado especifico (US, SEA, ID, VN, MX) e no
 * BR vem "0" — mas o DTO cobre todos: campo fora do DTO some no toArray().
 *
 * DINHEIRO E STRING (padrao TikTok).
 */
final class UnsettledFee implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $platformCommissionAmount = null,
        // referral/refund_administration: so' EUA.
        public readonly ?string $referralFeeAmount = null,
        public readonly ?string $refundAdministrationFeeAmount = null,
        public readonly ?string $transactionFeeAmount = null,
        public readonly ?string $creditCardHandlingFeeAmount = null,
        public readonly ?string $affiliateCommissionAmount = null,
        // PIT = imposto de renda retido do criador (mercados SEA).
        public readonly ?string $affiliateCommissionBeforePitAmount = null,
        public readonly ?string $pitWithheldFromAdsCommissionAmount = null,
        public readonly ?string $affiliatePartnerCommissionAmount = null,
        public readonly ?string $affiliateAdsCommissionAmount = null,
        // SFP = Seller Free Shipping Programme.
        public readonly ?string $sfpServiceFeeAmount = null,
        public readonly ?string $liveSpecialsFeeAmount = null,
        public readonly ?string $bonusCashbackServiceFeeAmount = null,
        public readonly ?string $mallServiceFeeAmount = null,
        // Retail delivery fee: taxa do estado do Colorado (EUA).
        public readonly ?string $retailDeliveryFeeAmount = null,
        public readonly ?string $retailDeliveryFeePaymentAmount = null,
        public readonly ?string $retailDeliveryFeeRefundAmount = null,
        public readonly ?string $platformSpecialServiceFeeAmount = null,
        public readonly ?string $smartPromotionFeeAmount = null,
        // Sem sufixo `_amount` na API (VN/ID) — manter a chave crua.
        public readonly ?string $vnFixInfrastructureFee = null,
        public readonly ?string $dynamicCommissionAmount = null,
        // CFP = Co-funded Promotion; SP = Smart Promotion. Cada uma com a
        // taxa e o imposto sobre a taxa em campos separados.
        public readonly ?string $campaignPeriodFeeCfpAmount = null,
        public readonly ?string $campaignPeriodFeeSpAmount = null,
        public readonly ?string $campaignPeriodFeeSpTaxAmount = null,
        public readonly ?string $campaignPeriodFeeCfpTaxAmount = null,
        public readonly ?string $sellerGrowthFeeAmount = null,
        public readonly ?string $failedDeliveryShippingFee = null,
        public readonly ?string $buyerFaultReturnShippingFee = null,
        public readonly ?string $insuranceFee = null,
        // Imposto sobre a taxa de anuncio GMV Max.
        public readonly ?string $cpsShopAdsCommissionTaxAmount = null,
        public readonly ?string $shippingInsuranceFeeTaxAmount = null,
    ) {}
}

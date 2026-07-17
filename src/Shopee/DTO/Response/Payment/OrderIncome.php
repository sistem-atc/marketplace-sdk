<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Payment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Repasse do pedido (`order_income`) — o que a Shopee efetivamente paga ao
 * vendedor e todas as taxas/descontos que compoem esse numero.
 *
 * `escrowAmount` e' o VALOR LIQUIDO do repasse (o que cai na conta). Os demais
 * ~85 campos sao as parcelas que o formam.
 *
 * TIPAGEM: todo campo de dinheiro e' `?float` mesmo quando a Shopee manda
 * inteiro. Motivo: nas 25 amostras reais, 72 campos numericos aparecem SO como
 * int — mas 56 deles sao sempre 0, e um campo hoje zerado pode vir decimal em
 * outro pedido. Tipado int, o cast truncaria 1.5 -> 1 em silencio: perda de
 * dinheiro invisivel. float nunca trunca.
 *
 * Campos zerados no BR (import_tax, th_import_duty, withholding_*, gst, vat...)
 * sao de outros mercados da Shopee; ficam mapeados pra manter o roundtrip
 * lossless.
 *
 * @property list<EscrowItem>|null $items
 * @property list<NetFeeInfo>|null $netCommissionFeeInfoList
 * @property list<NetFeeInfo>|null $netServiceFeeInfoList
 * @property list<OrderAdjustment>|null $orderAdjustment
 * @property list<TenureInfo>|null $tenureInfoList
 * @property list<string>|null $sellerVoucherCode
 */
final class OrderIncome implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?float $actualInstallationFee = null,
        public readonly ?float $actualShippingFee = null,
        public readonly ?float $adsEscrowTopUpFeeOrTechnicalSupportFee = null,
        public readonly ?float $bcrsDeposit = null,
        public readonly ?float $buyerPaidExtendedWarranty = null,
        public readonly ?float $buyerPaidShippingFee = null,
        public readonly ?string $buyerPaymentMethod = null,
        public readonly ?float $buyerTotalAmount = null,
        public readonly ?float $buyerTransactionFee = null,
        public readonly ?float $campaignFee = null,
        public readonly ?float $coins = null,
        public readonly ?float $commissionFee = null,
        public readonly ?float $costOfGoodsSold = null,
        public readonly ?float $creditCardPromotion = null,
        public readonly ?float $creditCardTransactionFee = null,
        public readonly ?float $crossBorderTax = null,
        public readonly ?float $deliverySellerProtectionFeePremiumAmount = null,
        public readonly ?float $drcAdjustableRefund = null,
        public readonly ?float $escrowAmount = null,
        public readonly ?float $escrowAmountAfterAdjustment = null,
        public readonly ?float $escrowImportTax = null,
        public readonly ?float $escrowTax = null,
        public readonly ?float $estimatedShippingFee = null,
        public readonly ?float $fbsFee = null,
        public readonly ?float $finalEscrowProductGst = null,
        public readonly ?float $finalEscrowShippingGst = null,
        public readonly ?float $finalProductProtection = null,
        public readonly ?float $finalProductVatTax = null,
        public readonly ?float $finalReturnToSellerShippingFee = null,
        public readonly ?float $finalShippingFee = null,
        public readonly ?float $finalShippingVatTax = null,
        public readonly ?float $fsfSellerProtectionFeeClaimAmount = null,
        public readonly ?float $installationFeePaidByBuyer = null,
        public readonly ?string $instalmentPlan = null,
        public readonly ?float $netCommissionFee = null,
        public readonly ?float $netServiceFee = null,
        public readonly ?float $orderAmsCommissionFee = null,
        public readonly ?float $orderChargeableWeight = null,
        public readonly ?float $orderDiscountedPrice = null,
        public readonly ?float $orderOriginalPrice = null,
        public readonly ?float $orderSellerDiscount = null,
        public readonly ?float $orderSellingPrice = null,
        public readonly ?float $originalCostOfGoodsSold = null,
        public readonly ?float $originalPrice = null,
        public readonly ?float $originalShopeeDiscount = null,
        public readonly ?float $overseasReturnServiceFee = null,
        public readonly ?float $paymentPromotion = null,
        public readonly ?float $pixDiscount = null,
        public readonly ?float $proratedCoinsValueOffsetReturnItems = null,
        public readonly ?float $proratedPaymentChannelPromoBankOffsetReturnItems = null,
        public readonly ?float $proratedPaymentChannelPromoShopeeOffsetReturnItems = null,
        public readonly ?float $proratedPixDiscountOffsetReturnItems = null,
        public readonly ?float $proratedSellerVoucherOffsetReturnItems = null,
        public readonly ?float $proratedShopeeVoucherOffsetReturnItems = null,
        public readonly ?float $remainingVoucher = null,
        public readonly ?float $returnToSellerShippingFeeSst = null,
        public readonly ?float $reverseShippingFee = null,
        public readonly ?float $reverseShippingFeeSst = null,
        public readonly ?float $rsfSellerProtectionFeeClaimAmount = null,
        public readonly ?float $salesTaxOnLvg = null,
        public readonly ?float $sellerCoinCashBack = null,
        public readonly ?float $sellerDiscount = null,
        public readonly ?float $sellerLostCompensation = null,
        public readonly ?float $sellerOrderProcessingFee = null,
        public readonly ?float $sellerReturnRefund = null,
        public readonly ?float $sellerShippingDiscount = null,
        public readonly ?float $sellerTransactionFee = null,
        public readonly ?float $serviceFee = null,
        public readonly ?float $shippingFeeDiscountFrom3pl = null,
        public readonly ?float $shippingFeeSst = null,
        public readonly ?float $shippingSellerProtectionFeeAmount = null,
        public readonly ?float $shopeeDiscount = null,
        public readonly ?float $shopeeShippingRebate = null,
        public readonly ?float $thImportDuty = null,
        public readonly ?float $totalAdjustmentAmount = null,
        public readonly ?float $tradeInBonusBySeller = null,
        public readonly ?float $vatOnImportedGoods = null,
        public readonly ?float $voucherFromSeller = null,
        public readonly ?float $voucherFromShopee = null,
        public readonly ?float $withholdingCitTax = null,
        public readonly ?float $withholdingPitTax = null,
        public readonly ?float $withholdingTax = null,
        public readonly ?float $withholdingVatTax = null,
        public readonly ?SellerProductRebate $sellerProductRebate = null,
        // Lista de STRINGS (codigos), nao de objetos.
        public readonly ?array $sellerVoucherCode = null,
        #[ArrayOf(EscrowItem::class)]
        public readonly ?array $items = null,
        #[ArrayOf(NetFeeInfo::class)]
        public readonly ?array $netCommissionFeeInfoList = null,
        #[ArrayOf(NetFeeInfo::class)]
        public readonly ?array $netServiceFeeInfoList = null,
        #[ArrayOf(OrderAdjustment::class)]
        public readonly ?array $orderAdjustment = null,
        #[ArrayOf(TenureInfo::class)]
        public readonly ?array $tenureInfoList = null,
    ) {}
}

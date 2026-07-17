<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Payment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * O que o COMPRADOR pagou (`buyer_payment_info`) — nao confundir com
 * OrderIncome, que e' o que o VENDEDOR recebe. A diferenca entre os dois sao
 * as taxas da Shopee.
 *
 * Mesma regra de tipagem do OrderIncome: dinheiro e' sempre `?float` (int
 * truncaria centavos em silencio).
 */
final class BuyerPaymentInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?float $adsVoucherDiscount = null,
        public readonly ?float $bcrsDeposit = null,
        public readonly ?float $bulkyHandlingFee = null,
        public readonly ?float $buyerPaidExtendedWarranty = null,
        public readonly ?float $buyerPaidInstallationFee = null,
        public readonly ?string $buyerPaymentMethod = null,
        public readonly ?float $buyerServiceFee = null,
        public readonly ?float $buyerTaxAmount = null,
        public readonly ?float $buyerTotalAmount = null,
        public readonly ?float $creditCardPromotion = null,
        public readonly ?float $discountPix = null,
        public readonly ?float $footwearTax = null,
        public readonly ?float $icmsTaxAmount = null,
        public readonly ?float $importDutyAndExciseTax = null,
        public readonly ?float $importProcessingCharge = null,
        public readonly ?float $importTaxAmount = null,
        public readonly ?float $initialBuyerTxnFee = null,
        public readonly ?float $insurancePremium = null,
        public readonly ?float $iofTaxAmount = null,
        public readonly ?bool $isPaidByCreditCard = null,
        public readonly ?float $lvgSalesTaxAdjustment = null,
        public readonly ?float $merchantSubtotal = null,
        public readonly ?float $sellerVoucher = null,
        public readonly ?float $shippingFee = null,
        public readonly ?float $shippingFeeSstAmount = null,
        public readonly ?float $shopeeCoinsRedeemed = null,
        public readonly ?float $shopeeVoucher = null,
        public readonly ?float $shopeevipSubtotal = null,
        public readonly ?float $totalTaxAndFeesAmount = null,
        public readonly ?float $tradeInBonus = null,
        public readonly ?float $tradeInDiscount = null,
        public readonly ?float $vat = null,
    ) {}
}

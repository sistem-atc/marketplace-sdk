<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Decomposicao de preco de UM item (`data.line_items[]` do price_detail).
 *
 * Mesmos campos do nivel raiz, por item. Dinheiro e' STRING, como em todo o
 * TikTok — tipar float comeria a casa decimal.
 */
final class PriceDetailLineItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $currency = null,
        public readonly ?string $total = null,
        public readonly ?string $payment = null,
        public readonly ?string $skuListPrice = null,
        public readonly ?string $skuSalePrice = null,
        public readonly ?string $subtotal = null,
        public readonly ?string $subtotalDeductionSeller = null,
        public readonly ?string $subtotalDeductionPlatform = null,
        public readonly ?string $subtotalTaxAmount = null,
        public readonly ?string $voucherDeductionPlatform = null,
        public readonly ?string $voucherDeductionSeller = null,
        public readonly ?string $shippingListPrice = null,
        public readonly ?string $shippingSalePrice = null,
        public readonly ?string $shippingFeeDeductionSeller = null,
        public readonly ?string $shippingFeeDeductionPlatform = null,
        public readonly ?string $shippingFeeDeductionPlatformVoucher = null,
        public readonly ?string $taxAmount = null,
        public readonly ?string $taxRate = null,
        public readonly ?string $netPriceAmount = null,
        public readonly ?string $codFee = null,
        /** No item o campo e' `cod_fee_amount`; na raiz e' `cod_fee_net_amount`. */
        public readonly ?string $codFeeAmount = null,
        public readonly ?string $skuGiftOriginalPrice = null,
        public readonly ?string $skuGiftNetPrice = null,
        public readonly ?string $distanceShippingFee = null,
        public readonly ?string $distanceFee = null,
    ) {}
}

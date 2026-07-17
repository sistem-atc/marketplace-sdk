<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Totais do pedido (`payment`).
 *
 * DINHEIRO É STRING no TikTok ("45.90", "0.00") — diferente de ML/Shopee, que
 * mandam número. Mantemos `?string` de propósito: tipar float quebraria o
 * roundtrip lossless ("45.90" -> 45.9 -> "45.9", e "0.00" -> "0") e a casa
 * decimal se perderia na re-serialização do raw. Converta no consumidor:
 * `(float) $payment->totalAmount`.
 */
final class OrderPayment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $currency = null,
        public readonly ?string $totalAmount = null,
        public readonly ?string $subTotal = null,
        public readonly ?string $originalTotalProductPrice = null,
        public readonly ?string $sellerDiscount = null,
        public readonly ?string $platformDiscount = null,
        // Raro (3 em 2000 pedidos reais) — desconto de plataforma no pagamento.
        public readonly ?string $paymentPlatformDiscount = null,
        public readonly ?string $shippingFee = null,
        public readonly ?string $originalShippingFee = null,
        public readonly ?string $shippingFeeSellerDiscount = null,
        public readonly ?string $shippingFeePlatformDiscount = null,
        public readonly ?string $shippingFeeCofundedDiscount = null,
        public readonly ?string $handlingFee = null,
        public readonly ?string $paymentDiscountServiceFee = null,
        public readonly ?string $retailDeliveryFee = null,
        public readonly ?string $tax = null,
    ) {}
}

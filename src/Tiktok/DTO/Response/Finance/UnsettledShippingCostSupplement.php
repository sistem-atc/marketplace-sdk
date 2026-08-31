<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `shipping_cost_breakdown.supplementary_component` — detalhamento INFORMATIVO
 * do frete.
 *
 * ARMADILHA: estes valores NAO entram na soma do `estShippingCostAmount`; eles
 * ja' estao DENTRO de `actualShippingFeeAmount` ou de
 * `shippingFeeDiscountAmount`. Somar junto com o breakdown de cima duplica o
 * frete. Servem pra explicar de onde veio o numero, nao pra recompor.
 *
 * DINHEIRO E STRING (padrao TikTok).
 */
final class UnsettledShippingCostSupplement implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $customerShippingFeeOffsetAmount = null,
        public readonly ?string $shippingFeeSubsidyAmount = null,
        public readonly ?string $platformShippingFeeDiscountAmount = null,
        // Parte de actual_shipping_fee_amount: FBM = frete do TikTok Shipping;
        // FBT = pedido fulfillado pelo TikTok (fora dos EUA).
        public readonly ?string $fbmShippingCostAmount = null,
        public readonly ?string $fbtShippingCostAmount = null,
        public readonly ?string $promoShippingIncentiveAmount = null,
        public readonly ?string $fbtFulfillmentFeeAmount = null,
        public readonly ?string $sellerShippingFeeDiscountAmount = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * SKU de um pedido rastreado por sharing link — item de `orders[].skus[]` do
 * /affiliate_creator/202505/orders/trace/search.
 *
 * Recorte MENOR que o AffiliateOrderSku (sem os `standard*`, sem impostos),
 * mas com `deliveryTime` POR SKU e o `trace` do link que gerou a venda.
 *
 * Taxas em centesimos de por cento; valores STRING (ver MoneyAmount).
 */
final class AffiliateTraceOrderSku implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $productId = null,
        public readonly ?MoneyAmount $price = null,
        public readonly ?int $quantity = null,
        public readonly ?int $commissionRate = null,
        public readonly ?MoneyAmount $estimatedCommissionBase = null,
        public readonly ?MoneyAmount $actualCommission = null,
        public readonly ?int $shopAdsCommissionRate = null,
        public readonly ?int $commissionBonusRate = null,
        public readonly ?string $productName = null,
        public readonly ?string $shopName = null,
        public readonly ?int $returnedQuantity = null,
        public readonly ?int $refundedQuantity = null,
        public readonly ?string $campaignId = null,
        public readonly ?MoneyAmount $actualCommissionBase = null,
        public readonly ?MoneyAmount $actualShopAdsCommission = null,
        public readonly ?MoneyAmount $estimatedShopAdsCommission = null,
        public readonly ?MoneyAmount $estimatedBonusCommission = null,
        public readonly ?int $deliveryTime = null,
        public readonly ?int $creatorCommissionRewardRate = null,
        public readonly ?MoneyAmount $estimatedCreatorCommissionRewardFee = null,
        public readonly ?MoneyAmount $actualCreatorCommissionRewardFee = null,
        public readonly ?string $contentType = null,
        public readonly ?string $contentId = null,
        // Aqui a chave e `trace` (no /orders/search e `trace_info`).
        public readonly ?TraceInfo $trace = null,
        public readonly ?MoneyAmount $estimatedCommission = null,
        public readonly ?MoneyAmount $actualBonusCommission = null,
    ) {}
}

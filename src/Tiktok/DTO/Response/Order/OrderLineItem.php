<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Linha do pedido (`line_items[]`).
 *
 * ATENÇÃO À QUANTIDADE: o TikTok NÃO manda quantidade — cada line_item é UMA
 * unidade. 2 unidades do mesmo SKU = 2 entradas na lista, cada uma com seu
 * `id` próprio. Agrupar por `skuId` pra obter quantidade.
 *
 * `sellerSku` é o de/para com o catálogo interno.
 *
 * Dinheiro é STRING (ver OrderPayment).
 */
final class OrderLineItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $productId = null,
        public readonly ?string $productName = null,
        public readonly ?string $skuId = null,
        public readonly ?string $skuName = null,
        public readonly ?string $skuImage = null,
        public readonly ?string $skuType = null,
        public readonly ?string $sellerSku = null,
        public readonly ?string $currency = null,
        // Dinheiro: STRING (ver OrderPayment).
        public readonly ?string $originalPrice = null,
        public readonly ?string $salePrice = null,
        public readonly ?string $sellerDiscount = null,
        public readonly ?string $platformDiscount = null,
        public readonly ?string $retailDeliveryFee = null,
        public readonly ?string $shippingVatAmount = null,
        public readonly ?string $shippingVatRate = null,
        public readonly ?string $giftRetailPrice = null,
        public readonly ?bool $isGift = null,
        public readonly ?string $displayStatus = null,
        public readonly ?string $packageId = null,
        public readonly ?string $packageStatus = null,
        public readonly ?string $shippingProviderId = null,
        public readonly ?string $shippingProviderName = null,
        public readonly ?string $trackingNumber = null,
        public readonly ?int $rtsTime = null,
        public readonly ?string $cancelReason = null,
        public readonly ?string $cancelUser = null,
    ) {}
}

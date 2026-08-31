<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Sub-item de uma linha cancelada (SKUs de um "virtual bundle").
 * Mesma logica do ReturnSubLineItem, do lado do cancelamento.
 */
final class CancelSubLineItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $cancelSubLineItemId = null,
        public readonly ?string $cancelLineItemId = null,
        public readonly ?string $subOrderLineItemId = null,
        public readonly ?string $orderLineItemId = null,
        public readonly ?string $skuId = null,
        public readonly ?string $skuName = null,
        public readonly ?ProductImage $productImage = null,
        public readonly ?string $productName = null,
        public readonly ?string $sellerSku = null,
        public readonly ?RefundAmount $refundAmount = null,
    ) {}
}

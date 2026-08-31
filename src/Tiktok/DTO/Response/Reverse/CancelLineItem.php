<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Linha do pedido que esta' sendo cancelada. Como no pedido, cada linha e' UMA
 * unidade — nao existe quantidade.
 *
 * @property list<CancelSubLineItem>|null $cancelSubLineItems
 */
final class CancelLineItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $cancelLineItemId = null,
        public readonly ?string $orderLineItemId = null,
        public readonly ?string $skuId = null,
        public readonly ?string $skuName = null,
        public readonly ?ProductImage $productImage = null,
        public readonly ?string $productName = null,
        public readonly ?string $sellerSku = null,
        public readonly ?RefundAmount $refundAmount = null,
        #[ArrayOf(CancelSubLineItem::class)]
        public readonly ?array $cancelSubLineItems = null,
    ) {}
}

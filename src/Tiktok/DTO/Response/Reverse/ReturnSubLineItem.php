<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Sub-item de uma linha de devolucao — os SKUs que compoem um "virtual bundle".
 *
 * Bundle no TikTok nao e' kit cadastrado: e' uma linha do pedido que se
 * desdobra em varios SKUs reais. Quem baixa estoque tem que olhar AQUI, nao no
 * `returnLineItems`, senao devolve 1 unidade de um SKU que nao existe.
 */
final class ReturnSubLineItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $returnSubLineItemId = null,
        public readonly ?string $returnLineItemId = null,
        public readonly ?string $subOrderLineItemId = null,
        public readonly ?string $orderLineItemId = null,
        public readonly ?string $skuId = null,
        public readonly ?string $skuName = null,
        public readonly ?ProductImage $productImage = null,
        public readonly ?string $productName = null,
        /** SKU definido por nos (seller_sku) — a chave do de/para com o produto. */
        public readonly ?string $sellerSku = null,
        public readonly ?RefundAmount $refundAmount = null,
    ) {}
}

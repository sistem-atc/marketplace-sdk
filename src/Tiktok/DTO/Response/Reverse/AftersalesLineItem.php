<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de um aftersales request / RMA — a versao MAGRA da linha (so' ids).
 *
 * Nao confundir com `ReturnLineItem`: aqui nao ha' preco nem nome de produto.
 * Serve pra amarrar o pedido de devolucao ao pedido de venda original via
 * `mainOrderId`, que e' o `id` do pedido no /order.
 */
final class AftersalesLineItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $returnLineItemId = null,
        /** Preenchido quando o item pertence a um "virtual bundle". */
        public readonly ?string $subReturnLineItemId = null,
        public readonly ?string $skuId = null,
        /** Pedido de VENDA (forward) que originou a devolucao. */
        public readonly ?string $mainOrderId = null,
        public readonly ?string $skuReturnRequestId = null,
    ) {}
}

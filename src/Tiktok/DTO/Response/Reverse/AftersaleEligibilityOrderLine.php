<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Linha de pedido dentro de `order_line_list[]`.
 *
 * Existe porque `order_line_items_ids` (lista de string, legada) nao consegue
 * enderecar item DENTRO de kit: no bundle, a linha de pedido e' a mesma e o
 * que distingue os componentes e' o `sub_order_line_item_id`. Para produto
 * simples esse sub-id nao vem.
 */
final class AftersaleEligibilityOrderLine implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderLineItemId = null,
        public readonly ?string $subOrderLineItemId = null,
    ) {}
}

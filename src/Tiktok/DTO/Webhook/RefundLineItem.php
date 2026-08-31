<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `line_items` do webhook type 67 — a linha exata que foi reembolsada.
 *
 * Cada entrada e' UMA unidade devolvida: o exemplo oficial repete o mesmo
 * `return_line_item_id` com `sku_id` diferente e nao traz quantidade em lugar
 * nenhum. Contar linhas, nao somar quantidade.
 */
final class RefundLineItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Pedido de VENDA original (o "forward order"). */
        public readonly ?string $mainOrderId = null,
        public readonly ?string $returnLineItemId = null,
        public readonly ?string $skuId = null,
        public readonly ?string $skuReturnRequestId = null,
        /** So' aparece em devolucao de Virtual Bundle (kit); opcional. */
        public readonly ?string $subReturnLineItemId = null,
    ) {}
}

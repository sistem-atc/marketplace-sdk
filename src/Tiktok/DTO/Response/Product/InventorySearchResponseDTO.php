<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /product/202309/inventory/search`.
 *
 * E' a UNICA rota que devolve o estoque COMPROMETIDO (reservado por pedido em
 * aberto) e o rateio por campanha/criador — o Get Product so' devolve o
 * disponivel por armazem. Sem paginacao: a resposta traz exatamente os
 * product_ids/sku_ids pedidos (max 100 produtos / 600 SKUs por chamada).
 */
final class InventorySearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(InventoryProduct::class)]
        public readonly ?array $inventory = null,
    ) {}
}

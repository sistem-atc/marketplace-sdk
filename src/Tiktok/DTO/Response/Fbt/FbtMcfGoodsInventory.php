<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Saldo disponivel de um goods pra atendimento multicanal.
 *
 * `availableQty` e' AGREGADO (soma dos armazens) e abreviado — a API de
 * inventario normal usa `available_quantity` por armazem. Nao sao o mesmo campo
 * nem batem 1:1: use este so' pra decidir se aceita o pedido do canal externo.
 */
final class FbtMcfGoodsInventory implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $goodsId = null,
        public readonly ?int $availableQty = null,
    ) {}
}

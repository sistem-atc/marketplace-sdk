<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Uma movimentacao de estoque no armazem FBT (`data.inventory_records[]`).
 *
 * E' o livro-razao do estoque em poder de terceiro: `changedQuantity` =
 * final - inicial, entao NEGATIVO e' saida. Cada linha aqui deveria ter
 * contrapartida no ERP — inbound casa com a nota de remessa, CONSIGN_ORDER com
 * a nota de venda, e ajuste/descarte NAO tem nota nenhuma (e' justamente o que
 * precisa virar acerto de inventario).
 *
 * `createTime` e' epoch em SEGUNDOS.
 */
final class FbtInventoryRecord implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?FbtInventoryGoods $goods = null,
        public readonly ?string $fbtWarehouseId = null,
        public readonly ?FbtInventoryRecordOrder $order = null,
        public readonly ?int $createTime = null,
        // NORMAL | DEFECTIVE — avariado nao volta pro saldo vendavel.
        public readonly ?string $inventoryGoodsType = null,
        public readonly ?int $initialOnHandQuantity = null,
        public readonly ?int $finalOnHandQuantity = null,
        public readonly ?int $changedQuantity = null,
    ) {}
}

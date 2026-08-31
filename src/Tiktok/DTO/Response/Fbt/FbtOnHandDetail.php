<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Estoque FISICO no armazem FBT (nao inclui o que esta' em transito).
 *
 * ARITMETICA QUE MUDA POR NIVEL:
 *   - no nivel do GOODS: total = available + reserved + unfulfillable;
 *   - no nivel do SKU: total = available + reserved (sem `unfulfillable`, que
 *     nao existe la' — por isso o campo e' nullable).
 * `available` NAO inclui o reservado: pra "quanto ainda posso vender" use
 * `available`, nunca `total`.
 */
final class FbtOnHandDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $totalQuantity = null,
        public readonly ?int $availableQuantity = null,
        // Reservado por pedido de cliente ou por devolucao ao fornecedor.
        public readonly ?int $reservedQuantity = null,
        // Avariado/vencido/bloqueado — so' existe no nivel do goods.
        public readonly ?int $unfulfillableQuantity = null,
    ) {}
}

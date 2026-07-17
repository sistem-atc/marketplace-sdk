<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Inventory;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Detalhe do estoque indisponível (`not_available_detail[]`) — o motivo pelo qual
 * a unidade não está à venda: transfer, damage, lost, noFiscalCoverage,
 * withdrawal, internal_process.
 */
final class InventoryStockDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $status = null,
        public readonly ?int $quantity = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Ordem que ORIGINOU a movimentacao de estoque.
 *
 * `type` e' o que decide o tratamento fiscal no ERP:
 *   INBOUND_ORDER / CUSTOMER_RETURN / FAILED_DELIVERY -> entrada no armazem;
 *   RETURN_TO_SUPPLIER_ORDER / DISPOSAL -> saida;
 *   INVENTORY_*_ADJUSTMENT_ORDER / INVENTORY_COUNT_ORDER -> ajuste de
 *   inventario sem contrapartida comercial (perda/sobra a justificar);
 *   CONSIGN_ORDER -> saida por venda; INVENTORY_TRANSFER_ORDER -> transferencia
 *   entre armazens FBT.
 * Demais valores possiveis estao na doc do endpoint.
 */
final class FbtInventoryRecordOrder implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $type = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Transicao de status da ordem de inbound (epoch em SEGUNDOS).
 *
 * NAO EXISTE campo de status "atual" na ordem: o status corrente e' o
 * `newStatus` do log mais recente. Ordenar por `operateTime` e' obrigatorio.
 *
 * Valores: WORKING, READY_TO_SHIP, TO_BE_RECEIVED, ARRIVED_HUB,
 * INTERNAL_TRANSFER, RECEIVING, PARTIALLY_RECEIVED, RECEIVED, CANCELLED,
 * DELIVERED.
 */
final class FbtInboundStatusLog implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $operateTime = null,
        public readonly ?string $newStatus = null,
        public readonly ?string $previousStatus = null,
        // Quem operou — pode ser um nome de sistema ("Warehouse"), nao um id.
        public readonly ?string $operator = null,
    ) {}
}

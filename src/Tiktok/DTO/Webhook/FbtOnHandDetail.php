<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `on_hand_detail` do webhook type 24: o que esta' FISICAMENTE no armazem —
 * exclui em transito e avariado.
 *
 * total = reserved + available (reservado = ja' vendido, aguardando expedicao).
 * Pro calculo de ruptura vale `available`, nao `total`.
 */
final class FbtOnHandDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $totalQuantity = null,
        public readonly ?int $reservedQuantity = null,
        public readonly ?int $availableQuantity = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Envelope `data.mcf_order` — compartilhado por TRES endpoints: create
 * (`POST /fbt/202607/mcf_outbound_orders`), status
 * (`GET /fbt/202601/mcf_outbound_orders`) e cancel
 * (`POST /fbt/202601/mcf_outbound_orders/cancel`). A shape e' a mesma; o que
 * muda e' quanto vem preenchido.
 */
final class FbtMcfOrderResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?FbtMcfOrder $mcfOrder = null,
    ) {}
}

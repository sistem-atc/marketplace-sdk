<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /fbt/202603/ship_inbound_order`.
 *
 * O "ship" aqui e' a declaracao de que a carga SAIU do nosso CD — e' o gatilho
 * natural pra emitir a nota de remessa e baixar o estoque proprio pra
 * "em transito".
 */
final class FbtShipInboundOrderResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $inboundOrderId = null,
    ) {}
}

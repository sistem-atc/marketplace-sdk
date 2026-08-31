<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /fbt/202603/confirm_inbound_method`.
 *
 * UM plano confirmado vira N ordens de inbound (uma por destino, quando o
 * metodo e' D2FC). Sao esses ids que alimentam ship/cancel/tracking/label — e,
 * no ERP, cada um deles corresponde a uma nota de remessa distinta.
 */
final class FbtConfirmInboundMethodResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** @var list<string>|null ids NUMERICOS (sem prefixo IBR) */
        public readonly ?array $inboundOrderIds = null,
    ) {}
}

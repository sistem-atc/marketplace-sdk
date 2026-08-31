<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /fbt/202602/inbound_orders/label_print`.
 *
 * ASSINCRONO: com `taskStatus=PROCESSING` o `downloadUrl` ainda nao presta —
 * repita ate' `SUCCESS`.
 *
 * A URL vem UNICODE-ESCAPADA pelo TikTok (o "e comercial" chega como
 * \u0026). O json_decode do PHP ja' desfaz isso; o cuidado e' nao repassar a
 * string crua do payload sem decodificar.
 */
final class FbtInboundLabelResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $downloadUrl = null,
        // PROCESSING | SUCCESS
        public readonly ?string $taskStatus = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do Calculate Refund (`/return_refund/202602/refunds/calculate`).
 *
 * SIMULACAO: diz quanto sairia do nosso bolso ANTES de aprovar a devolucao —
 * hoje o sistema so' descobre isso quando o valor aparece no extrato
 * financeiro. O valor depende do `request_type` enviado (CANCEL, REFUND e
 * RETURN_AND_REFUND usam politicas de calculo DIFERENTES pro MESMO pedido), e
 * por isso nao pode ser cacheado por pedido.
 */
final class CalculateRefundResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?RefundAmount $orderRefundAmount = null,
    ) {}
}

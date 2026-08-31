<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Detalhe do erro: identifica QUAL entrada do lote falhou.
 *
 * Sem ele o `errors[]` seria inutil num lote de 100 — a resposta nao repete a
 * ordem nem o indice do request, so' este par (pedido do TikTok + referencia
 * que tentamos gravar).
 *
 * O `externalOrder` daqui vem SEM `line_items`: o erro e' de nivel de pedido.
 */
final class ExternalOrderReferenceErrorDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Id do pedido no TIKTOK que a entrada tentou referenciar. */
        public readonly ?string $orderId = null,
        public readonly ?ExternalOrder $externalOrder = null,
    ) {}
}

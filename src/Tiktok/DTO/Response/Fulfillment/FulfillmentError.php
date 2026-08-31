<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Erro PARCIAL de operacao em lote.
 *
 * ARMADILHA: o envelope volta com `code: 0` (sucesso HTTP/negocio) mesmo
 * quando itens falharam — o BaseMethods NAO lanca excecao nesse caso. Quem
 * chama TEM de inspecionar `errors` pra saber o que realmente passou.
 */
final class FulfillmentError implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $code = null,
        public readonly ?string $message = null,
        public readonly ?FulfillmentErrorDetail $detail = null,
    ) {}
}

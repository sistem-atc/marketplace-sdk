<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * NFe referenciada por outra (`reference_invoices[]` da resposta de invoice).
 *
 * Em ML Full a nota de VENDA referencia a de RETORNO simbolico: aqui vem o id
 * ML da referenciada (usado no `getById`) + a chave SEFAZ.
 */
final class ReferenceInvoice implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $invoiceKey = null,
    ) {}
}

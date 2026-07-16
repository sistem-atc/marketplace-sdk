<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Totalizador fiscal da nota (`fiscal_data.fiscal_amounts[]`). O `attributes`
 * varia por imposto, entao fica como array cru (mesma decisao do TaxRule).
 *
 * @property array<string, mixed>|null $attributes
 */
final class FiscalAmount implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<string, mixed>|null $attributes */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?array $attributes = null,
    ) {}
}

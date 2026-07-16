<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Regra de imposto do item (`items[].fiscal_data.rules[]`). O `attributes` tem
 * shape TOTALMENTE diferente por imposto (PIS/ICMS/IBS/CBS/COFINS/IPI/IBPT/...),
 * por isso fica como array cru — nao vale tipar cada um.
 *
 * @property array<string, mixed>|null $attributes
 */
final class TaxRule implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<string, mixed>|null $attributes */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?array $attributes = null,
    ) {}
}

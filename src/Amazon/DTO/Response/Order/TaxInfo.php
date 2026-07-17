<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Info fiscal (`MarketplaceTaxInfo`). Para BR, `TaxClassifications` traz o
 * CPF/CNPJ do comprador (Name=CPF, Value=número).
 *
 * @property list<TaxClassification>|null $taxClassifications
 */
final class TaxInfo implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(TaxClassification::class)]
        public readonly ?array $taxClassifications = null,
    ) {}
}

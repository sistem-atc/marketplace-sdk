<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Atributos fiscais do item (`items[].fiscal_data.attributes`). O `cfop` daqui
 * e' essencial pra classificar a operacao (ex.: 6106 = venda interestadual).
 */
final class ItemFiscalAttributes implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $ncm = null,
        public readonly ?string $cest = null,
        public readonly ?int $taxRuleId = null,
        public readonly ?string $originType = null,
        public readonly ?string $originDetail = null,
        public readonly ?string $cfop = null,
        public readonly ?string $measurementUnit = null,
        public readonly ?string $fci = null,
        public readonly ?string $extipi = null,
        public readonly ?string $csosn = null,
        public readonly ?string $reductionBcComposition = null,
    ) {}
}

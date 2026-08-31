<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Regra "exigido em quais mercados" — shape identico para `responsible_person`
 * e `manufacturer` na Get Global Category Rules, por isso uma classe so'.
 *
 * Ambos valem SO' para a UE (exigencia do GPSR). Objeto ausente = a categoria
 * nao exige.
 */
final class GlobalRegionRequirementRule implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** true = exigido em TODOS os mercados da UE. */
        public readonly ?bool $isRequired = null,
        /** @var list<string>|null */
        public readonly ?array $optionalRegions = null,
        /** @var list<string>|null */
        public readonly ?array $requiredRegions = null,
    ) {}
}

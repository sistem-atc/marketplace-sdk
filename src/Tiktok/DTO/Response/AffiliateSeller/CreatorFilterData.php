<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Miolo de `data.data` — filtros e ordenacoes disponiveis.
 */
final class CreatorFilterData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(CreatorFilterOptionGroup::class)]
        public readonly ?array $optionList = null,
        #[ArrayOf(CreatorFilterSorter::class)]
        public readonly ?array $sorters = null,
    ) {}
}

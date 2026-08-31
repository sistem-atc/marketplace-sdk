<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Algoritmo de ordenacao aceito pela busca de creators. O `algorithmId` vai em
 * `advanced_filters.sorter_algorithm_id` no request da busca.
 */
final class CreatorFilterSorter implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $algorithmId = null,
        public readonly ?string $name = null,
    ) {}
}

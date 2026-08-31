<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Requisitos que o produto precisa cumprir pra atender a oportunidade. */
final class OpportunityListingCriteria implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productBrand = null,
        public readonly ?string $productCategory = null,
        public readonly ?string $productStatus = null,
        public readonly ?string $keywords = null,
    ) {}
}

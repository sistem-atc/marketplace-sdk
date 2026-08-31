<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Estado do produto na vitrine do creator.
 *
 * `inventoryStatus`: IN_STOCK | SOLD_OUT.
 * `reviewStatus`: APPROVED | CHANGES_UNDER_REVIEW | UNAVAILABLE | ZERO_COMMISSION.
 * `addedStatus`: NOT_ADDED | ADDED | REJECTED.
 */
final class ShowcaseProductStatus implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $inventoryStatus = null,
        public readonly ?string $reviewStatus = null,
        public readonly ?bool $isHidden = null,
        public readonly ?string $addedStatus = null,
    ) {}
}

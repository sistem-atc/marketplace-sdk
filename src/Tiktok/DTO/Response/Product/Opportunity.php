<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Oportunidade de catalogo (lead de produto/palavra/categoria) em detalhe. */
final class Opportunity implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $actionType = null,
        public readonly ?OpportunityListingCriteria $listingCriteria = null,
        public readonly ?OpportunityContentData $contentData = null,
        public readonly ?OpportunityMarketData $marketData = null,
        public readonly ?OpportunityBasicInfo $basicInfo = null,
    ) {}
}

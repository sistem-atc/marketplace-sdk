<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preço do SKU numa região.
 *
 * `list_price` é o preço "de" auditado (MSRP) e `sale_price` o "por" com
 * imposto — o `sale_price` só é preenchido pra vendedor cross-border chinês.
 */
final class SkuRegionPrice implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $regionCode = null,
        public readonly ?string $currency = null,
        public readonly ?string $listPrice = null,
        public readonly ?string $salePrice = null,
        public readonly ?string $localizedDutiablePrice = null,
    ) {}
}

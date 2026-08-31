<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Codigo de barras do goods. `type` pode ser UPC, EAN, GTIN, "ASIN OR FNSKU",
 * "MERCHANT SKU ID" ou "FBT SPECIFIC BARCODE" — note que alguns valores tem
 * ESPACO, nao sao enum snake_case; nao normalize.
 */
final class FbtGoodsBarcode implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $code = null,
        public readonly ?string $type = null,
    ) {}
}

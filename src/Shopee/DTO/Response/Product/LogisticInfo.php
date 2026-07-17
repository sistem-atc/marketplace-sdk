<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Canal logístico habilitado no anúncio (`logistic_info[]`). */
final class LogisticInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $logisticId = null,
        public readonly ?string $logisticName = null,
        public readonly ?bool $enabled = null,
        public readonly ?float $shippingFee = null,
        public readonly ?int $sizeId = null,
        public readonly ?bool $isFree = null,
        public readonly ?float $estimatedShippingFee = null,
    ) {}
}

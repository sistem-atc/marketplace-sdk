<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Faixa de preco do produto: min/max entre todos os SKUs.
 *
 * Dinheiro STRING (ver MoneyAmount).
 */
final class PriceRange implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $currency = null,
        public readonly ?string $minimumAmount = null,
        public readonly ?string $maximumAmount = null,
    ) {}
}

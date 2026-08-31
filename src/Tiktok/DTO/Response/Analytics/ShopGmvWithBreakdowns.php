<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * GMV total da loja no intervalo + fatias por tipo de conteudo.
 */
final class ShopGmvWithBreakdowns implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?MonetaryValue $overall = null,
        #[ArrayOf(ShopGmvBreakdown::class)]
        public readonly ?array $breakdowns = null,
    ) {}
}

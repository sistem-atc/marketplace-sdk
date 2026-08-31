<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Grupo de `top_contents[]`: um `type` (LIVE | VIDEO) e ate' 100 conteudos
 * ordenados por GMV.
 */
final class ShopProductTopContentGroup implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $type = null,
        #[ArrayOf(ShopProductTopContent::class)]
        public readonly ?array $contents = null,
    ) {}
}

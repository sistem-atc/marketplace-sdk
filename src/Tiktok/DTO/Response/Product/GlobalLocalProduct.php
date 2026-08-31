<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto LOCAL associado ao global (`products[]`) — por publicacao global
 * ou por vinculo manual. Um por mercado.
 *
 * @property list<GlobalSkuMapping>|null $skuMappings
 */
final class GlobalLocalProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $region = null,
        public readonly ?string $id = null,
        #[ArrayOf(GlobalSkuMapping::class)]
        public readonly ?array $skuMappings = null,
    ) {}
}

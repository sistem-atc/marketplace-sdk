<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Grupo de opcoes por tipo de filtro (idioma, nivel, categoria pro, marca).
 */
final class CreatorFilterOptionGroup implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // OPTION_VERTICAL_PRO | OPTION_LANGUAGE | OPTION_CREATOR_LEVEL | OPTION_SORTER | OPTION_BRAND
        public readonly ?string $optionType = null,
        #[ArrayOf(CreatorFilterOptionItem::class)]
        public readonly ?array $options = null,
    ) {}
}

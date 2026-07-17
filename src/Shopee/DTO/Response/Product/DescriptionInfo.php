<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Descrição estendida (`description_info`) — usada quando
 * `descriptionType` = 'extended'. Com type 'normal' a descrição vem no
 * campo `description` do item, e este bloco não vem.
 */
final class DescriptionInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?ExtendedDescription $extendedDescription = null,
    ) {}
}

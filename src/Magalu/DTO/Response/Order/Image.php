<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Imagem do item (`items[].info.images[]`). */
final class Image implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $url = null,
    ) {}
}

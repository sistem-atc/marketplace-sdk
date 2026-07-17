<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Item;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Foto do anúncio (`pictures[]`).
 */
final class ItemPicture implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $url = null,
        public readonly ?string $secureUrl = null,
        public readonly ?string $size = null,
        public readonly ?string $maxSize = null,
        public readonly ?string $quality = null,
    ) {}
}

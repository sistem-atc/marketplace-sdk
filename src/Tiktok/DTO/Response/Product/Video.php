<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Vídeo do produto (`video`). `size` em bytes. */
final class Video implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $url = null,
        public readonly ?string $coverUrl = null,
        public readonly ?string $format = null,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
        public readonly ?int $size = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Certificação do produto (`certifications[]`) — ex.: registro sanitário,
 * laudo. `images[]` são as fotos do documento.
 *
 * @property list<Image>|null $images
 */
final class Certification implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        #[ArrayOf(Image::class)]
        public readonly ?array $images = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Imagem (`main_images[]` e `certifications[].images[]` — mesma shape).
 * `uri` é o id interno; `urls`/`thumbUrls` são listas de URLs em resoluções.
 *
 * @property list<string>|null $urls
 * @property list<string>|null $thumbUrls
 */
final class Image implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $uri = null,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
        public readonly ?array $urls = null,
        public readonly ?array $thumbUrls = null,
    ) {}
}

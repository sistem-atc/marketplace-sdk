<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Certificacao exigida pela categoria (`certifications[]`).
 * Pode vir com arquivos (PDF/video) E imagens — sao listas separadas.
 *
 * @property list<GlobalCertificationFile>|null $files
 * @property list<GlobalImage>|null $images
 */
final class GlobalCertification implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        #[ArrayOf(GlobalCertificationFile::class)]
        public readonly ?array $files = null,
        #[ArrayOf(GlobalImage::class)]
        public readonly ?array $images = null,
    ) {}
}

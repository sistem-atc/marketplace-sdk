<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Thumbnail do produto: URI base + as URLs já renderizadas pelo CDN. */
final class ProductThumbnail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $uri = null,
        /** Lista de URLs (strings) — mesma imagem em CDNs diferentes. */
        public readonly ?array $urlList = null,
    ) {}
}

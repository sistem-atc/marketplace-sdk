<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Imagem do catalogo GLOBAL. Mesma shape em `main_images[]`,
 * `certifications[].images[]`, `skus[].sales_attributes[].sku_img` e
 * `size_chart.image` — por isso um DTO so'.
 *
 * `uri` e' o identificador interno (o que se manda de volta ao criar/editar
 * produto); `urls`/`thumbUrls` sao URLs de exibicao e nem sempre vem.
 *
 * @property list<string>|null $urls
 * @property list<string>|null $thumbUrls
 */
final class GlobalImage implements DTOInterface
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

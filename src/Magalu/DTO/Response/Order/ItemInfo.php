<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Ficha do produto na linha (`items[].info`). `sku` é o de/para com o catálogo.
 * `attributes`/`extras` têm shape livre (passthrough).
 *
 * @property list<Image>|null $images
 * @property array<int, mixed>|null $attributes
 * @property array<int, mixed>|null $extras
 */
final class ItemInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $sku = null,
        public readonly ?string $name = null,
        public readonly ?string $brand = null,
        public readonly ?string $description = null,
        #[ArrayOf(Image::class)]
        public readonly ?array $images = null,
        public readonly ?array $attributes = null,
        public readonly ?array $extras = null,
    ) {}
}

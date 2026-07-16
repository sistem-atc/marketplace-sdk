<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Atributos de catalogo do item (`items[].attributes`).
 */
final class ItemAttributes implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $ean = null,
        public readonly ?string $sku = null,
        public readonly ?string $type = null,
        public readonly ?int $bundleQuantity = null,
    ) {}
}

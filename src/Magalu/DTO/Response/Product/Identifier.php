<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Identificador do produto (`identifiers[]`) — `type` = GTIN/EAN/etc. É o dado
 * fiscal (EAN) que hoje o app não lê mas a API entrega.
 */
final class Identifier implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $value = null,
    ) {}
}

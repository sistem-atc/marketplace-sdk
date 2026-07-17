<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Valor monetário Amazon (`OrderTotal`, `ItemPrice`, `ItemTax`, etc).
 *
 * `Amount` é STRING no padrão SP-API ("159.90") — nunca number. Converta com
 * `(float)` no consumidor.
 */
final class Money implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $amount = null,
        public readonly ?string $currencyCode = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Par valor+moeda que TODA metrica monetaria da Analytics devolve (gmv, gpm,
 * avg_price, refunds, tax, ...). E' o mesmo shape em ~40 pontos da API, entao e'
 * UM DTO reusado — nao duplique por metrica.
 *
 * `amount` e' STRING de proposito ("39440.00", "0"): tipar float comeria a casa
 * decimal e quebraria o roundtrip lossless. Converta no consumidor.
 */
final class MonetaryValue implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $amount = null,
        public readonly ?string $currency = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Item;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Bloco de preço da sugestão (`{amount, usd_amount}`) — o ML repete essa shape
 * em current/suggested/lowest/internal/external_price.
 */
final class SuggestionPrice implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?float $amount = null,
        public readonly ?float $usdAmount = null,
    ) {}
}

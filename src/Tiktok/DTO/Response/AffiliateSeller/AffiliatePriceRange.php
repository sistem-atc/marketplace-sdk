<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Faixa de preco/GMV em dinheiro: min/max como STRING + faixa ja formatada.
 *
 * `formattedRange` ("$0-$100") so' vem quando o mercado alvo e' US e o creator
 * nao autorizou compartilhar numero exato.
 */
final class AffiliatePriceRange implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $currency = null,
        public readonly ?string $minimumAmount = null,
        public readonly ?string $maximumAmount = null,
        public readonly ?string $formattedRange = null,
    ) {}
}

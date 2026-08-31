<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Valor monetario do programa de afiliados: par {amount, currency}.
 *
 * `amount` e' STRING e vem JA FORMATADO na moeda local ("Rp9.900", "121.32").
 * Nao da' pra somar sem normalizar — o separador muda por regiao (BR usa
 * virgula decimal, ID usa ponto de milhar). Tipar float perderia isso.
 */
final class MoneyAmount implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $amount = null,
        public readonly ?string $currency = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Comissao do produto na colaboracao.
 *
 * `rate` e' INTEIRO em centesimos de por cento: 3000 = 30,00%; 3587 = 35,87%.
 * Divida por 10000 pra virar fracao. Faixa valida [100, 8000].
 */
final class ProductCommission implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $rate = null,
        public readonly ?string $currency = null,
        public readonly ?string $amount = null,
    ) {}
}

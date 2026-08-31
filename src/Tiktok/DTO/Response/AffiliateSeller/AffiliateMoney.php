<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Valor monetario do afiliado: `{amount, currency}`.
 *
 * O TikTok manda o AMOUNT como STRING ("1232.90"). Tipar float comeria a
 * casa decimal e quebraria o roundtrip — converta so' no consumidor.
 *
 * `symbol` so' aparece no trend do creator ("$"), onde o TikTok troca
 * `currency` por simbolo.
 */
final class AffiliateMoney implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $amount = null,
        public readonly ?string $currency = null,
        // so no creator_trend_profile: "$" no lugar do codigo ISO
        public readonly ?string $symbol = null,
    ) {}
}

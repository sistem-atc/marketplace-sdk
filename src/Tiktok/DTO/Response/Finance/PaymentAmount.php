<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Par valor+moeda usado pelos tres montantes do pagamento (`amount`,
 * `settlement_amount`, `payment_amount_before_exchange`).
 *
 * O TikTok embrulha o dinheiro num objeto aqui (diferente do statement, onde o
 * montante vem solto na raiz) porque cada um pode estar numa MOEDA DIFERENTE:
 * loja cross-border liquida em BRL e paga em USD, com `exchange_rate` no meio.
 * Ler so o `value` sem a `currency` mistura moedas em silencio.
 *
 * DINHEIRO E STRING (padrao TikTok — tipar float comeria a casa decimal).
 */
final class PaymentAmount implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $value = null,
        // ISO 4217 ("BRL", "USD").
        public readonly ?string $currency = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Um PAGAMENTO (`data.payments[]` de GET /finance/{v}/payments) — a
 * transferencia efetiva pra conta bancaria do vendedor.
 *
 * E o ELO QUE FALTAVA do repasse: o `Statement` diz quanto o TikTok apurou;
 * o `Payment` diz quanto SAIU pro banco, quando saiu e pra qual conta. Um
 * pagamento pode agregar varios statements, e o valor difere do apurado
 * quando ha conversao de moeda (`exchange_rate`).
 *
 * Encadeamento dos montantes (todos objetos valor+moeda, ver PaymentAmount):
 *   settlementAmount             -> apurado, ANTES do cambio
 *   paymentAmountBeforeExchange  -> a pagar, ainda na moeda de origem
 *   amount                       -> o que efetivamente caiu, APOS o cambio
 *
 * DINHEIRO E STRING; `exchangeRate` tambem — vem com SEIS casas decimais
 * ("1.000000") e virar float perderia o rateio na reconciliacao.
 */
final class Payment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        /**
         * PAID | FAILED | PROCESSING. Só `PAID` significa dinheiro na conta —
         * `PROCESSING` ainda pode virar `FAILED` (conta bancária incorreta).
         */
        public readonly ?string $status = null,
        public readonly ?PaymentAmount $amount = null,
        public readonly ?PaymentAmount $settlementAmount = null,
        public readonly ?PaymentAmount $paymentAmountBeforeExchange = null,
        public readonly ?string $exchangeRate = null,
        // epoch em SEGUNDOS: `createTime` = pagamento iniciado no TikTok;
        // `paidTime` = liquidado de fato. So' vem preenchido quando PAID.
        public readonly ?int $createTime = null,
        public readonly ?int $paidTime = null,
        // Mascarado pelo TikTok ("***********1234") — so' serve pra conferir
        // qual conta recebeu, nunca pra cadastrar/validar conta.
        public readonly ?string $bankAccount = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Uma movimentacao do SALDO da loja (`data.withdrawals[]` de
 * GET /finance/{v}/withdrawals). Apesar do nome do endpoint, a lista NAO e' so'
 * de saque: o `type` diz o que e', e o filtro `types` e' OBRIGATORIO na query.
 *
 *   WITHDRAW  saque do saldo pra conta bancaria (saida)
 *   SETTLE    a plataforma liquida o apurado no saldo (entrada)
 *   TRANSFER  subsidio ou deducao por politica da plataforma
 *   REVERSE   estorno do saque que falhou (conta bancaria errada)
 *
 * Somar tudo sem olhar o `type` conta o mesmo dinheiro duas vezes: o SETTLE
 * credita o saldo e o WITHDRAW so' o transfere pro banco.
 *
 * DINHEIRO E STRING (padrao TikTok). Aqui o montante vem SOLTO com a moeda ao
 * lado — nao embrulhado como no Payment.
 */
final class Withdrawal implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $type = null,
        public readonly ?string $amount = null,
        // ISO 4217.
        public readonly ?string $currency = null,
        /**
         * PROCESSING | SUCCESS | FAILED. Atencao ao vocabulario: aqui o
         * sucesso e' `SUCCESS`, enquanto no Payment e' `PAID`.
         */
        public readonly ?string $status = null,
        // epoch em SEGUNDOS.
        public readonly ?int $createTime = null,
    ) {}
}

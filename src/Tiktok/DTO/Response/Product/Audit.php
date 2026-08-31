<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Auditoria/moderação do produto (`audit`). `status` = estado da revisão
 * TikTok; `preApprovedReasons` justifica pré-aprovações.
 *
 * `notSubmittedReasons` (changelog "Get Product API v202309: New
 * not_submitted_reasons Response Field Added") diz por que o produto NÃO foi
 * sequer submetido à auditoria — pré-requisito da LOJA que não foi cumprido,
 * não problema do produto. Só vem preenchido quando `status === 'NONE'`; nos
 * demais casos a API devolve `[]`.
 *
 * Valores conhecidos: `PAYMENT_ACCOUNT_NOT_LINKED` (sem conta de pagamento
 * vinculada), `SHOP_INACTIVE` (loja inativa), `W8_NOT_CONFIGURED` (dado fiscal
 * W-8 ausente — só lojas US). Fica como list<string> solta de propósito: valor
 * novo do TikTok não pode explodir a hidratação.
 *
 * @property list<mixed>|null $preApprovedReasons
 * @property list<string>|null $notSubmittedReasons
 */
final class Audit implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $status = null,
        public readonly ?array $preApprovedReasons = null,
        public readonly ?array $notSubmittedReasons = null,
    ) {}
}

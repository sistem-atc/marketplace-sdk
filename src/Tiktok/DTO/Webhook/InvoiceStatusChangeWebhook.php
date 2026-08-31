<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do webhook type 36 — Invoice status change. SO' EXISTE NO BRASIL.
 *
 * E' o retorno assincrono do Upload Invoice: depois de mandar a chave/XML da
 * NF-e pro pacote, o TikTok valida contra a SEFAZ e responde AQUI. Sem ele nao
 * se sabe se a nota foi aceita — o POST de upload devolve 200 sem validar.
 *
 * PROCESSING = ainda validando. FAILED/INVALID = a nota NAO esta' anexada ao
 * pacote e a expedicao trava; `invalid_reason` diz se e' problema nosso
 * (CNPJ_NOT_MATCH, NOT_AUTHORIZED, ACCESS_KEY_DUPLICATE, UF_Inconsistent_*) ou
 * so' atraso da SEFAZ (NOT_FOUND, UNKNOWN — reenviar depois).
 *
 * A nota e' por PACOTE, nao por pedido: `order_ids` pode trazer varios pedidos
 * combinados sob a mesma NF-e.
 */
final class InvoiceStatusChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<string>|null $orderIds */
    public function __construct(
        public readonly ?string $packageId = null,
        /** Lista de string crua (ids de pedido), nao objetos. */
        public readonly ?array $orderIds = null,
        /** SUCCESS | PROCESSING | FAILED | INVALID. */
        public readonly ?string $invoiceStatus = null,
        /**
         * So' quando invoice_status = INVALID:
         * UNKNOWN | FAILED | NOT_FOUND | NOT_AUTHORIZED | ACCESS_KEY_DUPLICATE |
         * CNPJ_NOT_MATCH | UF_Inconsistent_Required |
         * UF_Inconsistent_Dismisse_Retirada_missing |
         * UF_Inconsistent_Dismissed_Retirada_Inconsistent.
         *
         * Repare a caixa MISTA nos UF_* — nao normalize, compare literal.
         */
        public readonly ?string $invalidReason = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do webhook type 67 — Refund Success.
 *
 * UNICO webhook do grupo que carrega DINHEIRO: `refund_total` + moeda + o
 * instante em que o reembolso concluiu. E' o sinal pra baixar/abater o
 * recebivel do pedido em `line_items[].main_order_id` — reembolso parcial
 * abate, total zera. Os outros topicos de pos-venda (11, 12, 64, 65) sao
 * mudanca de estado e NAO devem mexer no financeiro.
 *
 * Cuidado: o pedido que perde valor e' o `main_order_id` de cada line item, nao
 * um order_id de topo — o payload nao tem esse campo.
 *
 * @property list<RefundLineItem>|null $lineItems
 */
final class RefundSuccessWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $aftersalesRequestId = null,
        #[ArrayOf(RefundLineItem::class)]
        public readonly ?array $lineItems = null,
        /** ISO-4217 (BRL no Brasil; o exemplo oficial mostra USD). */
        public readonly ?string $refundCurrency = null,
        /** REFUND_SUCCESS no exemplo oficial. */
        public readonly ?string $refundStatus = null,
        /** Epoch em SEGUNDOS da conclusao do reembolso. */
        public readonly ?int $refundTimestamp = null,
        /**
         * Valor total reembolsado. STRING de proposito — float come a casa
         * decimal e quebra o roundtrip ("1.25" nao pode virar 1.25).
         */
        public readonly ?string $refundTotal = null,
        public readonly ?string $rmaId = null,
    ) {}
}

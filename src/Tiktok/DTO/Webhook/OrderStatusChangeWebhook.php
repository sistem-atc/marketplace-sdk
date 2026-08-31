<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do webhook type 1 — Order status change.
 *
 * E' o gatilho fiscal principal do canal: AWAITING_SHIPMENT e' o momento em que
 * o pedido esta' pago e pronto pra emissao; CANCEL manda invalidar/cancelar o
 * que ja' foi emitido. O payload NAO traz valores — so' o id e o status; o
 * financeiro sai do Get Order Detail / das transacoes.
 */
final class OrderStatusChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderId = null,
        /**
         * UNPAID | ON_HOLD | AWAITING_SHIPMENT | AWAITING_COLLECTION | CANCEL |
         * IN_TRANSIT | DELIVERED | COMPLETED.
         *
         * Tipado como string de proposito: valor novo do TikTok nao pode
         * explodir a hidratacao do webhook.
         */
        public readonly ?string $orderStatus = null,
        /** Se o pedido passou (ou vai passar) por ON_HOLD — retencao antifraude. */
        public readonly ?bool $isOnHoldOrder = null,
        /** Epoch em SEGUNDOS da mudanca de status. */
        public readonly ?int $updateTime = null,
    ) {}
}

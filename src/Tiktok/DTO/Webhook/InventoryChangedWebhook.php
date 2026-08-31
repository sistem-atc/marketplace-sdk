<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Webhook TYPE 68 — Inventory changed. Feed de saldo de estoque por SKU, em
 * tempo real, para QUALQUER causa: venda, cancelamento, envio, ajuste manual,
 * escrita via API, trava/destrava de campanha e de creator.
 *
 * ⚠️ ESTE DTO MODELA O PAYLOAD INTEIRO, nao o miolo de `data`. O exemplo
 * oficial do topico 68 e' PLANO: nao tem `type`, nem `tts_notification_id`, nem
 * `shop_id`, nem `data`. Hidratar com WebhookEnvelope aqui devolveria tudo
 * nulo. A identidade da loja vem como `seller_id`, e a dedup como `event_id`.
 *
 * E' o topico de MAIOR VOLUME do TikTok Shop (uma mensagem por movimento de
 * saldo, e um unico pedido movimenta varias vezes: created -> shipped).
 *
 * Nao confundir com o TYPE 27 (Inventory status change), que e' ALERTA de
 * ruptura — dispara so' em LOW_STOCK/OUT_OF_STOCK ou previsao de ruptura, com
 * vocabulario proprio de saldo. Os dois NAO sao redundantes: o 68 e' o feed de
 * movimento, o 27 e' o gatilho de reposicao.
 */
final class InventoryChangedWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<InventoryChangeDetail>|null $changeDetail */
    public function __construct(
        // UUID da mensagem — idempotencia no nivel do EVENTO.
        public readonly ?string $eventId = null,
        // ISO 8601 UTC com nanossegundos, nao epoch. Ver InventoryChangeDetail.
        public readonly ?string $occurredAt = null,
        // Doc tipa int64; snowflake -> string, senao perde precisao.
        public readonly ?string $sellerId = null,
        public readonly ?string $productId = null,
        public readonly ?string $skuId = null,
        public readonly ?InventoryQuantitySnapshot $quantitySnapshotAfterChange = null,
        // Lista: "na maioria dos casos" tem um item so', mas pode vir mais de um.
        #[ArrayOf(InventoryChangeDetail::class)]
        public readonly ?array $changeDetail = null,
    ) {}
}

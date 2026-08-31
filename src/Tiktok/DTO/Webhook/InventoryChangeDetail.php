<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `change_detail[]` do webhook TYPE 68 — a operacao que causou a
 * mudanca de saldo.
 *
 * Todo `*Delta` e' SINALIZADO: positivo aumenta, negativo diminui. Nunca some
 * delta com o snapshot sem olhar o sinal.
 *
 * `idempotencyKey` e' a chave de dedup NO NIVEL DA OPERACAO — o `event_id` do
 * payload deduplica a MENSAGEM. Sao coisas distintas (no exemplo oficial vem
 * iguais, o que engana): reentrega repete o event_id, e uma mesma operacao pode
 * aparecer em mensagens diferentes.
 */
final class InventoryChangeDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $idempotencyKey = null,
        // order_created | order_canceled | order_shipped | manual_adjustment |
        // api_sync | campaign_lock | campaign_unlock | creator_lock |
        // creator_unlock | system_auto_replenish (minusculo, ao contrario do
        // resto da API que usa MAIUSCULA).
        public readonly ?string $triggerSource = null,
        // ISO 8601 UTC com nanossegundos ("2026-04-02T09:28:34.979101552Z") —
        // NAO e' epoch como no resto dos webhooks. Mantido string pra nao
        // perder precisao nem o formato no roundtrip. Serve pra ordenar o
        // processamento das mudancas.
        public readonly ?string $occurredAt = null,
        public readonly ?int $totalQuantityDelta = null,
        public readonly ?int $availableQuantityDelta = null,
        public readonly ?int $committedQuantityDelta = null,
        public readonly ?int $inShopQuantityDelta = null,
        public readonly ?int $campaignLockedQuantityDelta = null,
        public readonly ?int $creatorLockedQuantityDelta = null,
    ) {}
}

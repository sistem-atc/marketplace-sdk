<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tests\Support\RecursiveFieldCoverage;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\InventoryChangedWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\InventoryStatusChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\WebhookEnvelope;

/**
 * Os DOIS webhooks de estoque do TikTok Shop — que sao coisas diferentes e
 * vivem sendo confundidos:
 *
 *  - TYPE 27 (Inventory status change): ALERTA. So' dispara em LOW_STOCK /
 *    OUT_OF_STOCK ou quando o TikTok PREVE ruptura em X dias. Baixo volume.
 *  - TYPE 68 (Inventory changed): FEED de movimento de saldo, uma mensagem por
 *    mudanca (venda, cancelamento, envio, ajuste manual, campanha, creator).
 *    Altissimo volume, e o payload NAO usa o envelope padrao.
 */
function ttInventoryWebhookFixture(string $slug): array
{
    return json_decode(file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'), true);
}

it('type 27: nao descarta NENHUM campo do exemplo OFICIAL da doc', function () {
    $data = ttInventoryWebhookFixture('webhook-27-inventory-status-change')['data'];

    $perdidos = RecursiveFieldCoverage::missing($data, InventoryStatusChangeWebhook::fromArray($data)->toArray());

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
});

it('type 68: nao descarta NENHUM campo do exemplo OFICIAL da doc', function () {
    // Aqui o payload INTEIRO e' o dado — nao ha' `data`.
    $payload = ttInventoryWebhookFixture('webhook-68-inventory-changed');

    $perdidos = RecursiveFieldCoverage::missing($payload, InventoryChangedWebhook::fromArray($payload)->toArray());

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
});

it('roundtrip e lossless nos dois topicos de estoque', function () {
    $data27 = ttInventoryWebhookFixture('webhook-27-inventory-status-change')['data'];
    $um = InventoryStatusChangeWebhook::fromArray($data27)->toArray();
    expect(InventoryStatusChangeWebhook::fromArray($um)->toArray())->toBe($um);

    $data68 = ttInventoryWebhookFixture('webhook-68-inventory-changed');
    $dois = InventoryChangedWebhook::fromArray($data68)->toArray();
    expect(InventoryChangedWebhook::fromArray($dois)->toArray())->toBe($dois);
});

it('type 68 e PLANO: o envelope padrao nao le nada dele', function () {
    // Armadilha real: quem roteia por `type` antes de hidratar simplesmente
    // nao acha o topico 68 — ele nao manda type, tts_notification_id, shop_id
    // nem data. A dedup e' por event_id e a loja vem em seller_id.
    $payload = ttInventoryWebhookFixture('webhook-68-inventory-changed');
    $envelope = WebhookEnvelope::fromArray($payload);

    expect($envelope->type)->toBeNull()
        ->and($envelope->ttsNotificationId)->toBeNull()
        ->and($envelope->shopId)->toBeNull()
        ->and($envelope->data)->toBeNull();

    $dto = InventoryChangedWebhook::fromArray($payload);
    expect($dto->eventId)->toBe('d7813cae-9997-4d24-a583-7d85801250f1')
        ->and($dto->sellerId)->toBeString()->toBe('7498123456789012345');
});

it('type 68: occurred_at e ISO 8601 com nanossegundos, nao epoch', function () {
    // Todo o resto dos webhooks do TikTok data em epoch de segundos. Este nao —
    // e' string ISO com 9 casas de nanossegundo, que nem DateTime do PHP
    // reproduz sem perda. Fica string.
    $dto = InventoryChangedWebhook::fromArray(ttInventoryWebhookFixture('webhook-68-inventory-changed'));

    expect($dto->occurredAt)->toBe('2026-04-02T09:28:34.979101552Z')
        ->and($dto->changeDetail[0]->occurredAt)->toBe('2026-04-02T09:28:34.979101552Z');
});

it('type 68: os deltas sao sinalizados e batem com o snapshot', function () {
    $dto = InventoryChangedWebhook::fromArray(ttInventoryWebhookFixture('webhook-68-inventory-changed'));

    $snap = $dto->quantitySnapshotAfterChange;
    $delta = $dto->changeDetail[0];

    // Identidades da doc, verificadas no exemplo oficial (ajuste manual de +4).
    expect($snap->totalQuantity)->toBe($snap->totalAvailableQuantity + $snap->totalCommittedQuantity)
        ->and($snap->inShopQuantity)->toBe(
            $snap->totalAvailableQuantity - ($snap->campaignLockedQuantity + $snap->creatorLockedQuantity)
        )
        ->and($delta->totalQuantityDelta)->toBe(4)
        ->and($delta->triggerSource)->toBe('manual_adjustment');
});

it('type 68: change_detail e LISTA — nao le so o primeiro item', function () {
    // "Na maioria dos casos" vem um item so'; assumir isso perde movimento.
    $dto = InventoryChangedWebhook::fromArray([
        'event_id' => 'e1', 'sku_id' => '1',
        'change_detail' => [
            ['idempotency_key' => 'a', 'trigger_source' => 'order_created', 'committed_quantity_delta' => 2],
            ['idempotency_key' => 'b', 'trigger_source' => 'campaign_lock', 'campaign_locked_quantity_delta' => 3],
        ],
    ]);

    expect($dto->changeDetail)->toHaveCount(2)
        ->and($dto->changeDetail[1]->triggerSource)->toBe('campaign_lock');
});

it('type 27 e ALERTA: trigger_reason diz PREDICTION ou REALTIME, com campos exclusivos', function () {
    $dto = InventoryStatusChangeWebhook::fromArray(
        ttInventoryWebhookFixture('webhook-27-inventory-status-change')['data']
    );

    // No exemplo oficial (PREDICTION) o piso de estoque nem vem — e' null, nao 0.
    expect($dto->triggerReason->alertType)->toBe('PREDICTION')
        ->and($dto->triggerReason->leadDays)->toBe(21)
        ->and($dto->triggerReason->lowStockThreshold)->toBeNull()
        ->and($dto->currentInventoryStatus)->toBe('LOW_STOCK');

    $realtime = InventoryStatusChangeWebhook::fromArray([
        'product_id' => '1', 'sku_id' => '2',
        'trigger_reason' => ['alert_type' => 'REALTIME', 'low_stock_threshold' => 5],
    ]);

    expect($realtime->triggerReason->leadDays)->toBeNull()
        ->and($realtime->triggerReason->lowStockThreshold)->toBe(5);
});

it('type 27 e 68 NAO sao redundantes: vocabulario de saldo diferente', function () {
    // O 27 reparte em available + creator_reserved + campaign_reserved +
    // committed; o 68 em available + committed, com in_shop_quantity ja'
    // liquido das travas. Somar um com o outro sem traduzir da' numero errado.
    $d27 = InventoryStatusChangeWebhook::fromArray(
        ttInventoryWebhookFixture('webhook-27-inventory-status-change')['data']
    )->inventoryDistribution;

    expect($d27->totalQuantity)->toBe(
        $d27->availableQuantity + $d27->creatorReservedQuantity + $d27->campaignReservedQuantity + $d27->committedQuantity
    );

    $chaves27 = array_keys($d27->toArray());
    $chaves68 = array_keys(
        InventoryChangedWebhook::fromArray(ttInventoryWebhookFixture('webhook-68-inventory-changed'))
            ->quantitySnapshotAfterChange->toArray()
    );

    // Unico nome em comum e' `total_quantity` — e nem esse significa o mesmo
    // recorte de travas.
    expect(array_intersect($chaves27, $chaves68))->toBe(['total_quantity']);
});

it('type 27 usa envelope padrao com shop_id, ao contrario do 68', function () {
    $envelope = WebhookEnvelope::fromArray(ttInventoryWebhookFixture('webhook-27-inventory-status-change'));

    expect($envelope->type)->toBe(27)
        ->and($envelope->shopId)->toBe('7494049642642441621')
        ->and($envelope->ttsNotificationId)->toBe('7327112393057371910');
});

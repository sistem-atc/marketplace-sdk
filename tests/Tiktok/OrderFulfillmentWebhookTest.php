<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tests\Support\RecursiveFieldCoverage;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\AftersalesRequestStatusUpdateWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\CancellationStatusChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\FbtInventoryUpdateWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\FbtMcfOrderStatusWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\FbtMerchantOnboardingWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\GoodsMatchWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\InboundFbtOrderStatusChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\InvoiceStatusChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\McfConsignOrder;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\OrderStatusChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\PackageUpdateWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\RecipientAddressUpdateWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\RefundSuccessWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\ReturnStatusChangeWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\RmaStatusUpdateWebhook;
use SistemAtc\Marketplaces\Tiktok\DTO\Webhook\WebhookEnvelope;

/**
 * Webhooks de PEDIDO / FULFILLMENT / LOGISTICA / POS-VENDA / FISCAL do TikTok
 * Shop (types 1, 3, 4, 11, 12, 21, 22, 23, 24, 36, 58, 64, 65, 67).
 *
 * Webhook nao tem chamada: o payload chega e ou o DTO carrega tudo, ou o campo
 * some sem ninguem perceber — e webhook nao se repete (o TikTok reentrega ate'
 * o 2xx e depois esquece). Por isso o coverage recursivo contra o exemplo
 * OFICIAL da doc e' o teste central de cada topico.
 */
function ttOfwFixture(string $slug): array
{
    return json_decode(file_get_contents(__DIR__.'/../Fixtures/Tiktok/webhook-'.$slug.'.json'), true);
}

dataset('webhooks de pedido/fulfillment/pos-venda', [
    'type 1 — order status change' => ['01-order-status-change', OrderStatusChangeWebhook::class],
    'type 3 — recipient address update' => ['03-recipient-address-update', RecipientAddressUpdateWebhook::class],
    'type 4 — package update' => ['04-package-update', PackageUpdateWebhook::class],
    'type 11 — cancellation status change' => ['11-cancellation-status-change', CancellationStatusChangeWebhook::class],
    'type 12 — return status change' => ['12-return-status-change', ReturnStatusChangeWebhook::class],
    'type 21 — inbound FBT order status' => ['21-inbound-fbt-order-status-change', InboundFbtOrderStatusChangeWebhook::class],
    'type 22 — FBT merchant onboarding' => ['22-fbt-merchant-onboarding', FbtMerchantOnboardingWebhook::class],
    'type 23 — goods match' => ['23-goods-match', GoodsMatchWebhook::class],
    'type 24 — FBT inventory update' => ['24-fbt-inventory-update', FbtInventoryUpdateWebhook::class],
    'type 36 — invoice status change' => ['36-invoice-status-change', InvoiceStatusChangeWebhook::class],
    'type 58 — FBT MCF order status' => ['58-fbt-mcf-order-status', FbtMcfOrderStatusWebhook::class],
    'type 64 — aftersales request status' => ['64-aftersales-request-status-update', AftersalesRequestStatusUpdateWebhook::class],
    'type 65 — RMA status update' => ['65-rma-status-update', RmaStatusUpdateWebhook::class],
    'type 67 — refund success' => ['67-refund-success', RefundSuccessWebhook::class],
]);

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function (string $slug, string $dto) {
    $evento = ttOfwFixture($slug);

    $perdidosEnvelope = RecursiveFieldCoverage::missing(
        array_diff_key($evento, ['data' => null]),
        WebhookEnvelope::fromArray($evento)->toArray(),
    );
    expect($perdidosEnvelope)->toBe([], 'envelope perdeu: '.implode(', ', $perdidosEnvelope));

    $perdidosData = RecursiveFieldCoverage::missing(
        $evento['data'],
        $dto::fromArray($evento['data'])->toArray(),
    );
    expect($perdidosData)->toBe([], 'data perdeu: '.implode(', ', $perdidosData));
})->with('webhooks de pedido/fulfillment/pos-venda');

it('faz roundtrip lossless do `data` — o que entra e o que sai sao o MESMO array', function (string $slug, string $dto) {
    $data = ttOfwFixture($slug)['data'];

    expect($dto::fromArray($data)->toArray())->toEqual($data);
})->with('webhooks de pedido/fulfillment/pos-venda');

it('envelope aceita as TRES formas de identificar o vendedor', function () {
    // Pedido/pos-venda/fiscal mandam shop_id...
    $pedido = WebhookEnvelope::fromArray(ttOfwFixture('01-order-status-change'));
    expect($pedido->shopId)->toBe('7494049642642441621')
        ->and($pedido->sellerOpenId)->toBeNull();

    // ...FBT manda seller_open_id e NAO manda shop_id...
    $fbt = WebhookEnvelope::fromArray(ttOfwFixture('24-fbt-inventory-update'));
    expect($fbt->shopId)->toBeNull()
        ->and($fbt->sellerOpenId)->toStartWith('VIyStQ');

    // ...e o type 58 diverge da propria doc: manda creator_open_id.
    $mcf = WebhookEnvelope::fromArray(ttOfwFixture('58-fbt-mcf-order-status'));
    expect($mcf->sellerOpenId)->toBeNull()
        ->and($mcf->creatorOpenId)->toStartWith('VIyStQ');
});

it('envelope deixa `data` CRU pra quem rotear por type decidir o DTO', function () {
    $envelope = WebhookEnvelope::fromArray(ttOfwFixture('36-invoice-status-change'));

    expect($envelope->type)->toBe(36)
        ->and($envelope->data)->toBeArray()
        ->and($envelope->data['invoice_status'])->toBe('INVALID');
});

it('o webhook de endereco (type 3) chega SEM tts_notification_id — dedup nao pode depender dele', function () {
    $evento = ttOfwFixture('03-recipient-address-update');

    expect($evento)->not->toHaveKey('tts_notification_id')
        ->and(WebhookEnvelope::fromArray($evento)->ttsNotificationId)->toBeNull();
});

it('o endereco novo NAO vem no type 3 — so o aviso de que mudou', function () {
    $dto = RecipientAddressUpdateWebhook::fromArray(ttOfwFixture('03-recipient-address-update')['data']);

    // Sem esses campos, corrigir a NF-e exige um Get Order Detail depois.
    expect(array_keys($dto->toArray()))->toBe(['order_id', 'update_time']);
});

it('package update com COMBINE junta varios pedidos num pacote so', function () {
    $dto = PackageUpdateWebhook::fromArray(ttOfwFixture('04-package-update')['data']);

    expect($dto->scType)->toBe('COMBINE')
        ->and($dto->packageList)->toHaveCount(1)
        ->and($dto->packageList[0]->orderIdList)->toBe(['152523', '532123']);
});

it('refund success e o UNICO do grupo com dinheiro — e o valor continua string', function () {
    $dto = RefundSuccessWebhook::fromArray(ttOfwFixture('67-refund-success')['data']);

    expect($dto->refundTotal)->toBe('1.25')
        ->and($dto->refundTotal)->toBeString()
        ->and($dto->refundCurrency)->toBe('USD')
        ->and($dto->refundTimestamp)->toBe(1776102211);
});

it('refund success aponta o pedido a baixar por line item, nao por order_id de topo', function () {
    $data = ttOfwFixture('67-refund-success')['data'];
    $dto = RefundSuccessWebhook::fromArray($data);

    expect($data)->not->toHaveKey('order_id')
        ->and($dto->lineItems)->toHaveCount(3)
        ->and($dto->lineItems[0]->mainOrderId)->toBe('789456123')
        // sub_return_line_item_id so' existe em devolucao de Virtual Bundle
        ->and($dto->lineItems[2]->subReturnLineItemId)->toBeNull();
});

it('RMA aceita as DUAS grafias de timestamp (tabela x exemplo da doc)', function () {
    $exemplo = RmaStatusUpdateWebhook::fromArray(ttOfwFixture('65-rma-status-update')['data']);
    expect($exemplo->rmaCreateTime)->toBe(1627587601)
        ->and($exemplo->rmaRequestCreateTime)->toBeNull();

    // A tabela de parametros da doc usa rma_request_*; nao pode sumir tambem.
    $tabela = RmaStatusUpdateWebhook::fromArray([
        'rma_id' => '1',
        'rma_request_create_time' => 1627587600,
        'rma_request_update_time' => 1644412885,
    ]);
    expect($tabela->rmaRequestCreateTime)->toBe(1627587600)
        ->and($tabela->rmaRequestUpdateTime)->toBe(1644412885);
});

it('MCF (type 58) tipa consign_orders vindo como objeto UNICO ou como lista', function () {
    $dto = FbtMcfOrderStatusWebhook::fromArray(ttOfwFixture('58-fbt-mcf-order-status')['data']);

    // Exemplo oficial: objeto unico (a tabela da doc promete lista).
    $consign = $dto->mcfOrder->typedConsignOrders();
    expect($consign)->toHaveCount(1)
        ->and($consign[0])->toBeInstanceOf(McfConsignOrder::class)
        ->and($consign[0]->trackingNumber)->toBe('TN87654321')
        // `carrier` nao esta' na tabela de parametros, so' no exemplo — mapeado.
        ->and($consign[0]->carrier)->toBe('USPS')
        ->and($consign[0]->typedGoods()[0]->quantity)->toBe(1);

    // Forma da tabela (lista) tem que funcionar igual.
    $lista = FbtMcfOrderStatusWebhook::fromArray([
        'mcf_order' => [
            'mcf_order_id' => '1',
            'consign_orders' => [
                ['id' => 'OBF1', 'status' => 'SHIPPED'],
                ['id' => 'OBF2', 'status' => 'ABNORMAL', 'issue' => 'Lack of inventory'],
            ],
        ],
    ]);
    expect($lista->mcfOrder->typedConsignOrders())->toHaveCount(2)
        ->and($lista->mcfOrder->typedConsignOrders()[1]->issue)->toBe('Lack of inventory');
});

it('saldo FBT (type 24) e snapshot por armazem: total = reservado + disponivel', function () {
    $dto = FbtInventoryUpdateWebhook::fromArray(ttOfwFixture('24-fbt-inventory-update')['data']);
    $detalhe = $dto->fbtWarehouseInventory[0]->onHandDetail;

    expect($detalhe->totalQuantity)->toBe(7)
        ->and($detalhe->reservedQuantity + $detalhe->availableQuantity)->toBe($detalhe->totalQuantity);
});

it('invoice status change (type 36) e por PACOTE, e o motivo do INVALID vem literal', function () {
    $dto = InvoiceStatusChangeWebhook::fromArray(ttOfwFixture('36-invoice-status-change')['data']);

    // Uma NF-e cobre N pedidos combinados no mesmo pacote.
    expect($dto->packageId)->toBe('123456')
        ->and($dto->orderIds)->toBe(['152523', '532123'])
        ->and($dto->invoiceStatus)->toBe('INVALID')
        // NOT_FOUND e' atraso da SEFAZ: reenviar depois, nao e' erro nosso.
        ->and($dto->invalidReason)->toBe('NOT_FOUND');
});

it('status desconhecido do TikTok NAO explode a hidratacao (por isso nao viram enum PHP)', function () {
    $dto = OrderStatusChangeWebhook::fromArray([
        'order_id' => '1',
        'order_status' => 'STATUS_QUE_AINDA_NAO_EXISTE',
        'is_on_hold_order' => true,
    ]);

    expect($dto->orderStatus)->toBe('STATUS_QUE_AINDA_NAO_EXISTE')
        ->and($dto->isOnHoldOrder)->toBeTrue();
});

it('is_on_hold_order false sobrevive ao roundtrip (nao pode virar campo omitido)', function () {
    $data = ttOfwFixture('01-order-status-change')['data'];

    expect(OrderStatusChangeWebhook::fromArray($data)->toArray())->toHaveKey('is_on_hold_order')
        ->and(OrderStatusChangeWebhook::fromArray($data)->isOnHoldOrder)->toBeFalse();
});

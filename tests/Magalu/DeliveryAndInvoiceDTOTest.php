<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Magalu\DTO\Response\Delivery\Delivery;
use SistemAtc\Marketplaces\Magalu\DTO\Response\Invoice\DeliveryInvoice;
use SistemAtc\Marketplaces\Magalu\DTO\Response\Invoice\FulfillmentSignedUrl;

it('DeliveryInvoice: roundtrip lossless na shape real (key/status/issued_at) — omite nulls', function () {
    // shape confirmada em magalu_invoices_raw source=magalu-api: só 3 campos.
    $payload = ['key' => str_repeat('1', 44), 'status' => 'authorized', 'issued_at' => '2026-07-10T12:00:00-03:00'];
    $dto = DeliveryInvoice::fromArray($payload);

    expect($dto->key)->toBe($payload['key'])
        ->and($dto->status)->toBe('authorized')
        ->and($dto->accessKey)->toBeNull()
        // toArray omite os nulls → igual ao payload (INSERT-first lossless)
        ->and($dto->toArray())->toEqual($payload);
});

it('DeliveryInvoice: com xml inline preserva o corpo', function () {
    $dto = DeliveryInvoice::fromArray(['key' => 'K', 'xml' => '<nfeProc>...</nfeProc>', 'status' => 'ok']);

    expect($dto->xml)->toBe('<nfeProc>...</nfeProc>')
        ->and($dto->toArray())->toEqual(['key' => 'K', 'xml' => '<nfeProc>...</nfeProc>', 'status' => 'ok']);
});

it('FulfillmentSignedUrl tipa o signed_url', function () {
    expect(FulfillmentSignedUrl::fromArray(['signed_url' => 'https://storage.googleapis.com/x?sig=y'])->signedUrl)
        ->toBe('https://storage.googleapis.com/x?sig=y');
});

it('Delivery: orderId direto + fallback order aninhado (passthrough)', function () {
    $direct = Delivery::fromArray(['id' => 'D1', 'order_id' => 'O1', 'status' => 'shipped']);
    $nested = Delivery::fromArray(['id' => 'D2', 'order' => ['id' => 'O2', 'code' => 'C2']]);

    expect($direct->orderId)->toBe('O1')
        ->and($nested->orderId)->toBeNull()
        ->and($nested->order)->toBe(['id' => 'O2', 'code' => 'C2']);
});

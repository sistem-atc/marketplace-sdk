<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\DTO\Response\Product\Audit;

/**
 * Changelog "Get Product API v202309: New not_submitted_reasons Response Field
 * Added". O campo explica por que o produto nao chegou a ser submetido a
 * auditoria — e' pre-requisito da LOJA, nao defeito do produto.
 */
it('hidrata not_submitted_reasons quando a loja nao cumpre pre-requisito', function () {
    $dto = Audit::fromArray([
        'status' => 'NONE',
        'not_submitted_reasons' => ['SHOP_INACTIVE'],
    ]);

    expect($dto->status)->toBe('NONE')
        ->and($dto->notSubmittedReasons)->toBe(['SHOP_INACTIVE']);
});

it('aceita mais de um motivo simultaneo', function () {
    $dto = Audit::fromArray([
        'status' => 'NONE',
        'not_submitted_reasons' => ['PAYMENT_ACCOUNT_NOT_LINKED', 'W8_NOT_CONFIGURED'],
    ]);

    expect($dto->notSubmittedReasons)->toHaveCount(2);
});

it('nao explode com motivo desconhecido — a lista fica solta de proposito', function () {
    // Valor novo do TikTok nao pode derrubar a hidratacao.
    $dto = Audit::fromArray([
        'status' => 'NONE',
        'not_submitted_reasons' => ['MOTIVO_QUE_AINDA_NAO_EXISTE'],
    ]);

    expect($dto->notSubmittedReasons)->toBe(['MOTIVO_QUE_AINDA_NAO_EXISTE']);
});

it('roundtrip preserva a lista vazia — [] nao pode virar null', function () {
    // A doc diz que fora do caso NONE a API devolve [], nao ausencia.
    $payload = ['status' => 'APPROVED', 'not_submitted_reasons' => []];

    expect(Audit::fromArray($payload)->toArray()['not_submitted_reasons'])->toBe([]);
});

it('nao perde os campos que ja existiam', function () {
    $payload = [
        'status' => 'NONE',
        'pre_approved_reasons' => ['ALGUM_MOTIVO'],
        'not_submitted_reasons' => ['SHOP_INACTIVE'],
    ];

    expect(Audit::fromArray($payload)->toArray())->toEqual($payload);
});

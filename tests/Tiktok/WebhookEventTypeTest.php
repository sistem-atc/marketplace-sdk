<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\Enums\WebhookEventType;

/**
 * O enum de type codes e' o unico mapa que existe: o payload do webhook manda
 * so' o inteiro `type`, nunca o nome do topico. Estes testes travam os 44
 * codigos oficiais e o comportamento diante de um topico DESCONHECIDO — que e'
 * o cenario que derruba conector (TikTok publica topico novo sem aviso).
 */
it('trava os 44 type codes oficiais do TikTok Shop', function () {
    $codigos = array_map(fn (WebhookEventType $c) => $c->value, WebhookEventType::cases());

    expect($codigos)->toBe([
        1, 3, 4, 5, 6, 7, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23,
        24, 25, 27, 33, 35, 36, 37, 38, 39, 42, 46, 50, 51, 52, 55, 56, 58, 59,
        62, 63, 64, 65, 66, 67, 68,
    ])->and($codigos)->toHaveCount(44);
});

it('a numeracao e ESPARSA — os buracos nao sao topicos, sao ausencias', function () {
    // Quem assumir "type <= 68 existe" cria case fantasma. Estes NAO existem.
    foreach ([2, 8, 9, 10, 26, 28, 29, 30, 31, 32, 34, 40, 41, 43, 44, 45, 47, 48, 49, 53, 54, 57, 60, 61] as $inexistente) {
        expect(WebhookEventType::isKnown($inexistente))->toBeFalse("type {$inexistente} nao deveria existir");
    }
});

it('isKnown protege o controller de topico novo em vez de estourar', function () {
    expect(WebhookEventType::isKnown(1))->toBeTrue()
        ->and(WebhookEventType::isKnown(999))->toBeFalse()
        ->and(WebhookEventType::isKnown(0))->toBeFalse()
        // tryFrom devolve null; e' o `from()` que estoura — por isso nao se usa.
        ->and(WebhookEventType::tryFrom(999))->toBeNull();

    expect(fn () => WebhookEventType::from(999))->toThrow(ValueError::class);
});

it('todo case tem nome legivel, e nenhum se repete', function () {
    $labels = array_map(fn (WebhookEventType $c) => $c->label(), WebhookEventType::cases());

    expect($labels)->toHaveCount(44)
        ->and(array_unique($labels))->toHaveCount(44)
        ->and(array_filter($labels, fn (string $l) => trim($l) === ''))->toBe([]);
});

it('os type codes ja em producao no conector continuam apontando pro mesmo topico', function () {
    // 1, 4, 33 e 36 estao hardcoded no roteador do Bunker desde mai/2026.
    expect(WebhookEventType::from(1))->toBe(WebhookEventType::ORDER_STATUS_CHANGE)
        ->and(WebhookEventType::from(4))->toBe(WebhookEventType::PACKAGE_UPDATE)
        ->and(WebhookEventType::from(33))->toBe(WebhookEventType::NEW_MESSAGE_LISTENER)
        ->and(WebhookEventType::from(36))->toBe(WebhookEventType::INVOICE_STATUS_CHANGE)
        ->and(WebhookEventType::from(36)->label())->toBe('Invoice status change');
});

it('os dois sinais de morte silenciosa da integracao tem case proprio', function () {
    // Sem tratar 6 e 7, a loja e' desautorizada e o conector so' "para de
    // receber pedido" — sem erro, sem alarme.
    expect(WebhookEventType::SELLER_DEAUTHORIZATION->value)->toBe(6)
        ->and(WebhookEventType::UPCOMING_AUTHORIZATION_EXPIRATION->value)->toBe(7)
        ->and(WebhookEventType::CREATOR_DEAUTHORIZATION->value)->toBe(20)
        ->and(WebhookEventType::SAMPLE_APPLICATION_STATUS_CHANGE->label())
        ->toBe('Sample Application Status Change');
});

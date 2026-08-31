<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Event\ShopWebhookListResponseDTO;

/**
 * Event API — gestão de webhook da loja. Antes disso não havia como LISTAR nem
 * CORRIGIR um webhook pela API: descobrir que um evento parou de chegar exigia
 * abrir o App Console. Estes testes fixam o contrato que sustenta essa
 * auditoria.
 */
beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttEventFixture(): array
{
    return json_decode(file_get_contents(__DIR__.'/../Fixtures/Tiktok/get-shop-webhooks.json'), true);
}

function ttEventIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'access',
        refreshToken: 'rt',
        settings: ['app_key' => 'ak', 'app_secret' => 'as', 'shop_cipher' => 'cipher1'],
        active: true,
        expired: false,
    );
}

/** @return list<string> caminhos de chaves do payload que o DTO não devolveu */
function ttEventPerdidos(array $payload, array $serializado): array
{
    $perdidos = [];
    foreach ($payload as $chave => $valor) {
        if (! array_key_exists($chave, $serializado)) {
            $perdidos[] = (string) $chave;

            continue;
        }
        if (is_array($valor) && is_array($serializado[$chave])) {
            foreach (ttEventPerdidos($valor, $serializado[$chave]) as $sub) {
                $perdidos[] = $chave.'.'.$sub;
            }
        }
    }

    return $perdidos;
}

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function () {
    $payload = ttEventFixture();

    $perdidos = ttEventPerdidos($payload, ShopWebhookListResponseDTO::fromArray($payload)->toArray());

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
});

it('webhook nao tem id — a chave e o event_type', function () {
    $dto = ShopWebhookListResponseDTO::fromArray(ttEventFixture());

    // Quem consome tende a procurar um id pra guardar; ele nao existe. O par
    // (loja, event_type) e' que identifica o registro, e e' por event_type que
    // o update e o delete operam.
    expect($dto->webhooks[0]->toArray())->not->toHaveKey('id')
        ->and($dto->webhooks[0]->eventType)->toBe('ORDER_STATUS_CHANGE')
        ->and($dto->webhooks[0]->address)->toBe('https://partner.tiktokshop.com');
});

it('datas do webhook sao epoch em SEGUNDOS', function () {
    $dto = ShopWebhookListResponseDTO::fromArray(ttEventFixture());

    // 1635338186 em segundos = out/2021. Se fosse milissegundo cairia em 1970.
    expect($dto->webhooks[0]->createTime)->toBe(1635338186)
        ->and($dto->webhooks[0]->updateTime)->toBe(1635338186)
        ->and(date('Y', $dto->webhooks[0]->updateTime))->toBe('2021');
});

it('getWebhooks lista os webhooks da loja em GET /event/202309/webhooks', function () {
    Http::fake([
        'open-api.tiktokglobalshop.com/event/202309/webhooks*' => Http::response([
            'code' => 0, 'data' => ttEventFixture(),
        ]),
    ]);

    $webhooks = MarketPlaces::Tiktok()->event(ttEventIntegration())->getWebhooks();

    expect($webhooks)->toHaveCount(1)
        ->and($webhooks[0]->eventType)->toBe('ORDER_STATUS_CHANGE');

    Http::assertSent(fn ($req) => $req->method() === 'GET'
        && str_contains($req->url(), '/event/202309/webhooks'));
});

it('updateWebhook usa PUT (nao POST, apesar da tabela da doc) e manda address + event_type', function () {
    Http::fake([
        'open-api.tiktokglobalshop.com/event/202309/webhooks*' => Http::response(['code' => 0, 'data' => []]),
    ]);

    MarketPlaces::Tiktok()->event(ttEventIntegration())
        ->updateWebhook('https://bunker.example/webhooks/tiktok', 'ORDER_STATUS_CHANGE');

    Http::assertSent(fn ($req) => $req->method() === 'PUT'
        && str_contains($req->body(), '"address":"https:\/\/bunker.example\/webhooks\/tiktok"')
        && str_contains($req->body(), '"event_type":"ORDER_STATUS_CHANGE"'));
});

it('deleteWebhook usa DELETE e identifica o registro so pelo event_type', function () {
    Http::fake([
        'open-api.tiktokglobalshop.com/event/202309/webhooks*' => Http::response(['code' => 0, 'data' => []]),
    ]);

    MarketPlaces::Tiktok()->event(ttEventIntegration())->deleteWebhook('PACKAGE_UPDATE');

    Http::assertSent(fn ($req) => $req->method() === 'DELETE'
        && str_contains($req->body(), '"event_type":"PACKAGE_UPDATE"')
        && ! str_contains($req->body(), 'address'));
});

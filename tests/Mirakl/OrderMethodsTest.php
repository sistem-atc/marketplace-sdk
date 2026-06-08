<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function miraklIntegration(): FakeIntegration
{
    // Convencao padrao: access_token = front API key; settings.base_url = host operador.
    return new FakeIntegration(
        accessToken: 'FRONT-API-KEY',
        settings: ['base_url' => 'https://centauro.mirakl.net'],
    );
}

it('listAllOrders pagina ate cobrir total_count + manda Authorization sem Bearer', function () {
    Http::fake([
        'centauro.mirakl.net/api/orders*' => Http::sequence()
            ->push(['orders' => [['order_id' => 'O1'], ['order_id' => 'O2']], 'total_count' => 3])
            ->push(['orders' => [['order_id' => 'O3']], 'total_count' => 3]),
    ]);

    $result = MarketPlaces::Mirakl()->orders(miraklIntegration())->listAllOrders([
        'start_update_date' => '2026-05-01T00:00:00Z',
        'max' => 2,
    ]);

    expect($result['orders'])->toHaveCount(3)
        ->and($result['total_count'])->toBe(3)
        ->and(array_column($result['orders'], 'order_id'))->toBe(['O1', 'O2', 'O3']);

    Http::assertSent(fn ($req) => $req->hasHeader('Authorization', 'FRONT-API-KEY')
        && str_contains($req->url(), '/api/orders'));
});

it('getOrder devolve o primeiro order do payload', function () {
    Http::fake([
        'centauro.mirakl.net/api/orders*' => Http::response(['orders' => [['order_id' => 'O9', 'order_state' => 'SHIPPED']]]),
    ]);

    $order = MarketPlaces::Mirakl()->orders(miraklIntegration())->getOrder('O9');

    expect($order['order_id'])->toBe('O9')
        ->and($order['order_state'])->toBe('SHIPPED');
});

it('listAllOrders para quando vem pagina vazia (sem total_count)', function () {
    Http::fake([
        'centauro.mirakl.net/api/orders*' => Http::response(['orders' => []]),
    ]);

    $result = MarketPlaces::Mirakl()->orders(miraklIntegration())->listAllOrders();

    expect($result['orders'])->toBe([])
        ->and($result['total_count'])->toBe(0);
});

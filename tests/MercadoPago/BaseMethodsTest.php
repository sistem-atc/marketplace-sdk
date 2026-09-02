<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders\MerchantOrderResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders\OrderResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments\PaymentResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\Exceptions\MercadoPagoRequestException;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function mpBaseIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'APP_USR-fake',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config(['marketplaces.mercadopago.api_base' => 'https://api.mercadopago.com']);
});

describe('X-Idempotency-Key', function () {
    it('gera UUID automatico em POST/PUT/PATCH, como o SDK oficial', function () {
        Http::fake(fn () => Http::response(['id' => 1]));
        $mp = MarketPlaces::MercadoPago();

        $mp->payments(mpBaseIntegration())->create(['transaction_amount' => 1]);
        $mp->payments(mpBaseIntegration())->cancel(1);
        $mp->point(mpBaseIntegration())->changeDeviceOperatingMode('D', 'PDV');

        Http::assertSentCount(3);
        Http::assertSent(fn (Request $r) => $r->hasHeader('X-Idempotency-Key')
            && preg_match('/^[0-9a-f-]{36}$/', $r->header('X-Idempotency-Key')[0]) === 1);
    });

    it('NAO manda a chave em GET/DELETE', function () {
        Http::fake(fn () => Http::response([]));

        MarketPlaces::MercadoPago()->payments(mpBaseIntegration())->get(1);
        MarketPlaces::MercadoPago()->customers(mpBaseIntegration())->delete('c');

        Http::assertSent(fn (Request $r) => ! $r->hasHeader('X-Idempotency-Key'));
        Http::assertNotSent(fn (Request $r) => $r->hasHeader('X-Idempotency-Key'));
    });

    it('respeita a chave do caller (retry legitimo devolve o MESMO pagamento)', function () {
        Http::fake(fn () => Http::response(['id' => 1]));

        MarketPlaces::MercadoPago()->payments(mpBaseIntegration())->create(['x' => 1], idempotencyKey: 'order-123');
        MarketPlaces::MercadoPago()->refunds(mpBaseIntegration())->create(1, 2.0, idempotencyKey: 'refund-123');

        Http::assertSent(fn (Request $r) => $r->hasHeader('X-Idempotency-Key', 'order-123'));
        Http::assertSent(fn (Request $r) => $r->hasHeader('X-Idempotency-Key', 'refund-123'));
    });

    it('nao vaza a chave de uma chamada pra proxima do mesmo client', function () {
        Http::fake(fn () => Http::response(['id' => 1]));
        $payments = MarketPlaces::MercadoPago()->payments(mpBaseIntegration());

        $payments->create(['x' => 1], idempotencyKey: 'first');
        $payments->get(1);

        Http::assertSent(fn (Request $r) => $r->method() === 'GET' && ! $r->hasHeader('X-Idempotency-Key'));
    });

    it('mantem a MESMA chave no retry de 5xx: e para isso que ela existe', function () {
        Http::fake([
            'api.mercadopago.com/v1/payments' => Http::sequence()
                ->push(['message' => 'boom'], 500, ['Retry-After' => '0'])
                ->push(['id' => 7]),
        ]);

        $resp = MarketPlaces::MercadoPago()->payments(mpBaseIntegration())->create(['x' => 1]);

        expect($resp)->toBeInstanceOf(PaymentResponseDTO::class)->and($resp->id)->toBe(7);

        $keys = [];
        Http::assertSent(function (Request $r) use (&$keys) {
            $keys[] = $r->header('X-Idempotency-Key')[0] ?? null;

            return true;
        });
        expect($keys)->toHaveCount(2)->and($keys[0])->toBe($keys[1])->not->toBeNull();
    });
});

describe('paginate (searchAll)', function () {
    it('percorre todas as paginas ate paging.total, mesmo quando total vem como STRING (Orders v2)', function () {
        Http::fake(function (Request $r) {
            parse_str(parse_url($r->url(), PHP_URL_QUERY) ?? '', $q);
            $offset = (int) ($q['offset'] ?? 0);
            $page = match ($offset) {
                0 => ['results' => [['id' => 'a'], ['id' => 'b']], 'paging' => ['total' => '3', 'limit' => 2, 'offset' => 0]],
                2 => ['results' => [['id' => 'c']], 'paging' => ['total' => '3', 'limit' => 2, 'offset' => 2]],
                default => throw new RuntimeException("offset inesperado {$offset}"),
            };

            return Http::response($page);
        });

        $orders = iterator_to_array(MarketPlaces::MercadoPago()->orders(mpBaseIntegration())->searchAll(['status' => 'processed'], limit: 2), false);

        expect($orders)->each->toBeInstanceOf(OrderResponseDTO::class)
            ->and(array_map(fn (OrderResponseDTO $o) => $o->id, $orders))->toBe(['a', 'b', 'c']);
        Http::assertSentCount(2);
        Http::assertSent(fn (Request $r) => str_contains($r->url(), 'status=processed') && str_contains($r->url(), 'limit=2'));
    });

    it('le `elements` (merchant_orders) e para na pagina curta', function () {
        Http::fake(fn () => Http::response(['elements' => [['id' => 1]], 'total' => 1]));

        $items = iterator_to_array(MarketPlaces::MercadoPago()->merchantOrders(mpBaseIntegration())->searchAll(limit: 50), false);

        expect($items)->toHaveCount(1)
            ->and($items[0])->toBeInstanceOf(MerchantOrderResponseDTO::class)
            ->and($items[0]->id)->toBe(1);
        Http::assertSentCount(1);
    });

    it('pagina vazia encerra sem loop', function () {
        Http::fake(fn () => Http::response(['results' => [], 'paging' => ['total' => 0]]));

        expect(iterator_to_array(MarketPlaces::MercadoPago()->payments(mpBaseIntegration())->searchAll(), false))->toBe([]);
        Http::assertSentCount(1);
    });
});

describe('erros', function () {
    it('4xx terminal vira MercadoPagoRequestException com status', function () {
        Http::fake(fn () => Http::response(['message' => 'bad'], 400));

        try {
            MarketPlaces::MercadoPago()->payments(mpBaseIntegration())->create([]);
            $this->fail('deveria lancar');
        } catch (MercadoPagoRequestException $e) {
            expect($e->status())->toBe(400)->and($e->getMessage())->toContain('bad');
        }
    });
});

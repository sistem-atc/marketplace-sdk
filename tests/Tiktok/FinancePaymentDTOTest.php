<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance\Payment;

/**
 * Payment = a transferencia que CAIU no banco do vendedor. O SDK so' tinha o
 * statement (quanto o TikTok apurou); sem este endpoint nao havia como provar
 * que o repasse foi efetivamente pago.
 */
function fixtureTiktokPayment(): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/get-payments.json'),
        true,
    );
}

function ttPaymentsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'access',
        refreshToken: 'rt',
        settings: [
            'app_key' => 'ak',
            'app_secret' => 'as',
            'shop_id' => 'shop1',
            'shop_cipher' => 'cipher1',
        ],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

it('separa o apurado, o a-pagar e o efetivamente pago — cada um com sua moeda', function () {
    $p = Payment::fromArray(fixtureTiktokPayment());

    // Os tres montantes vem embrulhados em objeto porque podem estar em
    // moedas diferentes (loja cross-border). Ler so' o `value` mistura moeda.
    expect($p->settlementAmount->value)->toBe('130')
        ->and($p->settlementAmount->currency)->toBe('GBP')
        ->and($p->paymentAmountBeforeExchange->value)->toBe('100')
        ->and($p->amount->value)->toBe('100')
        ->and($p->amount->currency)->toBe('GBP');
});

it('mantem dinheiro e cambio como STRING', function () {
    $p = Payment::fromArray(fixtureTiktokPayment());

    // Seis casas decimais: virar float perderia precisao do rateio.
    expect($p->exchangeRate)->toBe('1.000000')
        ->and($p->amount->value)->toBeString();
});

it('so status PAID tem paidTime — e a conta vem mascarada', function () {
    $p = Payment::fromArray(fixtureTiktokPayment());

    expect($p->status)->toBe('PAID')
        ->and($p->paidTime)->toBe(1685548800)   // epoch em SEGUNDOS
        ->and($p->createTime)->toBe(1636105796)
        // O TikTok mascara: serve pra conferir a conta, nunca pra cadastrar.
        ->and($p->bankAccount)->toBe('***********1234');
});

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function () {
    $payload = fixtureTiktokPayment();

    $perdidos = array_diff(
        array_keys($payload),
        array_keys(Payment::fromArray($payload)->toArray()),
    );

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
});

it('getPayments preenche o sort_field obrigatorio e devolve o cursor', function () {
    Http::fake([
        'open-api.tiktokglobalshop.com/finance/202605/payments*' => Http::response([
            'code' => 0,
            'message' => 'Success',
            'data' => [
                'next_page_token' => 'NEXT',
                'payments' => [fixtureTiktokPayment()],
            ],
        ]),
    ]);

    $resp = MarketPlaces::Tiktok()->finance(ttPaymentsIntegration())->getPayments();

    expect($resp['payments'])->toHaveCount(1)
        ->and($resp['payments'][0])->toBeInstanceOf(Payment::class)
        ->and($resp['next_page_token'])->toBe('NEXT');

    // Sem sort_field a API responde erro de parametro; o SDK ja' manda o
    // unico valor aceito.
    Http::assertSent(fn ($req) => str_contains($req->url(), 'sort_field=create_time'));
});

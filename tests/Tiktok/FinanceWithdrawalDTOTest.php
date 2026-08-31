<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance\Withdrawal;

/**
 * Withdrawal = movimentacao do SALDO da loja. Apesar do nome, a lista mistura
 * saque, liquidacao, subsidio e estorno — quem separa e' o `type`.
 */
function fixtureTiktokWithdrawal(): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/get-withdrawals.json'),
        true,
    );
}

function ttWithdrawalsIntegration(): FakeIntegration
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

it('hidrata a movimentacao com o type que a distingue de saque puro', function () {
    $w = Withdrawal::fromArray(fixtureTiktokWithdrawal());

    expect($w->id)->toBe('EFASDFSAFDA23432DFAFDSA')
        // WITHDRAW/SETTLE/TRANSFER/REVERSE: somar tudo sem olhar o type
        // conta o mesmo dinheiro duas vezes (SETTLE credita, WITHDRAW move).
        ->and($w->type)->toBe('WITHDRAW')
        ->and($w->currency)->toBe('IDR')
        ->and($w->createTime)->toBe(1623812664);   // epoch em SEGUNDOS
});

it('dinheiro continua STRING e o sucesso aqui e SUCCESS, nao PAID', function () {
    $w = Withdrawal::fromArray(fixtureTiktokWithdrawal());

    expect($w->amount)->toBe('100')
        ->and($w->amount)->toBeString()
        // Vocabulario diferente do Payment (que usa PAID) — checar a string
        // errada faz o saque pago parecer pendente pra sempre.
        ->and($w->status)->toBe('PROCESSING');
});

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function () {
    $payload = fixtureTiktokWithdrawal();

    $perdidos = array_diff(
        array_keys($payload),
        array_keys(Withdrawal::fromArray($payload)->toArray()),
    );

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
});

it('getWithdrawals manda types por virgula — array quebraria a assinatura', function () {
    Http::fake([
        'open-api.tiktokglobalshop.com/finance/202309/withdrawals*' => Http::response([
            'code' => 0,
            'message' => 'Success',
            'data' => [
                'next_page_token' => 'NEXT',
                'total_count' => 1,
                'withdrawals' => [fixtureTiktokWithdrawal()],
            ],
        ]),
    ]);

    $resp = MarketPlaces::Tiktok()->finance(ttWithdrawalsIntegration())->getWithdrawals();

    expect($resp['withdrawals'])->toHaveCount(1)
        ->and($resp['withdrawals'][0])->toBeInstanceOf(Withdrawal::class)
        ->and($resp['total_count'])->toBe(1);

    // `types` e' obrigatorio; o default pede os 4 pra nao esconder o SETTLE.
    // Serializado como CSV: http_build_query faria `types%5B0%5D=`, que nao
    // bate com o JSON que o SignatureGenerator assina.
    Http::assertSent(function ($req) {
        return str_contains(urldecode($req->url()), 'types=WITHDRAW,SETTLE,TRANSFER,REVERSE')
            && ! str_contains($req->url(), 'types%5B0%5D');
    });
});

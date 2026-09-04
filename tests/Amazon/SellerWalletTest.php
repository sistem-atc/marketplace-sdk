<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\SellerWallet;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function sellerWallet(): SellerWallet
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new SellerWallet(new Client($integration));
}

/** Um único request enviado, com verbo + URL exata + token LWA. */
function sellerWalletAssertSent(string $method, string $url, ?array $body = null, string $token = 'Atza|valid-token'): void
{
    Http::assertSent(function (Request $req) use ($method, $url, $body, $token): bool {
        if ($req->method() !== $method || $req->url() !== $url) {
            return false;
        }
        if ($req->header('x-amz-access-token')[0] !== $token) {
            return false;
        }
        if ($body !== null && $req->data() !== $body) {
            return false;
        }

        return true;
    });
}

const SW = 'https://sellingpartnerapi-na.amazon.com/finances/transfers/wallet/2024-03-01';

it('listAccounts GET /accounts', function () {
    Http::fake(['*/wallet/2024-03-01/accounts*' => Http::response(['accounts' => [['accountId' => 'ACC1']]])]);
    expect(sellerWallet()->listAccounts('A2Q3Y263D00KWC')['accounts'][0]['accountId'])->toBe('ACC1');
    sellerWalletAssertSent('GET', SW.'/accounts?marketplaceId=A2Q3Y263D00KWC');
});

it('getAccount GET /accounts/{id}', function () {
    Http::fake(['*/wallet/2024-03-01/accounts/ACC1*' => Http::response(['accountId' => 'ACC1'])]);
    sellerWallet()->getAccount('ACC1', 'A2Q3Y263D00KWC');
    sellerWalletAssertSent('GET', SW.'/accounts/ACC1?marketplaceId=A2Q3Y263D00KWC');
});

it('listAccountBalances GET /accounts/{id}/balance', function () {
    Http::fake(['*/wallet/2024-03-01/accounts/ACC1/balance*' => Http::response(['balances' => []])]);
    sellerWallet()->listAccountBalances('ACC1', 'A2Q3Y263D00KWC');
    sellerWalletAssertSent('GET', SW.'/accounts/ACC1/balance?marketplaceId=A2Q3Y263D00KWC');
});

it('getTransferPreview GET /transferPreview com todos os obrigatorios', function () {
    Http::fake(['*/wallet/2024-03-01/transferPreview*' => Http::response(['baseAmount' => 100])]);
    sellerWallet()->getTransferPreview(marketplaceId: 'A2Q3Y263D00KWC', sourceCountryCode: 'US', sourceCurrencyCode: 'USD', destinationCountryCode: 'BR', destinationCurrencyCode: 'BRL', baseAmount: 100.5);
    sellerWalletAssertSent('GET', SW.'/transferPreview?sourceCountryCode=US&sourceCurrencyCode=USD&destinationCountryCode=BR&destinationCurrencyCode=BRL&baseAmount=100.5&marketplaceId=A2Q3Y263D00KWC');
});

it('listAccountTransactions GET /transactions com nextPageToken', function () {
    Http::fake(['*/wallet/2024-03-01/transactions?*' => Http::response(['transactions' => [], 'nextPageToken' => null])]);
    sellerWallet()->listAccountTransactions('ACC1', 'A2Q3Y263D00KWC', 'P2');
    sellerWalletAssertSent('GET', SW.'/transactions?accountId=ACC1&marketplaceId=A2Q3Y263D00KWC&nextPageToken=P2');
});

it('getTransaction GET /transactions/{id}', function () {
    Http::fake(['*/wallet/2024-03-01/transactions/TX1*' => Http::response(['transactionId' => 'TX1'])]);
    sellerWallet()->getTransaction('TX1', 'A2Q3Y263D00KWC');
    sellerWalletAssertSent('GET', SW.'/transactions/TX1?marketplaceId=A2Q3Y263D00KWC');
});

it('listTransferSchedules GET /transferSchedules', function () {
    Http::fake(['*/wallet/2024-03-01/transferSchedules?*' => Http::response(['transferSchedules' => []])]);
    sellerWallet()->listTransferSchedules('ACC1', 'A2Q3Y263D00KWC');
    sellerWalletAssertSent('GET', SW.'/transferSchedules?accountId=ACC1&marketplaceId=A2Q3Y263D00KWC');
});

it('getTransferSchedule GET /transferSchedules/{id}', function () {
    Http::fake(['*/wallet/2024-03-01/transferSchedules/TS1*' => Http::response(['transferScheduleId' => 'TS1'])]);
    sellerWallet()->getTransferSchedule('TS1', 'A2Q3Y263D00KWC');
    sellerWalletAssertSent('GET', SW.'/transferSchedules/TS1?marketplaceId=A2Q3Y263D00KWC');
});

it('deleteScheduleTransaction DELETE /transferSchedules/{id} com marketplaceId na query', function () {
    Http::fake(['*/wallet/2024-03-01/transferSchedules/TS1*' => Http::response(['code' => 'OK'])]);
    sellerWallet()->deleteScheduleTransaction('TS1', 'A2Q3Y263D00KWC');
    sellerWalletAssertSent('DELETE', SW.'/transferSchedules/TS1?marketplaceId=A2Q3Y263D00KWC');
});

it('createTransaction → POST /transactions?marketplaceId com headers de assinatura', function () {
    Http::fake(['*' => Http::response(['transactionId' => 'tx-1'])]);

    $body = ['sourceAccountId' => 'acc-1', 'destinationAccountId' => 'acc-2', 'sourceTransactionAmount' => ['amount' => 100, 'currencyCode' => 'USD']];
    $resp = sellerWallet()->createTransaction('A2Q3Y263D00KWC', $body, 'sig-dest', 'sig-amount');

    expect($resp['transactionId'])->toBe('tx-1');
    sellerWalletAssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/finances/transfers/wallet/2024-03-01/transactions?marketplaceId=A2Q3Y263D00KWC', $body);
    Http::assertSent(fn (Request $r) => $r->hasHeader('destAccountDigitalSignature', 'sig-dest')
        && $r->hasHeader('amountDigitalSignature', 'sig-amount'));
});

it('createTransferSchedule → POST /transferSchedules?marketplaceId com headers de assinatura', function () {
    Http::fake(['*' => Http::response(['transferScheduleId' => 'ts-1'])]);

    $body = ['sourceAccountId' => 'acc-1', 'transferScheduleInformation' => ['scheduleType' => 'WEEKLY']];
    sellerWallet()->createTransferSchedule('A2Q3Y263D00KWC', $body, 'sig-dest', 'sig-amount');

    sellerWalletAssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/finances/transfers/wallet/2024-03-01/transferSchedules?marketplaceId=A2Q3Y263D00KWC', $body);
    Http::assertSent(fn (Request $r) => $r->hasHeader('destAccountDigitalSignature', 'sig-dest')
        && $r->hasHeader('amountDigitalSignature', 'sig-amount'));
});

it('updateTransferSchedule → PUT /transferSchedules?marketplaceId com headers de assinatura', function () {
    Http::fake(['*' => Http::response(['transferScheduleId' => 'ts-1'])]);

    $body = ['transferScheduleId' => 'ts-1', 'transferScheduleStatus' => 'PAUSED'];
    sellerWallet()->updateTransferSchedule('A2Q3Y263D00KWC', $body, 'sig-dest', 'sig-amount');

    sellerWalletAssertSent('PUT', 'https://sellingpartnerapi-na.amazon.com/finances/transfers/wallet/2024-03-01/transferSchedules?marketplaceId=A2Q3Y263D00KWC', $body);
    Http::assertSent(fn (Request $r) => $r->hasHeader('destAccountDigitalSignature', 'sig-dest')
        && $r->hasHeader('amountDigitalSignature', 'sig-amount'));
});

it('operações de leitura não enviam headers de assinatura', function () {
    Http::fake(['*' => Http::response(['accounts' => []])]);

    sellerWallet()->listAccounts('A2Q3Y263D00KWC');

    Http::assertSent(fn (Request $r) => ! $r->hasHeader('destAccountDigitalSignature')
        && ! $r->hasHeader('amountDigitalSignature'));
});

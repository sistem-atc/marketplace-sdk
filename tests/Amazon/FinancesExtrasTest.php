<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Finances;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function financesExtras(): Finances
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new Finances(new Client($integration));
}

/** Um único request enviado, com verbo + URL exata + token LWA. */
function financesExtrasAssertSent(string $method, string $url, ?array $body = null, string $token = 'Atza|valid-token'): void
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

it('listTransactions GET /finances/2024-06-19/transactions com filtros csv e devolve o JSON inteiro', function () {
    Http::fake(['*/finances/2024-06-19/transactions*' => Http::response(['payload' => ['transactions' => [['transactionType' => 'Shipment']], 'nextToken' => 'N2']])]);

    $resp = financesExtras()->listTransactions(['postedAfter' => '2026-08-01T00:00:00Z', 'marketplaceId' => 'A2Q3Y263D00KWC', 'transactionStatus' => 'RELEASED']);

    expect($resp['payload']['transactions'][0]['transactionType'])->toBe('Shipment')
        ->and($resp['payload']['nextToken'])->toBe('N2');
    financesExtrasAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/finances/2024-06-19/transactions?postedAfter=2026-08-01T00%3A00%3A00Z&marketplaceId=A2Q3Y263D00KWC&transactionStatus=RELEASED');
});

it('listBalances GET /finances/2024-06-19/balances com marketplaceIds csv', function () {
    Http::fake(['*/finances/2024-06-19/balances*' => Http::response(['balances' => []])]);

    financesExtras()->listBalances(['marketplaceIds' => ['A', 'B'], 'balanceType' => 'AVAILABLE']);

    financesExtrasAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/finances/2024-06-19/balances?marketplaceIds=A%2CB&balanceType=AVAILABLE');
});

it('listSummary GET /finances/2024-06-19/summary por settlement', function () {
    Http::fake(['*/finances/2024-06-19/summary*' => Http::response(['summaries' => []])]);

    financesExtras()->listSummary(['relatedIdentifierName' => 'SETTLEMENT_ID', 'relatedIdentifierValue' => '123']);

    financesExtrasAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/finances/2024-06-19/summary?relatedIdentifierName=SETTLEMENT_ID&relatedIdentifierValue=123');
});

it('initiatePayout POST /finances/transfers/2024-06-01/payouts com body', function () {
    Http::fake(['*/finances/transfers/2024-06-01/payouts' => Http::response(['payoutReferenceId' => 'P1'])]);

    $resp = financesExtras()->initiatePayout(marketplaceId: 'A2Q3Y263D00KWC', accountType: 'Standard Orders');

    expect($resp['payoutReferenceId'])->toBe('P1');
    financesExtrasAssertSent('POST', 'https://sellingpartnerapi-na.amazon.com/finances/transfers/2024-06-01/payouts', ['marketplaceId' => 'A2Q3Y263D00KWC', 'accountType' => 'Standard Orders']);
});

it('listPayouts GET /finances/transfers/2024-06-01/payouts', function () {
    Http::fake(['*/finances/transfers/2024-06-01/payouts*' => Http::response(['payouts' => [], 'nextToken' => null])]);

    financesExtras()->listPayouts(['marketplaceIds' => ['A2Q3Y263D00KWC'], 'createdAfter' => '2026-08-01T00:00:00Z']);

    financesExtrasAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/finances/transfers/2024-06-01/payouts?marketplaceIds=A2Q3Y263D00KWC&createdAfter=2026-08-01T00%3A00%3A00Z');
});

it('getPaymentMethods GET /finances/transfers/2024-06-01/paymentMethods com tipos csv', function () {
    Http::fake(['*/finances/transfers/2024-06-01/paymentMethods*' => Http::response(['paymentMethods' => []])]);

    financesExtras()->getPaymentMethods('A2Q3Y263D00KWC', ['BANK_ACCOUNT', 'CARD']);

    financesExtrasAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/finances/transfers/2024-06-01/paymentMethods?marketplaceId=A2Q3Y263D00KWC&paymentMethodTypes=BANK_ACCOUNT%2CCARD');
});

it('listExpectedPayouts GET /finances/transfers/2024-06-01/payouts/expected', function () {
    Http::fake(['*/finances/transfers/2024-06-01/payouts/expected*' => Http::response(['expectedPayouts' => []])]);

    financesExtras()->listExpectedPayouts(['accountType' => 'Standard Orders']);

    financesExtrasAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/finances/transfers/2024-06-01/payouts/expected?accountType=Standard%20Orders');
});

it('getInvoiceHeaders GET /finances/invoices/2026-06-25/invoices exige marketplaceId', function () {
    Http::fake(['*/finances/invoices/2026-06-25/invoices*' => Http::response(['invoices' => [], 'numOfRecords' => 0])]);

    financesExtras()->getInvoiceHeaders('A2Q3Y263D00KWC', ['fromIssueDate' => '2026-06-01', 'toIssueDate' => '2026-08-30']);

    financesExtrasAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/finances/invoices/2026-06-25/invoices?marketplaceId=A2Q3Y263D00KWC&fromIssueDate=2026-06-01&toIssueDate=2026-08-30');
});

it('getInvoice GET /finances/invoices/2026-06-25/invoices/{id} com token de itens', function () {
    Http::fake(['*/finances/invoices/2026-06-25/invoices/INV%2F1*' => Http::response(['invoiceHeader' => ['invoiceIdentifier' => 'INV/1'], 'invoiceItems' => []])]);

    $resp = financesExtras()->getInvoice('INV/1', 'A2Q3Y263D00KWC', 'T2');

    expect($resp['invoiceHeader']['invoiceIdentifier'])->toBe('INV/1');
    financesExtrasAssertSent('GET', 'https://sellingpartnerapi-na.amazon.com/finances/invoices/2026-06-25/invoices/INV%2F1?marketplaceId=A2Q3Y263D00KWC&nextTokenForLineItems=T2');
});

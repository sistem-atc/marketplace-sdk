<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Payment\PaymentMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function paymentExtrasIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );
}

function paymentExtrasMethods(): PaymentMethods
{
    $integration = paymentExtrasIntegration();

    return new PaymentMethods(HttpClientFactory::make($integration), $integration);
}

/** Verbo + path + assinatura de loja (partner_id, timestamp, sign, shop_id). */
function paymentExtrasAssertShopCall(string $verb, string $path, ?callable $extra = null): void
{
    Http::assertSent(function (Request $req) use ($verb, $path, $extra) {
        $url = $req->url();

        return $req->method() === $verb
            && str_contains($url, $path)
            && str_contains($url, 'partner_id=2030136')
            && str_contains($url, 'timestamp=')
            && str_contains($url, 'sign=')
            && str_contains($url, 'shop_id=999999')
            && str_contains($url, 'access_token=shopee-token')
            && ($extra === null || $extra($req));
    });
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

describe('PaymentMethods extras — escrow', function () {
    it('getEscrowDetailBatch GET com order_sn_list csv e devolve a lista do response', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/get_escrow_detail_batch*' => Http::response([
            'error' => '', 'response' => [['escrow_detail' => ['order_sn' => 'A1']], ['escrow_detail' => ['order_sn' => 'B2']]],
        ])]);

        $rows = paymentExtrasMethods()->getEscrowDetailBatch(['A1', 'B2']);

        expect($rows)->toHaveCount(2)->and($rows[1]['escrow_detail']['order_sn'])->toBe('B2');
        paymentExtrasAssertShopCall('GET', '/api/v2/payment/get_escrow_detail_batch', fn ($req) => str_contains($req->url(), 'order_sn_list=A1%2CB2'));
    });

    it('getEscrowList GET com janela de release_time + paginação', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/get_escrow_list*' => Http::response([
            'error' => '', 'response' => ['escrow_list' => [['order_sn' => 'X', 'payout_amount' => 10.5]], 'more' => true],
        ])]);

        $out = paymentExtrasMethods()->getEscrowList(releaseTimeFrom: 1700000000, releaseTimeTo: 1700600000, pageSize: 100, pageNo: 2);

        expect($out['more'])->toBeTrue()->and($out['escrow_list'][0]['payout_amount'])->toBe(10.5);
        paymentExtrasAssertShopCall('GET', '/api/v2/payment/get_escrow_list', fn ($req) => str_contains($req->url(), 'release_time_from=1700000000')
            && str_contains($req->url(), 'release_time_to=1700600000')
            && str_contains($req->url(), 'page_size=100')
            && str_contains($req->url(), 'page_no=2'));
    });
});

describe('PaymentMethods extras — payout (CB)', function () {
    it('getPayoutDetail GET paginado por page_no', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/get_payout_detail*' => Http::response([
            'error' => '', 'response' => ['payout_list' => [['payout_amount' => 1]], 'more' => false],
        ])]);

        $out = paymentExtrasMethods()->getPayoutDetail(1700000000, 1701000000);

        expect($out['payout_list'])->toHaveCount(1)->and($out['more'])->toBeFalse();
        paymentExtrasAssertShopCall('GET', '/api/v2/payment/get_payout_detail', fn ($req) => str_contains($req->url(), 'payout_time_from=1700000000')
            && str_contains($req->url(), 'payout_time_to=1701000000')
            && str_contains($req->url(), 'page_size=100')
            && str_contains($req->url(), 'page_no=1'));
    });

    it('getPayoutInfo GET com cursor e devolve next_cursor', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/get_payout_info*' => Http::response([
            'error' => '', 'response' => ['payout_list' => [], 'more' => true, 'next_cursor' => 'abc'],
        ])]);

        $out = paymentExtrasMethods()->getPayoutInfo(1700000000, 1701000000, 50, 'prev');

        expect($out['next_cursor'])->toBe('abc')->and($out['more'])->toBeTrue();
        paymentExtrasAssertShopCall('GET', '/api/v2/payment/get_payout_info', fn ($req) => str_contains($req->url(), 'cursor=prev')
            && str_contains($req->url(), 'page_size=50'));
    });

    it('getBillingTransactionInfo POST com tipo, cursor, page_size e encrypted_payout_ids no body', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/get_billing_transaction_info*' => Http::response([
            'error' => '', 'response' => ['transactions' => [['amount' => -594.78, 'order_sn' => 'S1']], 'more' => false, 'next_cursor' => ''],
        ])]);

        $out = paymentExtrasMethods()->getBillingTransactionInfo(billingTransactionInfoType: 2, cursor: '', pageSize: 100, encryptedPayoutIds: ['p1', 'p2']);

        expect($out['transactions'][0]['amount'])->toBe(-594.78)->and($out['more'])->toBeFalse()->and($out['next_cursor'])->toBe('');
        paymentExtrasAssertShopCall('POST', '/api/v2/payment/get_billing_transaction_info', fn ($req) => str_contains($req->body(), '"billing_transaction_info_type":2')
            && str_contains($req->body(), '"cursor":""')
            && str_contains($req->body(), '"page_size":100')
            && str_contains($req->body(), '"encrypted_payout_ids":["p1","p2"]'));
    });

    it('getBillingTransactionInfo omite encrypted_payout_ids quando vazio', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/get_billing_transaction_info*' => Http::response(['error' => '', 'response' => []])]);

        paymentExtrasMethods()->getBillingTransactionInfo(1);

        Http::assertSent(fn (Request $req) => ! str_contains($req->body(), 'encrypted_payout_ids'));
    });
});

describe('PaymentMethods extras — income', function () {
    it('getIncomeOverview GET com income_status opcional', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/get_income_overview*' => Http::response([
            'error' => '', 'response' => ['latest_payout_date' => 1700000000, 'total_income' => []],
        ])]);

        $out = paymentExtrasMethods()->getIncomeOverview(1);

        expect($out['latest_payout_date'])->toBe(1700000000);
        paymentExtrasAssertShopCall('GET', '/api/v2/payment/get_income_overview', fn ($req) => str_contains($req->url(), 'income_status=1'));
    });

    it('getIncomeOverview sem filtro não manda income_status', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/get_income_overview*' => Http::response(['error' => '', 'response' => []])]);

        paymentExtrasMethods()->getIncomeOverview();

        Http::assertSent(fn (Request $req) => ! str_contains($req->url(), 'income_status='));
    });

    it('getIncomeDetail GET com datas YYYY-MM-DD, status, page_size e cursor; devolve body cru', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/get_income_detail*' => Http::response([
            'error' => '', 'income_detail_list' => ['list' => [['order_sn' => 'Z']]],
        ])]);

        $out = paymentExtrasMethods()->getIncomeDetail('2026-08-01', '2026-08-20', 1, 5, '');

        expect($out['income_detail_list']['list'][0]['order_sn'])->toBe('Z');
        paymentExtrasAssertShopCall('GET', '/api/v2/payment/get_income_detail', fn ($req) => str_contains($req->url(), 'date_from=2026-08-01')
            && str_contains($req->url(), 'date_to=2026-08-20')
            && str_contains($req->url(), 'income_status=1')
            && str_contains($req->url(), 'page_size=5'));
    });

    it('generateIncomeStatement GET devolve o id e manda statement_type', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/generate_income_statement*' => Http::response(['error' => '', 'response' => ['id' => 4242]])]);

        $id = paymentExtrasMethods()->generateIncomeStatement(1700000000, 1700600000, 1);

        expect($id)->toBe(4242);
        paymentExtrasAssertShopCall('GET', '/api/v2/payment/generate_income_statement', fn ($req) => str_contains($req->url(), 'release_time_from=1700000000')
            && str_contains($req->url(), 'release_time_to=1700600000')
            && str_contains($req->url(), 'statement_type=1'));
    });

    it('getIncomeStatement GET por income_statement_id', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/get_income_statement*' => Http::response([
            'error' => '', 'response' => ['id' => 4242, 'status' => 'READY', 'file_link' => 'https://x/y.pdf'],
        ])]);

        $out = paymentExtrasMethods()->getIncomeStatement(4242);

        expect($out['file_link'])->toBe('https://x/y.pdf');
        paymentExtrasAssertShopCall('GET', '/api/v2/payment/get_income_statement', fn ($req) => str_contains($req->url(), 'income_statement_id=4242'));
    });

    it('generateIncomeReport GET devolve o id', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/generate_income_report*' => Http::response(['error' => '', 'response' => ['id' => 77]])]);

        expect(paymentExtrasMethods()->generateIncomeReport(1700000000, 1700600000))->toBe(77);
        paymentExtrasAssertShopCall('GET', '/api/v2/payment/generate_income_report', fn ($req) => str_contains($req->url(), 'release_time_from=1700000000')
            && str_contains($req->url(), 'release_time_to=1700600000'));
    });

    it('getIncomeReport GET por income_report_id', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/get_income_report*' => Http::response(['error' => '', 'response' => ['id' => 77, 'status' => 'PROCESSING']])]);

        expect(paymentExtrasMethods()->getIncomeReport(77)['status'])->toBe('PROCESSING');
        paymentExtrasAssertShopCall('GET', '/api/v2/payment/get_income_report', fn ($req) => str_contains($req->url(), 'income_report_id=77'));
    });
});

describe('PaymentMethods extras — público e parcelamento', function () {
    it('getPaymentMethodList usa assinatura pública (sem shop_id/access_token)', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/get_payment_method_list*' => Http::response([
            'error' => '', 'response' => [['payment_method' => ['Boleto', 'Pix'], 'region' => 'BR']],
        ])]);

        $out = paymentExtrasMethods()->getPaymentMethodList();

        expect($out[0]['region'])->toBe('BR');
        Http::assertSent(fn (Request $req) => $req->method() === 'GET'
            && str_contains($req->url(), '/api/v2/payment/get_payment_method_list')
            && str_contains($req->url(), 'partner_id=2030136')
            && str_contains($req->url(), 'timestamp=')
            && str_contains($req->url(), 'sign=')
            && ! str_contains($req->url(), 'shop_id=')
            && ! str_contains($req->url(), 'access_token='));
    });

    it('getShopInstallmentStatus GET devolve int', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/get_shop_installment_status*' => Http::response(['error' => '', 'response' => ['installment_status' => 1]])]);

        expect(paymentExtrasMethods()->getShopInstallmentStatus())->toBe(1);
        paymentExtrasAssertShopCall('GET', '/api/v2/payment/get_shop_installment_status');
    });

    it('setShopInstallmentStatus POST com installment_status no body', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/set_shop_installment_status*' => Http::response(['error' => '', 'response' => ['installment_status' => 0]])]);

        expect(paymentExtrasMethods()->setShopInstallmentStatus(0))->toBe(0);
        paymentExtrasAssertShopCall('POST', '/api/v2/payment/set_shop_installment_status', fn ($req) => str_contains($req->body(), '"installment_status":0'));
    });

    it('getItemInstallmentStatus POST com item_id_list', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/get_item_installment_status*' => Http::response([
            'error' => '', 'response' => ['item_installment_list' => [['item_id' => 1, 'tenure_list' => [3]]]],
        ])]);

        $out = paymentExtrasMethods()->getItemInstallmentStatus([1, 2]);

        expect($out['item_installment_list'][0]['item_id'])->toBe(1);
        paymentExtrasAssertShopCall('POST', '/api/v2/payment/get_item_installment_status', fn ($req) => str_contains($req->body(), '"item_id_list":[1,2]'));
    });

    it('setItemInstallmentStatus POST com item_id_list, tenure_list e participate_plan_ahora', function () {
        Http::fake(['partner.shopeemobile.com/api/v2/payment/set_item_installment_status*' => Http::response(['error' => '', 'response' => ['item_installment_list' => []]])]);

        paymentExtrasMethods()->setItemInstallmentStatus([1], [3, 6], true);

        paymentExtrasAssertShopCall('POST', '/api/v2/payment/set_item_installment_status', fn ($req) => str_contains($req->body(), '"item_id_list":[1]')
            && str_contains($req->body(), '"tenure_list":[3,6]')
            && str_contains($req->body(), '"participate_plan_ahora":true'));
    });
});

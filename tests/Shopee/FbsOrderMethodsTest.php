<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function makeShopeeIntegrationForSdk(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: [
            'partner_id' => 2030136,
            'partner_key' => 'fake-key',
            'shop_id' => 999999,
        ],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
});

/**
 * Cobre os 3 metodos novos FBS adicionados no SDK Shopee:
 *   - generateFbsInvoices  (cria tarefa bulk)
 *   - getFbsInvoicesResult (polling status)
 *   - downloadFbsInvoices  (obtem URLs S3)
 */
describe('MarketPlaces::Shopee()->orders()->generateFbsInvoices', function () {
    it('POST com batch_download body contendo todos parametros', function () {
        Http::fake([
            'partner.shopeemobile.com/api/v2/order/generate_fbs_invoices*' => Http::response([
                'result_list' => [['request_id' => 1001]],
            ]),
        ]);

        $integration = makeShopeeIntegrationForSdk();
        $resp = MarketPlaces::Shopee()->orders($integration)->generateFbsInvoices(
            start: 20260501,
            end: 20260501,
            documentType: 7,
            fileType: 1,
            documentStatus: 1,
        );

        expect($resp->resultList)->toHaveCount(1);

        Http::assertSent(function ($req) {
            $bodyText = $req->body();

            return str_contains($req->url(), '/api/v2/order/generate_fbs_invoices')
                && $req->method() === 'POST'
                && str_contains($bodyText, '"start":20260501')
                && str_contains($bodyText, '"end":20260501')
                && str_contains($bodyText, '"document_type":7')
                && str_contains($bodyText, '"file_type":1')
                && str_contains($bodyText, '"document_status":1');
        });
    });

    it('default args = documentType=7 (All), fileType=1 (XML), status=1 (Authorized)', function () {
        Http::fake([
            'partner.shopeemobile.com/api/v2/order/generate_fbs_invoices*' => Http::response(['result_list' => []]),
        ]);

        $integration = makeShopeeIntegrationForSdk();
        MarketPlaces::Shopee()->orders($integration)->generateFbsInvoices(start: 20260501, end: 20260501);

        Http::assertSent(function ($req) {
            $body = $req->body();

            return str_contains($body, '"document_type":7')
                && str_contains($body, '"file_type":1')
                && str_contains($body, '"document_status":1');
        });
    });
});

describe('MarketPlaces::Shopee()->orders()->getFbsInvoicesResult', function () {
    it('POST com request_id_list nested', function () {
        Http::fake([
            'partner.shopeemobile.com/api/v2/order/get_fbs_invoices_result*' => Http::response([
                'result_list' => [
                    ['request_id' => 1001, 'status' => 'AVAILABLE'],
                    ['request_id' => 1002, 'status' => 'PROCESSING'],
                ],
            ]),
        ]);

        $integration = makeShopeeIntegrationForSdk();
        $resp = MarketPlaces::Shopee()->orders($integration)->getFbsInvoicesResult([1001, 1002]);

        expect($resp->resultList[0]->status)->toBe('AVAILABLE');

        Http::assertSent(function ($req) {
            $body = $req->body();

            return str_contains($req->url(), '/api/v2/order/get_fbs_invoices_result')
                && str_contains($body, '"request_id":[1001,1002]');
        });
    });
});

describe('MarketPlaces::Shopee()->orders()->downloadFbsInvoices', function () {
    it('POST retorna response[] com file_link (formato atual Shopee 2026)', function () {
        Http::fake([
            'partner.shopeemobile.com/api/v2/order/download_fbs_invoices*' => Http::response([
                'response' => [
                    ['request_id' => 1001, 'file_link' => 'https://sfile-origin-br.sp-cdn.../zip?X-Amz-...'],
                ],
            ]),
        ]);

        $integration = makeShopeeIntegrationForSdk();
        $resp = MarketPlaces::Shopee()->orders($integration)->downloadFbsInvoices([1001]);

        // downloadFbsInvoices devolve list<FbsDownloadItem> (o `response` da
        // Shopee E' a propria lista aqui).
        expect($resp)->toHaveCount(1)
            ->and($resp[0]->fileLink)->toContain('sfile-origin-br');
    });
});

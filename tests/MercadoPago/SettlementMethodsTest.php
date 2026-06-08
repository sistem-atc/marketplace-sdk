<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\MercadoPago\Enum\ReportFormat;
use SistemAtc\Marketplaces\MercadoPago\Enum\ReportStatus;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function makeMpIntegrationForSdk(): FakeIntegration
{
    // Token fresco (expired: false) + credenciais nas settings → o guard do
    // TokenRefresher pula o refresh, entao nao precisamos fakear o oauth.
    return new FakeIntegration(
        accessToken: 'APP_USR-fake',
        refreshToken: 'rt-fake',
        settings: [
            'client_id' => 'cli',
            'client_secret' => 'sec',
        ],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config([
        'marketplaces.mercadopago.api_base' => 'https://api.mercadopago.com',
        'marketplaces.mercadopago.oauth_token_url' => 'https://api.mercadopago.com/oauth/token',
        // Sem sleep entre os polls no teste.
        'marketplaces.mercadopago.report_poll_interval' => 0,
        'marketplaces.mercadopago.report_poll_timeout' => 600,
    ]);
});

describe('MarketPlaces::MercadoPago()->settlement()->createReleasedMoneyReport', function () {
    it('faz POST em /v1/account/release_report com Bearer e retorna file_name', function () {
        Http::fake([
            'api.mercadopago.com/v1/account/release_report' => Http::response([
                'file_name' => 'release-report-2026-05.csv',
            ]),
        ]);

        $integration = makeMpIntegrationForSdk();
        $resp = MarketPlaces::MercadoPago()->settlement($integration)
            ->createReleasedMoneyReport('2026-05-01', '2026-05-31');

        expect($resp['file_name'])->toBe('release-report-2026-05.csv');

        Http::assertSent(fn ($req) => str_contains($req->url(), '/v1/account/release_report')
            && $req->method() === 'POST'
            && $req->hasHeader('Authorization', 'Bearer APP_USR-fake')
            && $req['begin_date'] === '2026-05-01T00:00:00Z'
            && $req['end_date'] === '2026-05-31T23:59:59Z'
            && $req['file_format'] === 'csv');
    });

    it('rejeita data em formato invalido', function () {
        Http::fake();

        $integration = makeMpIntegrationForSdk();
        MarketPlaces::MercadoPago()->settlement($integration)
            ->createReleasedMoneyReport('01/05/2026', '2026-05-31');
    })->throws(InvalidArgumentException::class, 'YYYY-MM-DD');
});

describe('MarketPlaces::MercadoPago()->settlement()->reportStatus', function () {
    it('mapeia o status da listagem pro enum ReportStatus', function () {
        Http::fake([
            'api.mercadopago.com/v1/account/settlement_report/list' => Http::response([
                'results' => [
                    ['file_name' => 'other.csv', 'status' => 'processing'],
                    ['file_name' => 'target.csv', 'status' => 'ready'],
                ],
            ]),
        ]);

        $integration = makeMpIntegrationForSdk();
        $status = MarketPlaces::MercadoPago()->settlement($integration)->reportStatus('target.csv');

        expect($status)->toBe(ReportStatus::READY);
    });

    it('retorna EXPIRED quando o report nao aparece na listagem', function () {
        Http::fake([
            'api.mercadopago.com/v1/account/settlement_report/list' => Http::response([
                'results' => [['file_name' => 'other.csv', 'status' => 'ready']],
            ]),
        ]);

        $integration = makeMpIntegrationForSdk();
        $status = MarketPlaces::MercadoPago()->settlement($integration)->reportStatus('missing.csv');

        expect($status)->toBe(ReportStatus::EXPIRED);
    });
});

describe('MarketPlaces::MercadoPago()->settlement()->fetchReleasedMoneyReportNow', function () {
    it('cria + faz polling ate ready + baixa o conteudo', function () {
        Http::fake([
            'api.mercadopago.com/v1/account/release_report' => Http::response([
                'file_name' => 'release-now.csv',
            ]),
            'api.mercadopago.com/v1/account/settlement_report/list' => Http::response([
                'results' => [['file_name' => 'release-now.csv', 'status' => 'ready']],
            ]),
            'api.mercadopago.com/v1/account/settlement_report/release-now.csv' => Http::response(
                "external_reference,net_received_amount\nORD-1,99.90\n",
            ),
        ]);

        $integration = makeMpIntegrationForSdk();
        $result = MarketPlaces::MercadoPago()->settlement($integration)
            ->fetchReleasedMoneyReportNow('2026-05-01', '2026-05-31', ReportFormat::CSV);

        expect($result['file_name'])->toBe('release-now.csv')
            ->and($result['content'])->toContain('net_received_amount')
            ->and($result['content'])->toContain('ORD-1,99.90')
            ->and($result['format'])->toBe(ReportFormat::CSV);
    });
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\MercadoLivre\Enum\BillingDocumentType;
use SistemAtc\Marketplaces\MercadoLivre\Enum\BillingGroup;
use SistemAtc\Marketplaces\MercadoLivre\Enum\BillingReportFormat;
use SistemAtc\Marketplaces\MercadoLivre\Enum\BillingReportStatus;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function mlBillingIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config([
        'marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com',
    ]);
    Http::preventStrayRequests();
});

describe('MarketPlaces::MercadoLivre()->billing()', function () {
    it('details aceita group+documentType enums + fromId/limit (assinatura que PullMercadoLivreBilling usa)', function () {
        Http::fake([
            'api.mercadolibre.com/billing/integration/periods/key/2026-04-01/group/ML/details*' => Http::response([
                'results' => [['id' => 'd1', 'detail_sub_type' => 'CVVML']],
                'last_id' => 'd1',
            ], 200),
        ]);

        $integration = mlBillingIntegration();
        $resp = MarketPlaces::MercadoLivre()->billing($integration)->details(
            periodKey: '2026-04-01',
            group: BillingGroup::ML,
            documentType: BillingDocumentType::BILL,
            fromId: null,
            limit: 1000,
        );

        expect($resp['results'][0]['detail_sub_type'])->toBe('CVVML');
        Http::assertSent(fn ($req) => str_contains($req->url(), '/group/ML/details')
            && str_contains($req->url(), 'document_type=BILL')
            && str_contains($req->url(), 'limit=1000'));
    });

    it('documents existe e bate o endpoint de NFSe', function () {
        Http::fake([
            'api.mercadolibre.com/billing/integration/periods/key/2026-04-01/documents*' => Http::response([
                'results' => [['document_id' => 'NFSE-1', 'files' => [['file_id' => 'f1_pdf']]]],
            ], 200),
        ]);

        $integration = mlBillingIntegration();
        $resp = MarketPlaces::MercadoLivre()->billing($integration)->documents(
            periodKey: '2026-04-01',
            group: BillingGroup::ML,
            documentType: BillingDocumentType::BILL,
            fromId: null,
            limit: 150,
        );

        expect($resp['results'][0]['document_id'])->toBe('NFSE-1');
        Http::assertSent(fn ($req) => str_contains($req->url(), '/periods/key/2026-04-01/documents')
            && str_contains($req->url(), 'group=ML'));
    });

    it('createReport + reportStatus devolve enum BillingReportStatus', function () {
        Http::fake([
            'api.mercadolibre.com/billing/integration/periods/key/2026-04-01/reports' => Http::response(['fileId' => 'rep-1'], 200),
            'api.mercadolibre.com/billing/integration/reports/rep-1/status*' => Http::response(['status' => 'READY'], 200),
        ]);

        $integration = mlBillingIntegration();
        $billing = MarketPlaces::MercadoLivre()->billing($integration);

        $created = $billing->createReport('2026-04-01', BillingGroup::FULL, BillingDocumentType::BILL, BillingReportFormat::XLSX);
        expect($created['fileId'])->toBe('rep-1');

        $status = $billing->reportStatus('rep-1');
        expect($status)->toBe(BillingReportStatus::READY);
    });

    it('details rejeita group que so suporta reports async (FULL)', function () {
        Http::fake();
        $integration = mlBillingIntegration();

        MarketPlaces::MercadoLivre()->billing($integration)->details('2026-04-01', BillingGroup::FULL);
    })->throws(InvalidArgumentException::class, 'reports assincronos');

    it('rejeita period key fora do formato YYYY-MM-01', function () {
        Http::fake();
        $integration = mlBillingIntegration();

        MarketPlaces::MercadoLivre()->billing($integration)->summary('2026-04-15');
    })->throws(InvalidArgumentException::class, 'Period key invalido');
});

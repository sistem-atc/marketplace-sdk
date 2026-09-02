<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Billing\BillingMethods;
use SistemAtc\Marketplaces\MercadoLivre\Enum\BillingDocumentType;
use SistemAtc\Marketplaces\MercadoLivre\Enum\BillingGroup;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function billingProvisionsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function billingProvisionsMethods(): BillingMethods
{
    $integration = billingProvisionsIntegration();

    return new BillingMethods(HttpClientFactory::make($integration), $integration);
}

beforeEach(function () {
    config([
        'marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.access_token_ttl_seconds' => 21600,
        'mercadolivre.default_site_id' => 'MLB',
    ]);
    Http::preventStrayRequests();
});

describe('BillingMethods provisoes por sub-grupo (flex/full/insurtech)', function () {
    it('flexDetails faz GET .../group/ML/flex/details com document_type, limit e from_id', function () {
        Http::fake(['api.mercadolibre.com/billing/integration/periods/key/2023-03-01/group/ML/flex/details*' => Http::response(['offset' => 0, 'limit' => 1, 'results' => [], 'last_id' => 0])]);

        $out = billingProvisionsMethods()->flexDetails('2023-03-01', BillingDocumentType::BILL, fromId: 12345678, limit: 1);

        expect($out)->toHaveKey('results');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && str_starts_with($req->url(), 'https://api.mercadolibre.com/billing/integration/periods/key/2023-03-01/group/ML/flex/details?')
            && str_contains($req->url(), 'document_type=BILL')
            && str_contains($req->url(), 'limit=1')
            && str_contains($req->url(), 'from_id=12345678')
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('fullDetails faz GET .../group/ML/full/details e repassa filtros extras', function () {
        Http::fake(['api.mercadolibre.com/billing/integration/periods/key/2023-03-01/group/ML/full/details*' => Http::response(['results' => []])]);

        billingProvisionsMethods()->fullDetails('2023-03-01', BillingDocumentType::CREDIT_NOTE, extra: ['sort_by' => 'DATE', 'order_by' => 'DESC']);

        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && str_starts_with($req->url(), 'https://api.mercadolibre.com/billing/integration/periods/key/2023-03-01/group/ML/full/details?')
            && str_contains($req->url(), 'document_type=CREDIT_NOTE')
            && str_contains($req->url(), 'sort_by=DATE')
            && str_contains($req->url(), 'order_by=DESC')
            && ! str_contains($req->url(), 'from_id='));
    });

    it('insurtechDetails faz GET .../group/ML/insurtech/details e trava limit em 1000', function () {
        Http::fake(['api.mercadolibre.com/billing/integration/periods/key/2022-10-01/group/ML/insurtech/details*' => Http::response(['results' => []])]);

        billingProvisionsMethods()->insurtechDetails('2022-10-01', limit: 5000);

        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && str_starts_with($req->url(), 'https://api.mercadolibre.com/billing/integration/periods/key/2022-10-01/group/ML/insurtech/details?')
            && str_contains($req->url(), 'limit=1000')
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('sub-grupos validam a period key (YYYY-MM-01)', function () {
        Http::fake();

        expect(fn () => billingProvisionsMethods()->flexDetails('2023-03-15'))->toThrow(InvalidArgumentException::class);
        Http::assertNothingSent();
    });
});

describe('BillingMethods percepcoes (MLA)', function () {
    it('perceptionsSummary faz GET .../periods/key/{key}/perceptions/summary?group=MP', function () {
        Http::fake(['api.mercadolibre.com/billing/integration/periods/key/2021-08-01/perceptions/summary*' => Http::response(['summary' => [['document_id' => 123456789, 'tax_type' => 'CRGI']], 'errors' => []])]);

        $out = billingProvisionsMethods()->perceptionsSummary('2021-08-01', BillingGroup::MP);

        expect($out['summary'][0]['tax_type'])->toBe('CRGI');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/billing/integration/periods/key/2021-08-01/perceptions/summary?group=MP'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('perceptionsSummary sem group nao manda query', function () {
        Http::fake(['api.mercadolibre.com/billing/integration/periods/key/2021-08-01/perceptions/summary' => Http::response(['summary' => []])]);

        billingProvisionsMethods()->perceptionsSummary('2021-08-01');

        Http::assertSent(fn ($req) => $req->url() === 'https://api.mercadolibre.com/billing/integration/periods/key/2021-08-01/perceptions/summary');
    });

    it('perceptionsDetails ML faz GET /group/ML/perceptions/details com document_id + tax_type', function () {
        Http::fake(['api.mercadolibre.com/billing/integration/group/ML/perceptions/details*' => Http::response(['offset' => 1, 'limit' => 2, 'total' => 4241, 'results' => []])]);

        $out = billingProvisionsMethods()->perceptionsDetails(BillingGroup::ML, 333555777, 'CIVA', offset: 1, limit: 2);

        expect($out['total'])->toBe(4241);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && str_starts_with($req->url(), 'https://api.mercadolibre.com/billing/integration/group/ML/perceptions/details?')
            && str_contains($req->url(), 'document_id=333555777')
            && str_contains($req->url(), 'tax_type=CIVA')
            && str_contains($req->url(), 'offset=1')
            && str_contains($req->url(), 'limit=2')
            && ! str_contains($req->url(), 'tax_id=')
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('perceptionsDetails MP exige tax_id e o manda na query', function () {
        Http::fake(['api.mercadolibre.com/billing/integration/group/MP/perceptions/details*' => Http::response(['results' => []])]);

        billingProvisionsMethods()->perceptionsDetails(BillingGroup::MP, 333555777, 'CIVAMP', taxId: 12345);

        Http::assertSent(fn ($req) => str_starts_with($req->url(), 'https://api.mercadolibre.com/billing/integration/group/MP/perceptions/details?')
            && str_contains($req->url(), 'tax_type=CIVAMP')
            && str_contains($req->url(), 'tax_id=12345'));

        expect(fn () => billingProvisionsMethods()->perceptionsDetails(BillingGroup::MP, 1, 'CIVAMP'))->toThrow(InvalidArgumentException::class);
        expect(fn () => billingProvisionsMethods()->perceptionsDetails(BillingGroup::FLEX, 1, 'CIVA'))->toThrow(InvalidArgumentException::class);
    });
});

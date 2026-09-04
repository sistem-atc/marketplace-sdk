<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Replenishment;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function replenishmentIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: [
            'client_id' => 'test-client',
            'client_secret' => 'test-secret',
            'marketplace_id' => 'A2Q3Y263D00KWC',
        ],
        active: true,
        expired: false,
    );
}

function replenishmentEndpoint(): Replenishment
{
    return new Replenishment(new Client(replenishmentIntegration()));
}

function replenishmentAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(fn ($r) => $r->method() === $method
        && $r->url() === $url
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token')
        && ($body === null || $r->data() === $body));
}

const REPL_BASE = 'https://sellingpartnerapi-na.amazon.com/replenishment/2022-11-07';

it('getSellingPartnerMetrics POSTs /sellingPartners/metrics/search with the body', function () {
    Http::fake([REPL_BASE.'/*' => Http::response(['metrics' => [['shippedSubscriptionUnits' => 10]]])]);
    $body = ['aggregationFrequency' => 'MONTH', 'timeInterval' => ['startDate' => '2026-01-01T00:00:00Z', 'endDate' => '2026-02-01T00:00:00Z'], 'marketplaceId' => 'A2Q3Y263D00KWC', 'programTypes' => ['SUBSCRIBE_AND_SAVE']];

    $resp = replenishmentEndpoint()->getSellingPartnerMetrics($body);

    expect($resp['metrics'][0]['shippedSubscriptionUnits'])->toBe(10);
    replenishmentAssertSent('POST', REPL_BASE.'/sellingPartners/metrics/search', $body);
});

it('listOfferMetrics POSTs /offers/metrics/search with the body', function () {
    Http::fake([REPL_BASE.'/*' => Http::response(['offers' => []])]);
    $body = ['pagination' => ['limit' => 50, 'offset' => 0], 'filters' => ['marketplaceId' => 'A2Q3Y263D00KWC', 'programType' => 'SUBSCRIBE_AND_SAVE']];

    replenishmentEndpoint()->listOfferMetrics($body);

    replenishmentAssertSent('POST', REPL_BASE.'/offers/metrics/search', $body);
});

it('listOffers POSTs /offers/search with the body', function () {
    Http::fake([REPL_BASE.'/*' => Http::response(['offers' => [['sku' => 'SKU-A']]])]);
    $body = ['pagination' => ['limit' => 50, 'offset' => 0], 'filters' => ['marketplaceId' => 'A2Q3Y263D00KWC', 'programTypes' => ['SUBSCRIBE_AND_SAVE']]];

    $resp = replenishmentEndpoint()->listOffers($body);

    expect($resp['offers'][0]['sku'])->toBe('SKU-A');
    replenishmentAssertSent('POST', REPL_BASE.'/offers/search', $body);
});

<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\FbaInboundEligibility;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
});

function fbaInboundEligibilityIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );
}

it('getItemEligibilityPreview sends asin + program + marketplaceIds', function () {
    Http::fake(['*' => Http::response(['payload' => ['asin' => 'B0ABC', 'program' => 'INBOUND', 'isEligibleForProgram' => true]])]);

    $api = new FbaInboundEligibility(new Client(fbaInboundEligibilityIntegration()));
    $resp = $api->getItemEligibilityPreview('B0ABC', 'INBOUND', ['marketplaceIds' => 'A2Q3Y263D00KWC']);

    expect($resp['payload']['isEligibleForProgram'])->toBeTrue();
    Http::assertSent(fn (Request $r) => $r->method() === 'GET'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/fba/inbound/v1/eligibility/itemPreview?asin=B0ABC&program=INBOUND&marketplaceIds=A2Q3Y263D00KWC'
        && $r->hasHeader('x-amz-access-token', 'Atza|valid-token'));
});

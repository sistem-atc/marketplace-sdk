<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\ApplicationManagement;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();
    config(['marketplaces.amazon.lwa_token_url' => 'https://api.amazon.com/auth/o2/token']);
});

function applicationManagementEndpoint(): ApplicationManagement
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new ApplicationManagement(new Client($integration));
}

it('rotateApplicationClientSecret POST grantless com scope client_credential:rotation e token de aplicacao', function () {
    Http::fake([
        'https://api.amazon.com/auth/o2/token' => Http::response(['access_token' => 'Atc|grantless-rotation', 'expires_in' => 3600]),
        'https://sellingpartnerapi-na.amazon.com/applications/2023-11-30/clientSecret' => Http::response('', 204),
    ]);

    expect(applicationManagementEndpoint()->rotateApplicationClientSecret())->toBe([]);

    Http::assertSent(fn (Request $r) => $r->url() === 'https://api.amazon.com/auth/o2/token'
        && $r['grant_type'] === 'client_credentials'
        && $r['scope'] === 'sellingpartnerapi::client_credential:rotation'
        && $r['client_id'] === 'test-client'
        && $r['client_secret'] === 'test-secret');

    Http::assertSent(fn (Request $r) => $r->method() === 'POST'
        && $r->url() === 'https://sellingpartnerapi-na.amazon.com/applications/2023-11-30/clientSecret'
        && $r->header('x-amz-access-token')[0] === 'Atc|grantless-rotation');
});

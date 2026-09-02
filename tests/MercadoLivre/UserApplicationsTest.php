<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\User\UserMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function userAppsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function userAppsMethods(): UserMethods
{
    $integration = userAppsIntegration();

    return new UserMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL (todos os pedaços) + Bearer + body/headers da única
 * requisição disparada.
 *
 * @param  array<int,string>  $urlParts
 * @param  array<int,string>  $bodyParts
 * @param  array<string,string>  $headers
 */
function userAppsAssertSent(string $method, array $urlParts, array $bodyParts = [], array $headers = []): void
{
    Http::assertSent(function ($req) use ($method, $urlParts, $bodyParts, $headers) {
        if ($req->method() !== $method || $req->header('Authorization')[0] !== 'Bearer ml-bearer') {
            return false;
        }
        foreach ($urlParts as $part) {
            if (! str_contains($req->url(), $part)) {
                return false;
            }
        }
        foreach ($bodyParts as $part) {
            if (! str_contains($req->body(), $part)) {
                return false;
            }
        }
        foreach ($headers as $name => $value) {
            if (($req->header($name)[0] ?? null) !== $value) {
                return false;
            }
        }

        return true;
    });
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

describe('UserMethods — aplicativo', function () {
    it('applications: GET /users/{id}/applications', function () {
        Http::fake(['api.mercadolibre.com/users/26317316/applications' => Http::response([['app_id' => '13795']])]);

        $result = userAppsMethods()->applications(26317316);

        expect($result[0]['app_id'])->toBe('13795');

        userAppsAssertSent('GET', ['/users/26317316/applications']);
    });

    it('applicationGrants: GET /applications/{id}/grants paginado', function () {
        Http::fake(['api.mercadolibre.com/applications/12345/grants*' => Http::response(['grants' => []])]);

        $result = userAppsMethods()->applicationGrants(12345, 10, 20);

        userAppsAssertSent('GET', ['/applications/12345/grants', 'limit=10', 'offset=20']);
    });

    it('consumedApplications: GET /applications/v1/{id}/consumed-applications com datas', function () {
        Http::fake(['api.mercadolibre.com/applications/v1/12345/consumed-applications*' => Http::response(['ok' => true])]);

        $result = userAppsMethods()->consumedApplications(12345, '2025-08-01', '2025-08-20');

        userAppsAssertSent('GET', ['/applications/v1/12345/consumed-applications', 'date_start=2025-08-01', 'date_end=2025-08-20']);
    });
});

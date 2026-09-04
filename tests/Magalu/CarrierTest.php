<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Carrier\CarrierMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluCarrierIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-carrier',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-carrier'],
        active: true,
        expired: false,
    );
}

function magaluCarrierClient(): CarrierMethods
{
    $integration = magaluCarrierIntegration();

    return new CarrierMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluCarrierSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
{
    Http::assertSent(function (Request $req) use ($method, $url, $query, $body, $headers): bool {
        $parts = parse_url($req->url());
        $actualUrl = $parts['scheme'].'://'.$parts['host'].($parts['path'] ?? '');
        // parse_str() troca '.' por '_' (seller.id → seller_id); parse manual preserva a chave.
        $actualQuery = [];
        foreach (array_filter(explode('&', $parts['query'] ?? '')) as $pair) {
            [$k, $v] = array_pad(explode('=', $pair, 2), 2, '');
            $actualQuery[urldecode($k)] = urldecode($v);
        }

        if ($req->method() !== $method || $actualUrl !== $url) return false;
        if ($actualQuery != $query) return false;
        if (! $req->hasHeader('Authorization', 'Bearer jwt-carrier')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-carrier')) return false;
        foreach ($headers as $k => $v) {
            if (! $req->hasHeader($k, $v)) return false;
        }
        if ($body !== null && json_decode($req->body(), true) !== $body) return false;

        return true;
    });
}

beforeEach(function () {
    config([
        'marketplaces.magalu.api_base' => 'https://api.magalu.com',
        'marketplaces.magalu.services_base' => 'https://services.magalu.com',
        'marketplaces.magalu.token_url' => 'https://autoseg-idp.luizalabs.com/oauth/token',
    ]);
    Http::fake(fn (Request $req) => str_contains($req->url(), '/attachments/')
        ? Http::response('BIN', 200, ['Content-Type' => 'image/png'])
        : Http::response(['ok' => true, 'results' => []]));
});

describe('CarrierMethods', function () {
    it('account: GET services /logistic/carrier/v1/account', function () {
        $resp = magaluCarrierClient()->account();

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCarrierSent('GET', 'https://services.magalu.com/logistic/carrier/v1/account', [], null);
    });

    it('token: GET /logistic/carrier/v1/token', function () {
        $resp = magaluCarrierClient()->token();

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCarrierSent('GET', 'https://services.magalu.com/logistic/carrier/v1/token', [], null);
    });

    it('getShipping: GET /logistic/carrier/v1/shippings/{id}', function () {
        $resp = magaluCarrierClient()->getShipping('s-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCarrierSent('GET', 'https://services.magalu.com/logistic/carrier/v1/shippings/s-1', [], null);
    });

    it('createTracking: POST shippings/{id}/trackings', function () {
        $resp = magaluCarrierClient()->createTracking('s-1', 'DELIVERED', '2026-09-01T10:00:00Z', ['type' => 'HIMSELF', 'name' => 'Jo', 'location' => ['latitude' => -23.5, 'longitude' => -46.6]]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCarrierSent('POST', 'https://services.magalu.com/logistic/carrier/v1/shippings/s-1/trackings', [], ['event' => 'DELIVERED', 'date' => '2026-09-01T10:00:00Z', 'recipient_details' => ['type' => 'HIMSELF', 'name' => 'Jo', 'location' => ['latitude' => -23.5, 'longitude' => -46.6]]]);
    });

    it('createTrackingLocation: POST shippings/{id}/trackings/locations', function () {
        $resp = magaluCarrierClient()->createTrackingLocation('s-1', -23.5, -46.6, '2026-09-01T10:00:00Z');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCarrierSent('POST', 'https://services.magalu.com/logistic/carrier/v1/shippings/s-1/trackings/locations', [], ['coordinates' => ['latitude' => -23.5, 'longitude' => -46.6], 'date' => '2026-09-01T10:00:00Z']);
    });

    it('tracking: GET /logistics/v1/shippings/{id}/tracking (seller)', function () {
        $resp = magaluCarrierClient()->tracking('s-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCarrierSent('GET', 'https://services.magalu.com/logistics/v1/shippings/s-1/tracking', [], null);
    });

    it('generateSmartLabel: POST /smart-label/v1/labels/generate com x-correlation-id', function () {
        $resp = magaluCarrierClient()->generateSmartLabel(['order' => ['id' => 'o-1']], correlationId: 'corr-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluCarrierSent('POST', 'https://services.magalu.com/smart-label/v1/labels/generate', [], ['order' => ['id' => 'o-1']], ['x-correlation-id' => 'corr-1']);
    });
});

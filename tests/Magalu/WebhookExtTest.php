<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Webhooks\WebhookMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluWebhookExtIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-webhookext',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-webhookext'],
        active: true,
        expired: false,
    );
}

function magaluWebhookExtClient(): WebhookMethods
{
    $integration = magaluWebhookExtIntegration();

    return new WebhookMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluWebhookExtSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-webhookext')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-webhookext')) return false;
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

describe('WebhookMethods', function () {
    it('listV0: GET /v0/onboarding/signup', function () {
        $resp = magaluWebhookExtClient()->listV0(limit: 10, offset: 0);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluWebhookExtSent('GET', 'https://api.magalu.com/v0/onboarding/signup', ['_limit' => '10', '_offset' => '0'], null);
    });

    it('signupHistoryV0: PUT /v0/onboarding/signup/history sem webhook', function () {
        $resp = magaluWebhookExtClient()->signupHistoryV0('order.created', ['channel' => 'x']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluWebhookExtSent('PUT', 'https://api.magalu.com/v0/onboarding/signup/history', [], ['topic_id' => 'order.created', 'filter_by' => ['channel' => 'x']]);
    });

    it('signoffV0: DELETE /v0/onboarding/signup/{id}', function () {
        $resp = magaluWebhookExtClient()->signoffV0('sub-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluWebhookExtSent('DELETE', 'https://api.magalu.com/v0/onboarding/signup/sub-1', [], null);
    });

    it('queuesHistory: GET /v0/queues/history com filtros', function () {
        $resp = magaluWebhookExtClient()->queuesHistory(['type' => 'order.created', 'request_id' => 'r1']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluWebhookExtSent('GET', 'https://api.magalu.com/v0/queues/history', ['type' => 'order.created', 'request_id' => 'r1', '_limit' => '50', '_offset' => '0'], null);
    });

    it('listTraces: GET /v0/traces com filtros', function () {
        $resp = magaluWebhookExtClient()->listTraces(['code' => 'sku.updated', 'severity' => 'error'], limit: 10);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluWebhookExtSent('GET', 'https://api.magalu.com/v0/traces', ['code' => 'sku.updated', 'severity' => 'error', '_limit' => '10', '_offset' => '0'], null);
    });

    it('getTrace: GET /v0/traces/{id}', function () {
        $resp = magaluWebhookExtClient()->getTrace('tr-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluWebhookExtSent('GET', 'https://api.magalu.com/v0/traces/tr-1', [], null);
    });
});

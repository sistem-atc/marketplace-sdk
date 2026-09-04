<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Conversation\QuestionMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluQuestionIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-question',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-question'],
        active: true,
        expired: false,
    );
}

function magaluQuestionClient(): QuestionMethods
{
    $integration = magaluQuestionIntegration();

    return new QuestionMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluQuestionSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-question')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-question')) return false;
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

describe('QuestionMethods', function () {
    it('list: GET services /v0/questions', function () {
        $resp = magaluQuestionClient()->list(['status' => 'pending'], limit: 10, offset: 0);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluQuestionSent('GET', 'https://services.magalu.com/v0/questions', ['status' => 'pending', '_limit' => '10', '_offset' => '0'], null);
    });

    it('get: GET /v0/questions/{id}', function () {
        $resp = magaluQuestionClient()->get('q-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluQuestionSent('GET', 'https://services.magalu.com/v0/questions/q-1', [], null);
    });

    it('answer: POST /v0/questions/{id}/answer', function () {
        $resp = magaluQuestionClient()->answer('q-1', 'Temos', 'u1', 'Ana', externalId: 'ext-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluQuestionSent('POST', 'https://services.magalu.com/v0/questions/q-1/answer', [], ['message' => 'Temos', 'owner' => ['external_id' => 'u1', 'name' => 'Ana'], 'external_id' => 'ext-1']);
    });
});

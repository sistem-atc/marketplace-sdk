<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Conversation\ConversationMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluConversationIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-conversation',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-conversation'],
        active: true,
        expired: false,
    );
}

function magaluConversationClient(): ConversationMethods
{
    $integration = magaluConversationIntegration();

    return new ConversationMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluConversationSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-conversation')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-conversation')) return false;
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

describe('ConversationMethods', function () {
    it('list: GET services /v0/conversations', function () {
        $resp = magaluConversationClient()->list(['status' => 'OPENED'], limit: 20, offset: 0);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluConversationSent('GET', 'https://services.magalu.com/v0/conversations', ['status' => 'OPENED', '_limit' => '20', '_offset' => '0'], null);
    });

    it('get: GET /v0/conversations/{id}', function () {
        $resp = magaluConversationClient()->get('c-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluConversationSent('GET', 'https://services.magalu.com/v0/conversations/c-1', [], null);
    });

    it('listMessages: GET /v0/conversations/{id}/messages', function () {
        $resp = magaluConversationClient()->listMessages('c-1', limit: 5, offset: 5);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluConversationSent('GET', 'https://services.magalu.com/v0/conversations/c-1/messages', ['_limit' => '5', '_offset' => '5'], null);
    });

    it('getMessage: GET /v0/conversations/{id}/messages/{mid}', function () {
        $resp = magaluConversationClient()->getMessage('c-1', 'm-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluConversationSent('GET', 'https://services.magalu.com/v0/conversations/c-1/messages/m-1', [], null);
    });

    it('sendMessage: POST /v0/conversations/{id}/messages', function () {
        $resp = magaluConversationClient()->sendMessage('c-1', 'Oi', 'u1', 'Ana', externalId: 'ext-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluConversationSent('POST', 'https://services.magalu.com/v0/conversations/c-1/messages', [], ['content' => 'Oi', 'owner' => ['external_id' => 'u1', 'name' => 'Ana'], 'external_id' => 'ext-1']);
    });

    it('markAsRead: PATCH /v0/conversations/{id}/read_by', function () {
        $resp = magaluConversationClient()->markAsRead('c-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluConversationSent('PATCH', 'https://services.magalu.com/v0/conversations/c-1/read_by', [], null);
    });
});

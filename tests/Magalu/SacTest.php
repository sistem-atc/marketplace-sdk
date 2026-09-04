<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Sac\SacMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluSacIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-sac',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-sac'],
        active: true,
        expired: false,
    );
}

function magaluSacClient(): SacMethods
{
    $integration = magaluSacIntegration();

    return new SacMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluSacSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-sac')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-sac')) return false;
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

describe('SacMethods', function () {
    it('listTickets: GET /seller/v0/tickets com filtros', function () {
        $resp = magaluSacClient()->listTickets(['status' => 'open', 'order__code' => 'ORD-1'], limit: 10, offset: 5, sort: 'created_at:desc');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSacSent('GET', 'https://api.magalu.com/seller/v0/tickets', ['status' => 'open', 'order__code' => 'ORD-1', '_limit' => '10', '_offset' => '5', '_sort' => 'created_at:desc'], null);
    });

    it('createTicket: POST /seller/v0/tickets', function () {
        $resp = magaluSacClient()->createTicket(['type' => 'cancellation', 'order' => ['code' => 'ORD-1']]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSacSent('POST', 'https://api.magalu.com/seller/v0/tickets', [], ['type' => 'cancellation', 'order' => ['code' => 'ORD-1']]);
    });

    it('getTicket: GET /seller/v0/tickets/{id}', function () {
        $resp = magaluSacClient()->getTicket('t-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSacSent('GET', 'https://api.magalu.com/seller/v0/tickets/t-1', [], null);
    });

    it('listEvents: GET tickets/{id}/events', function () {
        $resp = magaluSacClient()->listEvents('t-1', ['type' => 'product_sent']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSacSent('GET', 'https://api.magalu.com/seller/v0/tickets/t-1/events', ['type' => 'product_sent', '_limit' => '50', '_offset' => '0'], null);
    });

    it('createEvent: POST tickets/{id}/events', function () {
        $resp = magaluSacClient()->createEvent('t-1', 'product_sent', code: 'erp-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSacSent('POST', 'https://api.magalu.com/seller/v0/tickets/t-1/events', [], ['type' => 'product_sent', 'code' => 'erp-1']);
    });

    it('listMessages: GET tickets/{id}/messages', function () {
        $resp = magaluSacClient()->listMessages('t-1', ['has_attachment' => 'true']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSacSent('GET', 'https://api.magalu.com/seller/v0/tickets/t-1/messages', ['has_attachment' => 'true', '_limit' => '50', '_offset' => '0'], null);
    });

    it('createMessage: POST tickets/{id}/messages (JSON)', function () {
        $resp = magaluSacClient()->createMessage('t-1', 'Ola', 'customer', 'u1', 'Ana', code: 'msg-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSacSent('POST', 'https://api.magalu.com/seller/v0/tickets/t-1/messages', [], ['message' => 'Ola', 'destination' => 'customer', 'owner' => ['code' => 'u1', 'name' => 'Ana'], 'code' => 'msg-1']);
    });

    it('getMessage: GET tickets/{id}/messages/{mid}', function () {
        $resp = magaluSacClient()->getMessage('t-1', 'm-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSacSent('GET', 'https://api.magalu.com/seller/v0/tickets/t-1/messages/m-1', [], null);
    });

    it('listReturns: GET tickets/{id}/returns', function () {
        $resp = magaluSacClient()->listReturns('t-1', ['reverse_code' => 'RV1']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSacSent('GET', 'https://api.magalu.com/seller/v0/tickets/t-1/returns', ['reverse_code' => 'RV1', '_limit' => '50', '_offset' => '0'], null);
    });

    it('createReturn: POST tickets/{id}/returns', function () {
        $resp = magaluSacClient()->createReturn('t-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSacSent('POST', 'https://api.magalu.com/seller/v0/tickets/t-1/returns', [], null);
    });

    it('getReturn: GET tickets/{id}/returns/{rid}', function () {
        $resp = magaluSacClient()->getReturn('t-1', 'r-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSacSent('GET', 'https://api.magalu.com/seller/v0/tickets/t-1/returns/r-1', [], null);
    });

    it('listTransactions: GET /seller/v0/transactions com datas obrigatorias', function () {
        $resp = magaluSacClient()->listTransactions('2026-01-01T00:00:00Z', '2026-01-31T00:00:00Z', status: 'completed');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSacSent('GET', 'https://api.magalu.com/seller/v0/transactions', ['created_at_gte' => '2026-01-01T00:00:00Z', 'created_at_lte' => '2026-01-31T00:00:00Z', 'status' => 'completed', '_limit' => '50', '_offset' => '0'], null);
    });

    it('getTransaction: GET /seller/v0/transactions/{id}', function () {
        $resp = magaluSacClient()->getTransaction('tx-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluSacSent('GET', 'https://api.magalu.com/seller/v0/transactions/tx-1', [], null);
    });

    it('createMessageWithFile: POST /seller/v1/tickets/{id}/messages multipart com file + campos', function () {
        $resp = magaluSacClient()->createMessageWithFile('t-1', 'Segue', 'customer', 'u1', 'Ana', 'PDFBYTES', 'nota.pdf', code: 'msg-2');

        expect($resp)->toHaveKey('ok');
        Http::assertSent(fn (Request $req) => $req->method() === 'POST'
            && $req->url() === 'https://api.magalu.com/seller/v1/tickets/t-1/messages'
            && $req->hasHeader('Authorization', 'Bearer jwt-sac')
            && $req->hasHeader('X-Tenant-Id', 'tenant-sac')
            && $req->isMultipart()
            && $req->hasFile('file', 'PDFBYTES', 'nota.pdf')
            && $req->hasFile('message', 'Segue')
            && $req->hasFile('destination', 'customer')
            && $req->hasFile('owner_code', 'u1')
            && $req->hasFile('owner_name', 'Ana')
            && $req->hasFile('code', 'msg-2'));
    });

    it('downloadAttachment: GET attachments/{id} devolve body cru', function () {
        $body = magaluSacClient()->downloadAttachment('t-1', 'm-1', 'a-1');

        expect($body)->toBe('BIN');
        magaluSacSent('GET', 'https://api.magalu.com/seller/v0/tickets/t-1/messages/m-1/attachments/a-1');
    });
});

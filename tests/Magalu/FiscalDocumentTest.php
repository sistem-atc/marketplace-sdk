<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Fulfillment\FiscalDocumentMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluFiscalDocumentIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-fiscaldocument',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-fiscaldocument'],
        active: true,
        expired: false,
    );
}

function magaluFiscalDocumentClient(): FiscalDocumentMethods
{
    $integration = magaluFiscalDocumentIntegration();

    return new FiscalDocumentMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluFiscalDocumentSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-fiscaldocument')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-fiscaldocument')) return false;
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

describe('FiscalDocumentMethods', function () {
    it('getByAccessKey: GET services /logistics/v1/fiscal-documents/{key} com format e flags', function () {
        $resp = magaluFiscalDocumentClient()->getByAccessKey('35260900000000000000550010000000011000000011', format: 'pdf', options: ['include_canceled' => 'true']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalDocumentSent('GET', 'https://services.magalu.com/logistics/v1/fiscal-documents/35260900000000000000550010000000011000000011', ['format' => 'pdf', 'include_canceled' => 'true'], null);
    });

    it('requestBatchDownload: POST fiscal-documents/batch-download', function () {
        $resp = magaluFiscalDocumentClient()->requestBatchDownload(['date_range' => ['initial' => '2026-08-01', 'end' => '2026-08-31']], zipContents: 'PDF');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalDocumentSent('POST', 'https://services.magalu.com/logistics/v1/fiscal-documents/batch-download', [], ['zip_contents' => 'PDF', 'filters' => ['date_range' => ['initial' => '2026-08-01', 'end' => '2026-08-31']]]);
    });

    it('getBatchExport: GET fiscal-documents/batch-export/{task_id}', function () {
        $resp = magaluFiscalDocumentClient()->getBatchExport('task-1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalDocumentSent('GET', 'https://services.magalu.com/logistics/v1/fiscal-documents/batch-export/task-1', [], null);
    });
});

<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Invoice\InvoiceMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluInvoiceExtIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-invoiceext',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-invoiceext'],
        active: true,
        expired: false,
    );
}

function magaluInvoiceExtClient(): InvoiceMethods
{
    $integration = magaluInvoiceExtIntegration();

    return new InvoiceMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluInvoiceExtSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-invoiceext')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-invoiceext')) return false;
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

describe('InvoiceMethods', function () {
    it('listForDeliveryRaw: GET deliveries/{id}/invoices paginado', function () {
        $resp = magaluInvoiceExtClient()->listForDeliveryRaw('d-1', limit: 5, offset: 0, sort: 'created_at:desc');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluInvoiceExtSent('GET', 'https://api.magalu.com/seller/v1/deliveries/d-1/invoices', ['_limit' => '5', '_offset' => '0', '_sort' => 'created_at:desc'], null);
    });

    it('createForDelivery: POST deliveries/{id}/invoices', function () {
        $resp = magaluInvoiceExtClient()->createForDelivery('d-1', ['key' => '352609', 'xml' => 'PD94', 'amount' => 10.5, 'issued_at' => '2026-09-01T10:00:00Z', 'channel' => ['id' => 'ch-1']]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluInvoiceExtSent('POST', 'https://api.magalu.com/seller/v1/deliveries/d-1/invoices', [], ['key' => '352609', 'xml' => 'PD94', 'amount' => 10.5, 'issued_at' => '2026-09-01T10:00:00Z', 'channel' => ['id' => 'ch-1']]);
    });

    it('updateForDelivery: PUT deliveries/{id}/invoices/{key}', function () {
        $resp = magaluInvoiceExtClient()->updateForDelivery('d-1', '352609', ['amount' => 11, 'issued_at' => '2026-09-01T10:00:00Z', 'xml' => 'PD94', 'channel' => ['id' => 'ch-1']]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluInvoiceExtSent('PUT', 'https://api.magalu.com/seller/v1/deliveries/d-1/invoices/352609', [], ['amount' => 11, 'issued_at' => '2026-09-01T10:00:00Z', 'xml' => 'PD94', 'channel' => ['id' => 'ch-1']]);
    });
});

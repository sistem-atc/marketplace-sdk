<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\FinancialAnalysis\FinancialAnalysisMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluFinancialAnalysisIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-financialanalysis',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-financialanalysis'],
        active: true,
        expired: false,
    );
}

function magaluFinancialAnalysisClient(): FinancialAnalysisMethods
{
    $integration = magaluFinancialAnalysisIntegration();

    return new FinancialAnalysisMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluFinancialAnalysisSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-financialanalysis')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-financialanalysis')) return false;
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

describe('FinancialAnalysisMethods', function () {
    it('listOrders: GET financial-analysis/orders com filtros e cursor', function () {
        $resp = magaluFinancialAnalysisClient()->listOrders(['purchased_at__gte' => '2026-01-01T00:00:00Z', '_paginate' => 'cursor'], limit: 100);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFinancialAnalysisSent('GET', 'https://api.magalu.com/seller/v1/financial-analysis/orders', ['purchased_at__gte' => '2026-01-01T00:00:00Z', '_paginate' => 'cursor', '_limit' => '100', '_offset' => '0'], null);
    });

    it('getOrder: GET financial-analysis/orders/{id}', function () {
        $resp = magaluFinancialAnalysisClient()->getOrder('132847080471234');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFinancialAnalysisSent('GET', 'https://api.magalu.com/seller/v1/financial-analysis/orders/132847080471234', [], null);
    });

    it('listTransactions: GET financial-analysis/transactions', function () {
        $resp = magaluFinancialAnalysisClient()->listTransactions(['created_at__gte' => '2026-01-01T00:00:00Z', 'created_at__lte' => '2026-01-31T23:59:59Z']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFinancialAnalysisSent('GET', 'https://api.magalu.com/seller/v1/financial-analysis/transactions', ['created_at__gte' => '2026-01-01T00:00:00Z', 'created_at__lte' => '2026-01-31T23:59:59Z', '_limit' => '50', '_offset' => '0'], null);
    });

    it('listTransactionsByBatch: GET transactions/{batch_id}', function () {
        $resp = magaluFinancialAnalysisClient()->listTransactionsByBatch('batch-1', limit: 10, offset: 20, sort: 'created_at:desc');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFinancialAnalysisSent('GET', 'https://api.magalu.com/seller/v1/financial-analysis/transactions/batch-1', ['_limit' => '10', '_offset' => '20', '_sort' => 'created_at:desc'], null);
    });
});

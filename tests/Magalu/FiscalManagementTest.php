<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Fulfillment\FiscalManagementMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function magaluFiscalManagementIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'jwt-fiscalmanagement',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec', 'tenant_id' => 'tenant-fiscalmanagement'],
        active: true,
        expired: false,
    );
}

function magaluFiscalManagementClient(): FiscalManagementMethods
{
    $integration = magaluFiscalManagementIntegration();

    return new FiscalManagementMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo, URL completa (path + query parseada), Bearer, X-Tenant-Id e body JSON.
 *
 * @param array<string, mixed> $query
 * @param array<mixed>|null $body null = nao confere body
 */
function magaluFiscalManagementSent(string $method, string $url, array $query = [], ?array $body = null, array $headers = []): void
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
        if (! $req->hasHeader('Authorization', 'Bearer jwt-fiscalmanagement')) return false;
        if (! $req->hasHeader('X-Tenant-Id', 'tenant-fiscalmanagement')) return false;
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

describe('FiscalManagementMethods', function () {
    it('createStateInscriptions: POST companies/state-inscriptions', function () {
        $resp = magaluFiscalManagementClient()->createStateInscriptions([['state' => 'SP', 'inscription' => '123', 'gnre' => false]]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('POST', 'https://services.magalu.com/logistics/fiscal-management/v1/companies/state-inscriptions', [], ['inscriptions' => [['state' => 'SP', 'inscription' => '123', 'gnre' => false]]]);
    });

    it('updateStateInscriptions: PUT companies/state-inscriptions', function () {
        $resp = magaluFiscalManagementClient()->updateStateInscriptions([['state' => 'SP', 'gnre' => true]]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('PUT', 'https://services.magalu.com/logistics/fiscal-management/v1/companies/state-inscriptions', [], ['inscriptions' => [['state' => 'SP', 'gnre' => true]]]);
    });

    it('listCompanyParameters: GET parameters/company com type__in', function () {
        $resp = magaluFiscalManagementClient()->listCompanyParameters([1, 2], limit: 10);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('GET', 'https://services.magalu.com/logistics/fiscal-management/v1/parameters/company', ['_limit' => '10', '_offset' => '0', 'type__in' => '1,2'], null);
    });

    it('createCompanyParameters: POST parameters/company', function () {
        $resp = magaluFiscalManagementClient()->createCompanyParameters([['type' => 1, 'value' => 'x']]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('POST', 'https://services.magalu.com/logistics/fiscal-management/v1/parameters/company', [], ['parameters' => [['type' => 1, 'value' => 'x']]]);
    });

    it('updateCompanyParameters: PUT parameters/company', function () {
        $resp = magaluFiscalManagementClient()->updateCompanyParameters([['type' => 1, 'value' => 'y']]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('PUT', 'https://services.magalu.com/logistics/fiscal-management/v1/parameters/company', [], ['parameters' => [['type' => 1, 'value' => 'y']]]);
    });

    it('listProducts: GET products com filtros', function () {
        $resp = magaluFiscalManagementClient()->listProducts(['sku__in' => 'A,B'], limit: 10, offset: 5);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('GET', 'https://services.magalu.com/logistics/fiscal-management/v1/products', ['sku__in' => 'A,B', '_limit' => '10', '_offset' => '5'], null);
    });

    it('getProduct: GET products/{id}', function () {
        $resp = magaluFiscalManagementClient()->getProduct('SKU 1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('GET', 'https://services.magalu.com/logistics/fiscal-management/v1/products/SKU%201', [], null);
    });

    it('updateProduct: PUT products/{id}', function () {
        $resp = magaluFiscalManagementClient()->updateProduct('A', ['ncm' => '12345678', 'origin' => 0]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('PUT', 'https://services.magalu.com/logistics/fiscal-management/v1/products/A', [], ['ncm' => '12345678', 'origin' => 0]);
    });

    it('updateProductsBatch: PATCH products/batch com lista', function () {
        $resp = magaluFiscalManagementClient()->updateProductsBatch([['id' => 'A', 'ncm' => '1'], ['id' => 'B', 'ncm' => '2']]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('PATCH', 'https://services.magalu.com/logistics/fiscal-management/v1/products/batch', [], [['id' => 'A', 'ncm' => '1'], ['id' => 'B', 'ncm' => '2']]);
    });

    it('listKits: GET kits com seller.id obrigatorio', function () {
        $resp = magaluFiscalManagementClient()->listKits('seller-1', ['sku__in' => 'K1']);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('GET', 'https://services.magalu.com/logistics/fiscal-management/v1/kits', ['sku__in' => 'K1', 'seller.id' => 'seller-1', '_limit' => '50', '_offset' => '0'], null);
    });

    it('createKit: POST kits/{id}', function () {
        $resp = magaluFiscalManagementClient()->createKit('K1', [['sku' => 'A', 'quantity' => 2]]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('POST', 'https://services.magalu.com/logistics/fiscal-management/v1/kits/K1', [], ['compositions' => [['sku' => 'A', 'quantity' => 2]]]);
    });

    it('updateKit: PUT kits/{id}', function () {
        $resp = magaluFiscalManagementClient()->updateKit('K1', [['sku' => 'A', 'quantity' => 3]]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('PUT', 'https://services.magalu.com/logistics/fiscal-management/v1/kits/K1', [], ['compositions' => [['sku' => 'A', 'quantity' => 3]]]);
    });

    it('deleteKit: DELETE kits/{id}', function () {
        $resp = magaluFiscalManagementClient()->deleteKit('K1');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('DELETE', 'https://services.magalu.com/logistics/fiscal-management/v1/kits/K1', [], null);
    });

    it('listTaxDepartments: GET tax-departments', function () {
        $resp = magaluFiscalManagementClient()->listTaxDepartments(limit: 10, offset: 0);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('GET', 'https://services.magalu.com/logistics/fiscal-management/v1/tax-departments', ['_limit' => '10', '_offset' => '0'], null);
    });

    it('getTaxDepartment: GET tax-departments/{id}', function () {
        $resp = magaluFiscalManagementClient()->getTaxDepartment('7');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('GET', 'https://services.magalu.com/logistics/fiscal-management/v1/tax-departments/7', [], null);
    });

    it('createTaxDepartment: POST tax-departments', function () {
        $resp = magaluFiscalManagementClient()->createTaxDepartment('Suplementos', [['customer_type' => 'B2C']]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('POST', 'https://services.magalu.com/logistics/fiscal-management/v1/tax-departments', [], ['tax_department' => ['description' => 'Suplementos'], 'tax_rules' => [['customer_type' => 'B2C']]]);
    });

    it('createTaxDepartmentsBatch: POST tax-departments/batch', function () {
        $resp = magaluFiscalManagementClient()->createTaxDepartmentsBatch([['tax_department' => ['description' => 'X'], 'tax_rules' => []]]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('POST', 'https://services.magalu.com/logistics/fiscal-management/v1/tax-departments/batch', [], ['tax_departments' => [['tax_department' => ['description' => 'X'], 'tax_rules' => []]]]);
    });

    it('renameTaxDepartment: PATCH tax-departments/{id}', function () {
        $resp = magaluFiscalManagementClient()->renameTaxDepartment('7', 'Novo');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('PATCH', 'https://services.magalu.com/logistics/fiscal-management/v1/tax-departments/7', [], ['description' => 'Novo']);
    });

    it('deleteTaxDepartment: DELETE tax-departments/{id}', function () {
        $resp = magaluFiscalManagementClient()->deleteTaxDepartment('7');

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('DELETE', 'https://services.magalu.com/logistics/fiscal-management/v1/tax-departments/7', [], null);
    });

    it('updateFederalTaxes: PUT taxes/department/{dep}/types/{type}', function () {
        $resp = magaluFiscalManagementClient()->updateFederalTaxes('7', '1', [['type' => 'PIS', 'aliquot' => 165, 'currency' => 'BRL', 'normalizer' => 100]]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('PUT', 'https://services.magalu.com/logistics/fiscal-management/v1/taxes/department/7/types/1', [], ['data' => [['type' => 'PIS', 'aliquot' => 165, 'currency' => 'BRL', 'normalizer' => 100]]]);
    });

    it('updateInterstateTaxes: PATCH taxes/.../interstate/{id}', function () {
        $resp = magaluFiscalManagementClient()->updateInterstateTaxes('7', '1', '35', [['type' => 'ICMS', 'aliquot' => 1800, 'currency' => 'BRL', 'normalizer' => 100]]);

        expect($resp)->toBeArray()->toHaveKey('ok');
        magaluFiscalManagementSent('PATCH', 'https://services.magalu.com/logistics/fiscal-management/v1/taxes/department/7/types/1/interstate/35', [], ['data' => [['type' => 'ICMS', 'aliquot' => 1800, 'currency' => 'BRL', 'normalizer' => 100]]]);
    });
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Invoice\InvoiceMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function invoiceStateRegIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function invoiceStateRegMethods(): InvoiceMethods
{
    $integration = invoiceStateRegIntegration();

    return new InvoiceMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL (todos os pedaços) + Bearer + body/headers da única
 * requisição disparada.
 *
 * @param  array<int,string>  $urlParts
 * @param  array<int,string>  $bodyParts
 * @param  array<string,string>  $headers
 */
function invoiceStateRegAssertSent(string $method, array $urlParts, array $bodyParts = [], array $headers = []): void
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

describe('InvoiceMethods — inscrições estaduais', function () {
    it('stateRegistries: GET .../state_registry/{cnpj}', function () {
        Http::fake(['api.mercadolibre.com/users/77/invoices/state_registry/52640544000111' => Http::response([['state' => 'sp']])]);

        $result = invoiceStateRegMethods()->stateRegistries(77, '52640544000111');

        expect($result[0]['state'])->toBe('sp');
        Http::assertNotSent(fn ($req) => str_ends_with($req->url(), '/sp'));

        invoiceStateRegAssertSent('GET', ['/users/77/invoices/state_registry/52640544000111']);
    });

    it('stateRegistry: GET .../{cnpj}/{state} com UF em minúsculo', function () {
        Http::fake(['api.mercadolibre.com/users/77/invoices/state_registry/52640544000111/mg' => Http::response(['ok' => true])]);

        $result = invoiceStateRegMethods()->stateRegistry(77, '52640544000111', 'MG');

        invoiceStateRegAssertSent('GET', ['/users/77/invoices/state_registry/52640544000111/mg']);
    });

    it('createStateRegistry: POST state_registry + gnre_enable', function () {
        Http::fake(['api.mercadolibre.com/users/77/invoices/state_registry/52640544000111/mg' => Http::response(['state' => 'mg'])]);

        $result = invoiceStateRegMethods()->createStateRegistry(77, '52640544000111', 'mg', '1262819934920', true);

        invoiceStateRegAssertSent('POST', ['/users/77/invoices/state_registry/52640544000111/mg'], ['"state_registry":"1262819934920"', '"gnre_enable":true']);
    });

    it('updateStateRegistry: PUT', function () {
        Http::fake(['api.mercadolibre.com/users/77/invoices/state_registry/52640544000111/mg' => Http::response(['ok' => true])]);

        $result = invoiceStateRegMethods()->updateStateRegistry(77, '52640544000111', 'mg', '1713072179209');

        invoiceStateRegAssertSent('PUT', ['/users/77/invoices/state_registry/52640544000111/mg'], ['"state_registry":"1713072179209"', '"gnre_enable":false']);
    });

    it('batchStateRegistries: PUT .../{cnpj}/batch com lista', function () {
        Http::fake(['api.mercadolibre.com/users/77/invoices/state_registry/52640544000111/batch' => Http::response([])]);

        $result = invoiceStateRegMethods()->batchStateRegistries(77, '52640544000111', [['state' => 'sp', 'state_registry' => '097817832671', 'gnre_enable' => false], ['state' => 'rj', 'state_registry' => '700219151993', 'gnre_enable' => true]]);

        invoiceStateRegAssertSent('PUT', ['/users/77/invoices/state_registry/52640544000111/batch'], ['[{"state":"sp"', '"state":"rj"']);
    });

    it('deleteStateRegistry: DELETE', function () {
        Http::fake(['api.mercadolibre.com/users/77/invoices/state_registry/52640544000111/rj' => Http::response([])]);

        $result = invoiceStateRegMethods()->deleteStateRegistry(77, '52640544000111', 'rj');

        invoiceStateRegAssertSent('DELETE', ['/users/77/invoices/state_registry/52640544000111/rj']);
    });
});

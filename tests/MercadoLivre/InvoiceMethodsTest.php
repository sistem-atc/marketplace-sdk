<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function mlInvoicesIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config([
        'marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.access_token_ttl_seconds' => 21600,
        'mercadolivre.default_site_id' => 'MLB',
    ]);
});

describe('MarketPlaces::MercadoLivre()->invoices()->batchDownloadInvoicesZip', function () {
    it('chama endpoint correto com Bearer + filtros sale/return/full/others', function () {
        Http::fake([
            'api.mercadolibre.com/users/64196652/invoices/sites/MLB/batch_request/period/stream*' => Http::response("PK\x03\x04zip-fake-bytes"),
        ]);

        $integration = mlInvoicesIntegration();
        $body = MarketPlaces::MercadoLivre()->invoices($integration)->batchDownloadInvoicesZip(
            sellerId: 64196652,
            startDate: '2026-05-01',
            endDate: '2026-05-01',
            options: [
                'sale' => 'all',
                'return' => 'all',
                'full' => 'all',
                'others' => 'none',
            ]
        );

        expect($body)->toContain('PK');

        Http::assertSent(fn ($req) => str_contains($req->url(), '/users/64196652/invoices/sites/MLB/batch_request/period/stream')
            && str_contains($req->url(), 'start=20260501')
            && str_contains($req->url(), 'end=20260501')
            && str_contains($req->url(), 'sale=all')
            && str_contains($req->url(), 'full=all')
            && str_contains($req->url(), 'others=none')
            && str_contains($req->url(), 'file_types=xml')
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('throws MercadoLivreRequestException em rate limit 429', function () {
        Http::fake([
            'api.mercadolibre.com/users/*/invoices/sites/MLB/batch_request/period/stream*' => Http::response('', 429),
        ]);

        $integration = mlInvoicesIntegration();
        MarketPlaces::MercadoLivre()->invoices($integration)->batchDownloadInvoicesZip(
            sellerId: 1, startDate: '2026-05-01', endDate: '2026-05-01',
        );
    })->throws(MercadoLivreRequestException::class);
});

describe('MarketPlaces::MercadoLivre()->user()->me', function () {
    it('faz GET /users/me com Bearer', function () {
        Http::fake([
            'api.mercadolibre.com/users/me' => Http::response([
                'id' => 64196652,
                'nickname' => 'SOLDIERS NUTRITION',
                'site_id' => 'MLB',
            ]),
        ]);

        $integration = mlInvoicesIntegration();
        $me = MarketPlaces::MercadoLivre()->user($integration)->me();

        // me() devolve UserResponseDTO (v2.5.36).
        expect($me->id)->toBe(64196652)
            ->and($me->nickname)->toBe('SOLDIERS NUTRITION');
    });
});

describe('MarketPlaces::MercadoLivre()->orders()->search', function () {
    it('aceita sort=date_desc (corrigido — antes era date_created_desc invalido)', function () {
        Http::fake([
            'api.mercadolibre.com/orders/search*' => Http::response([
                'results' => [],
                'paging' => ['total' => 0],
            ]),
        ]);

        $integration = mlInvoicesIntegration();
        MarketPlaces::MercadoLivre()->orders($integration)->search(sellerId: 64196652, sort: 'date_desc', limit: 5);

        Http::assertSent(fn ($req) => str_contains($req->url(), 'sort=date_desc')
            && str_contains($req->url(), 'seller=64196652'));
    });
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Netshoes\Exceptions\NetshoesAuthenticationException;
use SistemAtc\Marketplaces\Netshoes\Exceptions\NetshoesRequestException;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    Http::preventStrayRequests();

    config([
        'marketplaces.netshoes.api_base' => 'https://api-marketplace.netshoes.com.br',
        'marketplaces.netshoes.sandbox_base' => 'http://api-sandbox.netshoes.com.br',
    ]);
});

function netshoesIntegration(array $settings = [], string $accessToken = 'tok-real'): FakeIntegration
{
    return new FakeIntegration(
        accessToken: $accessToken,
        refreshToken: null,
        settings: array_merge(['client_id' => 'cli-123'], $settings),
        active: true,
        expired: false,
    );
}

describe('Netshoes auth (2 headers + content-type)', function () {
    it('manda client_id + access_token + content-type em toda request', function () {
        Http::fake([
            'api-marketplace.netshoes.com.br/api/v2/orders/*' => Http::response([
                'orderNumber' => '141352269',
                'status' => 'INVOICED',
            ]),
        ]);

        MarketPlaces::Netshoes()->orders(netshoesIntegration())->getOrder('141352269');

        Http::assertSent(fn ($req) => $req->hasHeader('client_id', 'cli-123')
            && $req->hasHeader('access_token', 'tok-real')
            && $req->hasHeader('content-type', 'application/json')
            && str_contains($req->url(), '/api/v2/orders/141352269'));
    });

    it('usa PROD https por padrao', function () {
        Http::fake(['api-marketplace.netshoes.com.br/*' => Http::response(['orderNumber' => '1'])]);

        MarketPlaces::Netshoes()->orders(netshoesIntegration())->getOrder('1');

        Http::assertSent(fn ($req) => str_starts_with($req->url(), 'https://api-marketplace.netshoes.com.br'));
    });

    it('usa HOMOLOG http quando environment=sandbox', function () {
        Http::fake(['api-sandbox.netshoes.com.br/*' => Http::response(['orderNumber' => '1'])]);

        MarketPlaces::Netshoes()
            ->orders(netshoesIntegration(['environment' => 'sandbox']))
            ->getOrder('1');

        Http::assertSent(fn ($req) => str_starts_with($req->url(), 'http://api-sandbox.netshoes.com.br'));
    });

    it('base_url explicito no settings vence o default', function () {
        Http::fake(['custom.netshoes.test/*' => Http::response(['orderNumber' => '1'])]);

        MarketPlaces::Netshoes()
            ->orders(netshoesIntegration(['base_url' => 'https://custom.netshoes.test']))
            ->getOrder('1');

        Http::assertSent(fn ($req) => str_starts_with($req->url(), 'https://custom.netshoes.test'));
    });

    it('lanca NetshoesAuthenticationException sem client_id', function () {
        Http::fake();

        MarketPlaces::Netshoes()
            ->orders(new FakeIntegration(accessToken: 'tok', settings: []))
            ->getOrder('1');
    })->throws(NetshoesAuthenticationException::class);

    it('lanca NetshoesAuthenticationException sem access_token', function () {
        Http::fake();

        MarketPlaces::Netshoes()
            ->orders(new FakeIntegration(accessToken: null, settings: ['client_id' => 'cli']))
            ->getOrder('1');
    })->throws(NetshoesAuthenticationException::class);
});

describe('getOrder', function () {
    it('usa V2 e expande items,shippings por padrao', function () {
        Http::fake([
            'api-marketplace.netshoes.com.br/api/v2/orders/*' => Http::response([
                'orderNumber' => '141352269',
            ]),
        ]);

        $order = MarketPlaces::Netshoes()->orders(netshoesIntegration())->getOrder('141352269');

        expect($order['orderNumber'])->toBe('141352269');

        Http::assertSent(fn ($req) => str_contains($req->url(), '/api/v2/orders/141352269')
            && str_contains(urldecode($req->url()), 'expand=items,shippings'));
    });

    it('aceita expand custom', function () {
        Http::fake(['api-marketplace.netshoes.com.br/*' => Http::response(['orderNumber' => '1'])]);

        MarketPlaces::Netshoes()->orders(netshoesIntegration())->getOrder('1', ['items']);

        Http::assertSent(fn ($req) => str_contains(urldecode($req->url()), 'expand=items')
            && ! str_contains(urldecode($req->url()), 'shippings'));
    });

    it('propaga 403 Access Denied (escopo Orders ausente) como NetshoesRequestException', function () {
        Http::fake([
            'api-marketplace.netshoes.com.br/*' => Http::response(['message' => 'Access Denied'], 403),
        ]);

        try {
            MarketPlaces::Netshoes()->orders(netshoesIntegration())->getOrder('1');
            $this->fail('Deveria ter lancado NetshoesRequestException');
        } catch (NetshoesRequestException $e) {
            expect($e->status())->toBe(403)
                ->and($e->getMessage())->toContain('Access Denied');
        }
    });
});

describe('getOrders (paginacao page/size)', function () {
    it('manda page/size e limita size a 50', function () {
        Http::fake([
            'api-marketplace.netshoes.com.br/api/v2/orders*' => Http::response([
                'content' => [['orderNumber' => 'A'], ['orderNumber' => 'B']],
            ]),
        ]);

        MarketPlaces::Netshoes()->orders(netshoesIntegration())->getOrders(['page' => 2, 'size' => 999]);

        Http::assertSent(fn ($req) => str_contains($req->url(), 'page=2')
            && str_contains($req->url(), 'size=50'));
    });

    it('getAllOrders pagina ate vir pagina parcial e achata a lista', function () {
        Http::fake([
            'api-marketplace.netshoes.com.br/api/v2/orders*' => Http::sequence()
                ->push(['content' => array_fill(0, 50, ['orderNumber' => 'X'])])
                ->push(['content' => [['orderNumber' => 'last']]]),
        ]);

        $all = MarketPlaces::Netshoes()->orders(netshoesIntegration())->getAllOrders(['size' => 50]);

        expect($all)->toHaveCount(51)
            ->and(end($all)['orderNumber'])->toBe('last');
    });

    it('getAllOrders para em pagina vazia', function () {
        Http::fake([
            'api-marketplace.netshoes.com.br/api/v2/orders*' => Http::response(['content' => []]),
        ]);

        $all = MarketPlaces::Netshoes()->orders(netshoesIntegration())->getAllOrders();

        expect($all)->toBe([]);
    });
});

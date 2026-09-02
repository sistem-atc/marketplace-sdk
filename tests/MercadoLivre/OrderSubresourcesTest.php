<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function orderSubresourcesIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function orderSubresourcesMethods(): OrderMethods
{
    $integration = orderSubresourcesIntegration();

    return new OrderMethods(HttpClientFactory::make($integration), $integration);
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

describe('OrderMethods sub-recursos de /orders/{id}', function () {
    it('discounts faz GET /orders/{id}/discounts com Bearer', function () {
        Http::fake(['api.mercadolibre.com/orders/2000003508419013/discounts' => Http::response(['details' => [['type' => 'coupon']]])]);

        $out = orderSubresourcesMethods()->discounts(2000003508419013);

        expect($out['details'][0]['type'])->toBe('coupon');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/orders/2000003508419013/discounts'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('product faz GET /orders/{id}/product', function () {
        Http::fake(['api.mercadolibre.com/orders/1/product' => Http::response(['attributes' => [['name' => 'IMEI', 'value' => '111']]])]);

        $out = orderSubresourcesMethods()->product(1);

        expect($out['attributes'][0]['name'])->toBe('IMEI');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/orders/1/product'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('shipments manda X-New-Domain e list_all=true, devolvendo a lista', function () {
        Http::fake(['api.mercadolibre.com/orders/2000014428837134/shipments*' => Http::response([
            ['id' => 1, 'type' => 'forward'],
            ['id' => 2, 'type' => 'return'],
        ])]);

        $out = orderSubresourcesMethods()->shipments(2000014428837134, listAll: true, apiVersion: '2');

        expect($out)->toHaveCount(2)->and($out[1]['type'])->toBe('return');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && str_starts_with($req->url(), 'https://api.mercadolibre.com/orders/2000014428837134/shipments?')
            && str_contains($req->url(), 'list_all=true')
            && $req->hasHeader('X-New-Domain', 'true')
            && $req->hasHeader('X-Api-Version', '2')
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('shipments sem flags nao manda query nem X-Api-Version', function () {
        Http::fake(['api.mercadolibre.com/orders/5/shipments' => Http::response(['id' => 9, 'type' => 'forward'])]);

        $out = orderSubresourcesMethods()->shipments(5);

        expect($out['id'])->toBe(9);
        Http::assertSent(fn ($req) => $req->url() === 'https://api.mercadolibre.com/orders/5/shipments'
            && $req->hasHeader('X-New-Domain', 'true')
            && ! $req->hasHeader('X-Api-Version'));
    });
});

describe('OrderMethods notas internas', function () {
    it('notes faz GET /orders/{id}/notes', function () {
        Http::fake(['api.mercadolibre.com/orders/10/notes' => Http::response([['id' => 'n1', 'note' => 'test']])]);

        orderSubresourcesMethods()->notes(10);

        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/orders/10/notes'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('createNote faz POST com body {note}', function () {
        Http::fake(['api.mercadolibre.com/orders/10/notes' => Http::response(['id' => 'n1', 'note' => 'test'])]);

        orderSubresourcesMethods()->createNote(10, 'test');

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/orders/10/notes'
            && $req->data() === ['note' => 'test']
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('updateNote faz PUT /orders/{id}/notes/{noteId} com body {note}', function () {
        Http::fake(['api.mercadolibre.com/orders/10/notes/abc' => Http::response(['id' => 'abc', 'note' => 'test2'])]);

        orderSubresourcesMethods()->updateNote(10, 'abc', 'test2');

        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === 'https://api.mercadolibre.com/orders/10/notes/abc'
            && $req->data() === ['note' => 'test2']);
    });

    it('deleteNote faz DELETE /orders/{id}/notes/{noteId} e devolve [] em corpo vazio', function () {
        Http::fake(['api.mercadolibre.com/orders/10/notes/abc' => Http::response('', 200)]);

        $out = orderSubresourcesMethods()->deleteNote(10, 'abc');

        expect($out)->toBe([]);
        Http::assertSent(fn ($req) => $req->method() === 'DELETE'
            && $req->url() === 'https://api.mercadolibre.com/orders/10/notes/abc'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });
});

describe('OrderMethods feedback e blacklist', function () {
    it('feedback faz GET /orders/{id}/feedback e devolve sale + purchase', function () {
        Http::fake(['api.mercadolibre.com/orders/77/feedback' => Http::response([
            'sale' => ['id' => 5040068160032, 'role' => 'seller'],
            'purchase' => ['id' => 5040068164512, 'role' => 'buyer'],
        ])]);

        $out = orderSubresourcesMethods()->feedback(77);

        expect($out['sale']['id'])->toBe(5040068160032);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/orders/77/feedback'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('createFeedback faz POST /orders/{id}/feedback com o body inteiro', function () {
        Http::fake(['api.mercadolibre.com/orders/77/feedback' => Http::response(['id' => 1])]);

        $body = ['fulfilled' => false, 'rating' => 'neutral', 'message' => 'Operation not completed', 'reason' => 'OUT_OF_STOCK', 'restock_item' => false];
        orderSubresourcesMethods()->createFeedback(77, $body);

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/orders/77/feedback'
            && $req->data() === $body);
    });

    it('updateFeedback faz PUT /feedback/{id}', function () {
        Http::fake(['api.mercadolibre.com/feedback/9041207884458' => Http::response(['id' => 9041207884458])]);

        orderSubresourcesMethods()->updateFeedback(9041207884458, ['fulfilled' => true, 'rating' => 'positive', 'message' => 'ok']);

        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === 'https://api.mercadolibre.com/feedback/9041207884458'
            && $req->data()['rating'] === 'positive');
    });

    it('replyFeedback faz POST /feedback/{id}/reply com {reply}', function () {
        Http::fake(['api.mercadolibre.com/feedback/9041207884458/reply' => Http::response(['id' => 9041207884458, 'reply' => 'Obrigado'])]);

        orderSubresourcesMethods()->replyFeedback(9041207884458, 'Obrigado');

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/feedback/9041207884458/reply'
            && $req->data() === ['reply' => 'Obrigado']
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('blacklistBuyer faz POST /users/{seller}/order_blacklist com {user_id}', function () {
        Http::fake(['api.mercadolibre.com/users/64196652/order_blacklist' => Http::response(['user_id' => 123])]);

        orderSubresourcesMethods()->blacklistBuyer(64196652, 123);

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/users/64196652/order_blacklist'
            && $req->data() === ['user_id' => 123]);
    });

    it('currencyConversion faz GET /currency_conversions/search?from&to', function () {
        Http::fake(['api.mercadolibre.com/currency_conversions/search*' => Http::response(['ratio' => 0.0704988])]);

        $out = orderSubresourcesMethods()->currencyConversion('ARS', 'BRL');

        expect($out['ratio'])->toBe(0.0704988);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && str_contains($req->url(), '/currency_conversions/search?')
            && str_contains($req->url(), 'from=ARS')
            && str_contains($req->url(), 'to=BRL')
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });
});

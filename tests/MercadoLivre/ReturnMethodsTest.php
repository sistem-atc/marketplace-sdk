<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Returns\ReturnMethods;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function returnMethodsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function returnMethodsMethods(): ReturnMethods
{
    $integration = returnMethodsIntegration();

    return new ReturnMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL (todos os pedaços) + Bearer + body/headers da única
 * requisição disparada.
 *
 * @param  array<int,string>  $urlParts
 * @param  array<int,string>  $bodyParts
 * @param  array<string,string>  $headers
 */
function returnMethodsAssertSent(string $method, array $urlParts, array $bodyParts = [], array $headers = []): void
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

describe('ReturnMethods', function () {
    it('get: GET /post-purchase/v2/claims/{id}/returns', function () {
        Http::fake(['api.mercadolibre.com/post-purchase/v2/claims/500/returns' => Http::response(['id' => 57341011])]);

        $result = returnMethodsMethods()->get(500);

        expect($result['id'])->toBe(57341011);

        returnMethodsAssertSent('GET', ['/post-purchase/v2/claims/500/returns']);
    });

    it('reviews: GET /post-purchase/v1/returns/{id}/reviews', function () {
        Http::fake(['api.mercadolibre.com/post-purchase/v1/returns/546/reviews' => Http::response(['ok' => true])]);

        $result = returnMethodsMethods()->reviews(546);

        returnMethodsAssertSent('GET', ['/post-purchase/v1/returns/546/reviews']);
    });

    it('reasons: GET /post-purchase/v1/returns/reasons?flow&claim_id', function () {
        Http::fake(['api.mercadolibre.com/post-purchase/v1/returns/reasons*' => Http::response([])]);

        $result = returnMethodsMethods()->reasons(555);

        returnMethodsAssertSent('GET', ['/post-purchase/v1/returns/reasons', 'flow=seller_return_failed', 'claim_id=555']);
    });

    it('uploadAttachment: POST multipart em claims/{id}/returns/attachments', function () {
        Http::fake(['api.mercadolibre.com/post-purchase/v1/claims/500/returns/attachments' => Http::response(['user_id' => 1, 'file_name' => 'a.png'])]);

        $result = returnMethodsMethods()->uploadAttachment(500, 'png-bytes', 'a.png');

        expect($result['file_name'])->toBe('a.png');

        returnMethodsAssertSent('POST', ['/post-purchase/v1/claims/500/returns/attachments'], ['a.png', 'png-bytes']);
    });

    it('createReview: POST body vazio = produto OK', function () {
        Http::fake(['api.mercadolibre.com/post-purchase/v1/returns/546/return-review' => Http::response([])]);

        $result = returnMethodsMethods()->createReview(546);

        returnMethodsAssertSent('POST', ['/post-purchase/v1/returns/546/return-review']);
    });

    it('createReview: POST lista de falhas', function () {
        Http::fake(['api.mercadolibre.com/post-purchase/v1/returns/546/return-review' => Http::response([])]);

        $result = returnMethodsMethods()->createReview(546, [['reason' => 'SRF2', 'message' => 'Danificado', 'attachments' => ['a.png']]]);

        returnMethodsAssertSent('POST', ['/post-purchase/v1/returns/546/return-review'], ['[{"reason":"SRF2"', '"attachments":["a.png"]']);
    });

    it('returnCost: GET sem calculate_amount_usd por padrão', function () {
        Http::fake(['api.mercadolibre.com/post-purchase/v1/claims/500/charges/return-cost' => Http::response(['currency_id' => 'BRL', 'amount' => 42.9])]);

        $result = returnMethodsMethods()->returnCost(500);

        Http::assertNotSent(fn ($req) => str_contains($req->url(), 'calculate_amount_usd'));

        returnMethodsAssertSent('GET', ['/post-purchase/v1/claims/500/charges/return-cost']);
    });

    it('returnCost: GET com calculate_amount_usd=true', function () {
        Http::fake(['api.mercadolibre.com/post-purchase/v1/claims/500/charges/return-cost*' => Http::response(['ok' => true])]);

        $result = returnMethodsMethods()->returnCost(500, true);

        returnMethodsAssertSent('GET', ['/post-purchase/v1/claims/500/charges/return-cost', 'calculate_amount_usd=true']);
    });

    it('changes: GET /post-purchase/v1/claims/{id}/changes', function () {
        Http::fake(['api.mercadolibre.com/post-purchase/v1/claims/500/changes' => Http::response(['ok' => true])]);

        $result = returnMethodsMethods()->changes(500);

        returnMethodsAssertSent('GET', ['/post-purchase/v1/claims/500/changes']);
    });

    it('allowReplace: POST expected-resolutions/allow-replace', function () {
        Http::fake(['api.mercadolibre.com/post-purchase/v1/claims/500/expected-resolutions/allow-replace' => Http::response([])]);

        $result = returnMethodsMethods()->allowReplace(500);

        returnMethodsAssertSent('POST', ['/post-purchase/v1/claims/500/expected-resolutions/allow-replace']);
    });

    it('uploadAttachment: 404 vira MercadoLivreRequestException', function () {
        Http::fake(['api.mercadolibre.com/post-purchase/v1/claims/500/returns/attachments' => Http::response(['error' => 'not_found_error'], 404)]);

        returnMethodsMethods()->uploadAttachment(500, 'x', 'a.png');
    })->throws(MercadoLivreRequestException::class);
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Question\QuestionMethods;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Question\QuestionResponseDTO;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function questionExtrasIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function questionExtrasMethods(): QuestionMethods
{
    $integration = questionExtrasIntegration();

    return new QuestionMethods(HttpClientFactory::make($integration), $integration);
}

/**
 * Confere verbo + URL (todos os pedaços) + Bearer + body/headers da única
 * requisição disparada.
 *
 * @param  array<int,string>  $urlParts
 * @param  array<int,string>  $bodyParts
 * @param  array<string,string>  $headers
 */
function questionExtrasAssertSent(string $method, array $urlParts, array $bodyParts = [], array $headers = []): void
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

describe('QuestionMethods — extras', function () {
    it('create: POST /questions com text + item_id', function () {
        Http::fake(['api.mercadolibre.com/questions' => Http::response(['id' => 123, 'text' => 'Tem em vermelho?', 'status' => 'UNANSWERED', 'item_id' => 'MLB1'])]);

        $result = questionExtrasMethods()->create('MLB1', 'Tem em vermelho?');

        expect($result)->toBeInstanceOf(QuestionResponseDTO::class);

        questionExtrasAssertSent('POST', ['/questions'], ['"text":"Tem em vermelho?"', '"item_id":"MLB1"']);
    });

    it('responseTime: GET /users/{id}/questions/response_time', function () {
        Http::fake(['api.mercadolibre.com/users/77/questions/response_time' => Http::response(['user_id' => 77, 'total' => ['response_time' => 22]])]);

        $result = questionExtrasMethods()->responseTime(77);

        expect($result['total']['response_time'])->toBe(22);

        questionExtrasAssertSent('GET', ['/users/77/questions/response_time']);
    });
});

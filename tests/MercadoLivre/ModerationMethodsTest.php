<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Moderation\ModerationMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function moderationMethodsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function moderationMethods(): ModerationMethods
{
    $integration = moderationMethodsIntegration();

    return new ModerationMethods(HttpClientFactory::make($integration), $integration);
}

beforeEach(function () {
    config([
        'marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.access_token_ttl_seconds' => 21600,
        'mercadolivre.default_site_id' => 'MLB',
    ]);
    Http::preventStrayRequests();
    Http::fake(['api.mercadolibre.com/*' => Http::response(['ok' => true], 200)]);
});

it('lastModeration monta a referência {element_id}-{element_type}', function () {
    moderationMethods()->lastModeration('MLB123');
    Http::assertSent(fn ($req) => $req->method() === 'GET'
        && str_ends_with($req->url(), '/moderations/last_moderation/MLB123-ITM')
        && $req->header('Authorization')[0] === 'Bearer ml-bearer');
});

it('lastModeration aceita a referência já montada', function () {
    moderationMethods()->lastModeration('MLB123-REV');
    Http::assertSent(fn ($req) => str_ends_with($req->url(), '/moderations/last_moderation/MLB123-REV'));
});

it('infractions GET /moderations/infractions/{user} com filtros', function () {
    moderationMethods()->infractions(288230000, ['date_created_since' => '2023-09-01', 'limit' => 2]);
    Http::assertSent(fn ($req) => $req->method() === 'GET'
        && str_contains($req->url(), '/moderations/infractions/288230000?')
        && str_contains($req->url(), 'date_created_since=2023-09-01')
        && str_contains($req->url(), 'limit=2')
        && $req->header('Authorization')[0] === 'Bearer ml-bearer');
});

it('pictureDiagnostic POST /moderations/pictures/diagnostic com picture_url + context', function () {
    moderationMethods()->pictureDiagnostic('https://x/img.jpg', 'MLB1346', 'Titulo', 'thumbnail');
    Http::assertSent(fn ($req) => $req->method() === 'POST'
        && str_ends_with($req->url(), '/moderations/pictures/diagnostic')
        && $req->header('Authorization')[0] === 'Bearer ml-bearer'
        && $req->body() === '{"picture_url":"https:\/\/x\/img.jpg","context":{"category_id":"MLB1346","title":"Titulo","picture_type":"thumbnail"}}');
});

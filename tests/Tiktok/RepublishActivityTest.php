<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tests\Support\RecursiveFieldCoverage;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion\RepublishActivityResponseDTO;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttRepubFixture(): array
{
    return json_decode(file_get_contents(__DIR__.'/../Fixtures/Tiktok/republish-activity.json'), true);
}

function ttRepubPromotion()
{
    return MarketPlaces::Tiktok()->promotion(new FakeIntegration(
        accessToken: 'access',
        refreshToken: 'rt',
        settings: ['app_key' => 'ak', 'app_secret' => 'as', 'shop_cipher' => 'cipher1'],
        active: true,
        expired: false,
    ));
}

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function () {
    $payload = ttRepubFixture();
    $perdidos = RecursiveFieldCoverage::missing(
        $payload,
        RepublishActivityResponseDTO::fromArray($payload)->toArray(),
    );

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
});

it('faz roundtrip lossless do payload inteiro', function () {
    $payload = ttRepubFixture();

    expect(RepublishActivityResponseDTO::fromArray($payload)->toArray())->toEqual($payload);
});

it('o activity_id devolvido e o da campanha NOVA, nao o do path', function () {
    Http::fake([
        '*/promotion/202607/activities/7657162458772768530/republish*' => Http::response([
            'code' => 0,
            'message' => 'Success',
            'data' => ttRepubFixture(),
        ]),
    ]);

    $dto = ttRepubPromotion()->republishActivity(
        '7657162458772768530',
        beginTime: 1782875064,
        endTime: 1782895064,
    );

    // Republicar CRIA outra activity — guardar o id antigo e' monitorar campanha morta.
    expect($dto->activityId)->toBe('7136104329798256386')
        ->and($dto->activityId)->not->toBe('7657162458772768530');
});

it('usa POST na versao propria 202607 com as datas em epoch no CORPO', function () {
    Http::fake(['*republish*' => Http::response(['code' => 0, 'data' => ttRepubFixture()])]);

    ttRepubPromotion()->republishActivity('7657162458772768530', beginTime: 1782875064, endTime: 1782895064);

    Http::assertSent(fn ($r) => $r->method() === 'POST'
        && str_contains($r->url(), '/promotion/202607/activities/7657162458772768530/republish')
        && ! str_contains($r->url(), '/promotion/202309/')
        && str_contains($r->body(), '"begin_time":1782875064')
        && str_contains($r->body(), '"end_time":1782895064'));
});

it('sem janela informada o corpo vai VAZIO (a API reaproveita a da origem)', function () {
    Http::fake(['*republish*' => Http::response(['code' => 0, 'data' => ttRepubFixture()])]);

    ttRepubPromotion()->republishActivity('7657162458772768530');

    Http::assertSent(fn ($r) => ! str_contains($r->body(), 'begin_time')
        && ! str_contains($r->body(), 'end_time'));
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Audiences\AudiencesMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function adsAudiencesMethods(): AudiencesMethods
{
    $integration = new FakeIntegration(
        accessToken: 'ads-access-token',
        refreshToken: 'ads-refresh',
        settings: [],
        active: true,
        expired: false,
    );

    return new AudiencesMethods($integration, 'client-abc');
}

function adsAudiencesAuthOk(\Illuminate\Http\Client\Request $req, ?string $scope): bool
{
    $base = $req->header('Amazon-Advertising-API-ClientId')[0] === 'client-abc'
        && $req->header('Authorization')[0] === 'Bearer ads-access-token';

    return $scope === null
        ? $base && ! $req->hasHeader('Amazon-Advertising-API-Scope')
        : $base && $req->header('Amazon-Advertising-API-Scope')[0] === $scope;
}

beforeEach(fn () => Http::preventStrayRequests());

describe('Ads AudiencesMethods — discovery', function () {
    it('listAudiences: POST /audiences/list com query de paginação, body e AccountId', function () {
        Http::fake(['advertising-api.amazon.com/audiences/list*' => Http::response(['audiences' => [['audienceId' => 'a1']], 'nextToken' => 'n'])]);

        $out = adsAudiencesMethods()->listAudiences('111', ['adType' => 'DSP'], ['maxResults' => 10, 'nextToken' => 'x'], accountId: 'dsp-1');

        expect($out['audiences'][0]['audienceId'])->toBe('a1');
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/audiences/list?maxResults=10&nextToken=x'
            && $req->hasHeader('Content-Type', 'application/json')
            && $req->header('Amazon-Ads-AccountId')[0] === 'dsp-1'
            && $req['adType'] === 'DSP'
            && adsAudiencesAuthOk($req, '111'));
    });

    it('fetchTaxonomy: POST /audiences/taxonomy/list', function () {
        Http::fake(['advertising-api.amazon.com/audiences/taxonomy/list*' => Http::response(['categories' => []])]);

        adsAudiencesMethods()->fetchTaxonomy('111', ['adType' => 'SD', 'categoryPath' => ['In-market']], ['maxResults' => 5]);

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/audiences/taxonomy/list?maxResults=5'
            && $req->hasHeader('Content-Type', 'application/json')
            && ! $req->hasHeader('Amazon-Ads-AccountId')
            && $req['categoryPath'] === ['In-market']
            && adsAudiencesAuthOk($req, '111'));
    });
});

describe('Ads AudiencesMethods — DSP', function () {
    it('dspAudienceDelete: POST /dsp/audiences/delete com AdvertiserId + media type dspaudiences.v1', function () {
        Http::fake(['advertising-api.amazon.com/dsp/audiences/delete' => Http::response(['success' => []])]);

        adsAudiencesMethods()->dspAudienceDelete('111', 'adv-7', [['audienceId' => 'a1', 'idempotencyKey' => 'k1']]);

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/dsp/audiences/delete'
            && $req->hasHeader('Content-Type', 'application/vnd.dspaudiences.v1+json')
            && $req->hasHeader('Accept', 'application/vnd.dspaudiences.v1+json')
            && $req->header('AdvertiserId')[0] === 'adv-7'
            && $req['dspAudienceDeleteRequestItems'][0]['audienceId'] === 'a1'
            && adsAudiencesAuthOk($req, '111'));
    });

    it('dspAudienceEdit: PUT /dsp/audiences/edit', function () {
        Http::fake(['advertising-api.amazon.com/dsp/audiences/edit' => Http::response(['success' => []])]);

        adsAudiencesMethods()->dspAudienceEdit('111', 'adv-7', [['audienceId' => 'a1', 'audienceType' => 'PRODUCT_VIEWS', 'idempotencyKey' => 'k1', 'name' => 'Novo']]);

        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === 'https://advertising-api.amazon.com/dsp/audiences/edit'
            && $req->hasHeader('Content-Type', 'application/vnd.dspaudiences.v1+json')
            && $req->header('AdvertiserId')[0] === 'adv-7'
            && $req['dspAudienceEditRequestItems'][0]['name'] === 'Novo'
            && adsAudiencesAuthOk($req, '111'));
    });
});

describe('Ads AudiencesMethods — data provider (v2/dp)', function () {
    it('createAudienceMetadata: POST /v2/dp/audiencemetadata/ sem Scope', function () {
        Http::fake(['advertising-api.amazon.com/v2/dp/audiencemetadata/' => Http::response(['audienceId' => 99])]);

        $out = adsAudiencesMethods()->createAudienceMetadata(['name' => 'Aud', 'description' => 'd', 'advertiserId' => 1, 'metadata' => ['type' => 'DATA_PROVIDER']]);

        expect($out['audienceId'])->toBe(99);
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/v2/dp/audiencemetadata/'
            && $req->hasHeader('Content-Type', 'application/json')
            && $req['name'] === 'Aud'
            && adsAudiencesAuthOk($req, null));
    });

    it('updateAudienceMetadata / getAudienceMetadata: /v2/dp/audiencemetadata/{id}', function () {
        Http::fake(['advertising-api.amazon.com/v2/dp/audiencemetadata/99' => Http::response(['audienceId' => 99, 'description' => 'nova'])]);

        adsAudiencesMethods()->updateAudienceMetadata('99', ['description' => 'nova']);
        expect(adsAudiencesMethods()->getAudienceMetadata('99')['description'])->toBe('nova');

        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === 'https://advertising-api.amazon.com/v2/dp/audiencemetadata/99'
            && $req->hasHeader('Content-Type', 'application/json')
            && $req['description'] === 'nova'
            && adsAudiencesAuthOk($req, null));
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://advertising-api.amazon.com/v2/dp/audiencemetadata/99'
            && adsAudiencesAuthOk($req, null));
    });

    it('patchAudienceRecords: PATCH /v2/dp/audience embrulhando em patches', function () {
        Http::fake(['advertising-api.amazon.com/v2/dp/audience' => Http::response(['jobRequestId' => 'j1'])]);

        adsAudiencesMethods()->patchAudienceRecords([['op' => 'add', 'path' => '/99', 'value' => [['hashedPII' => ['email' => 'h']]]]]);

        Http::assertSent(fn ($req) => $req->method() === 'PATCH'
            && $req->url() === 'https://advertising-api.amazon.com/v2/dp/audience'
            && $req->hasHeader('Content-Type', 'application/json')
            && $req['patches'][0]['op'] === 'add'
            && adsAudiencesAuthOk($req, null));
    });

    it('patchUsers: PATCH /v2/dp/users', function () {
        Http::fake(['advertising-api.amazon.com/v2/dp/users' => Http::response(['jobRequestId' => 'j2'])]);

        adsAudiencesMethods()->patchUsers(['users' => [['userId' => ['hashedEmail' => 'h']]], 'operation' => 'DELETE']);

        Http::assertSent(fn ($req) => $req->method() === 'PATCH'
            && $req->url() === 'https://advertising-api.amazon.com/v2/dp/users'
            && $req->hasHeader('Content-Type', 'application/json')
            && $req['operation'] === 'DELETE'
            && adsAudiencesAuthOk($req, null));
    });
});

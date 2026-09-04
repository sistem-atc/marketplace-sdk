<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Creatives\CreativeAssetsMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function adsCreativeAssetsMethods(): CreativeAssetsMethods
{
    $integration = new FakeIntegration(
        accessToken: 'ads-access-token',
        refreshToken: 'ads-refresh',
        settings: [],
        active: true,
        expired: false,
    );

    return new CreativeAssetsMethods($integration, 'client-abc');
}

function adsCreativeAssetsAuthOk(\Illuminate\Http\Client\Request $req, string $scope): bool
{
    return $req->header('Amazon-Advertising-API-ClientId')[0] === 'client-abc'
        && $req->header('Authorization')[0] === 'Bearer ads-access-token'
        && $req->header('Amazon-Advertising-API-Scope')[0] === $scope;
}

beforeEach(fn () => Http::preventStrayRequests());

describe('Ads CreativeAssetsMethods', function () {
    it('getAsset: GET /assets?assetId=…&version=… com Accept v3', function () {
        Http::fake(['advertising-api.amazon.com/assets*' => Http::response(['assetId' => 'as-1', 'versions' => []])]);

        expect(adsCreativeAssetsMethods()->getAsset('111', 'as-1', 'v2')['assetId'])->toBe('as-1');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://advertising-api.amazon.com/assets?assetId=as-1&version=v2'
            && $req->hasHeader('Accept', 'application/vnd.creativeassetsgetresponse.v3+json')
            && adsCreativeAssetsAuthOk($req, '111'));
    });

    it('getAssetsBatchRegister: GET /assets/batchRegister/{requestId}', function () {
        Http::fake(['advertising-api.amazon.com/assets/batchRegister/req-1' => Http::response(['requestId' => 'req-1', 'status' => 'COMPLETED'])]);

        expect(adsCreativeAssetsMethods()->getAssetsBatchRegister('111', 'req-1')['status'])->toBe('COMPLETED');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://advertising-api.amazon.com/assets/batchRegister/req-1'
            && $req->hasHeader('Accept', 'application/vnd.assetsbatchregisterresponse.v1+json')
            && adsCreativeAssetsAuthOk($req, '111'));
    });

    it('assetsBatchRegister: POST /assets/batchRegister com media type v1', function () {
        Http::fake(['advertising-api.amazon.com/assets/batchRegister' => Http::response(['requestId' => 'req-2'])]);

        $out = adsCreativeAssetsMethods()->assetsBatchRegister('111', ['assetDetailsList' => [['url' => 'https://up/1', 'name' => 'a.png', 'assetType' => 'IMAGE']]]);

        expect($out['requestId'])->toBe('req-2');
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/assets/batchRegister'
            && $req->hasHeader('Content-Type', 'application/vnd.assetsbatchregisterrequest.v1+json')
            && $req->hasHeader('Accept', 'application/vnd.assetsbatchregisterrequest.v1+json')
            && $req['assetDetailsList'][0]['name'] === 'a.png'
            && adsCreativeAssetsAuthOk($req, '111'));
    });

    it('registerAsset: POST /assets/register com Accept v3 e AccountId opcional', function () {
        Http::fake(['advertising-api.amazon.com/assets/register' => Http::response(['assetId' => 'as-9', 'versionId' => 'v1'])]);

        $out = adsCreativeAssetsMethods()->registerAsset('111', ['url' => 'https://up/1', 'name' => 'a.png', 'assetType' => 'IMAGE'], accountId: 'dsp-1');

        expect($out['assetId'])->toBe('as-9');
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/assets/register'
            && $req->hasHeader('Content-Type', 'application/json')
            && $req->hasHeader('Accept', 'application/vnd.creativeassetsregisterresponse.v3+json')
            && $req->header('Amazon-Ads-AccountId')[0] === 'dsp-1'
            && $req['assetType'] === 'IMAGE'
            && adsCreativeAssetsAuthOk($req, '111'));
    });

    it('getUploadLocation + uploadAsset: POST /assets/upload devolve url; PUT cru do binário sem headers da Ads API', function () {
        Http::fake([
            'advertising-api.amazon.com/assets/upload' => Http::response(['url' => 'https://upload.fake/asset?sig=1']),
            'upload.fake/*' => Http::response('', 200),
        ]);

        $loc = adsCreativeAssetsMethods()->getUploadLocation('111', 'banner.png');
        adsCreativeAssetsMethods()->uploadAsset($loc['url'], 'PNGBYTES', 'image/png');

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/assets/upload'
            && $req->hasHeader('Content-Type', 'application/json')
            && $req->hasHeader('Accept', 'application/vnd.creativeassetsuploadresponse.v3+json')
            && $req['fileName'] === 'banner.png'
            && adsCreativeAssetsAuthOk($req, '111'));
        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === 'https://upload.fake/asset?sig=1'
            && $req->body() === 'PNGBYTES'
            && $req->hasHeader('Content-Type', 'image/png')
            && ! $req->hasHeader('Authorization')
            && ! $req->hasHeader('Amazon-Advertising-API-ClientId'));
    });

    it('uploadAsset: 403 (URL expirada) vira AmazonAdsRequestException', function () {
        Http::fake(['upload.fake/*' => Http::response('expired', 403)]);

        expect(fn () => adsCreativeAssetsMethods()->uploadAsset('https://upload.fake/x', 'b'))
            ->toThrow(AmazonAdsRequestException::class);
    });

    it('searchAssets: POST /assets/search com Accept v3', function () {
        Http::fake(['advertising-api.amazon.com/assets/search' => Http::response(['assetList' => [], 'nextToken' => 't'])]);

        $out = adsCreativeAssetsMethods()->searchAssets('111', ['text' => 'banner', 'pageCriteria' => ['size' => 20]]);

        expect($out['nextToken'])->toBe('t');
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/assets/search'
            && $req->hasHeader('Content-Type', 'application/json')
            && $req->hasHeader('Accept', 'application/vnd.creativeassetssearchassetsresponse.v3+json')
            && $req['text'] === 'banner'
            && adsCreativeAssetsAuthOk($req, '111'));
    });
});

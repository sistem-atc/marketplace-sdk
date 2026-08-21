<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;
use SistemAtc\Marketplaces\Amazon\Ads\Support\OAuth as AdsOAuth;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function makeAmazonAdsIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ads-access-token',
        refreshToken: 'ads-refresh',
        settings: [],
        active: true,
        expired: false,
    );
}

describe('MarketPlaces::Amazon()->ads()', function () {
    it('profiles() manda ClientId + Bearer e devolve a lista', function () {
        Http::fake([
            'advertising-api.amazon.com/v2/profiles' => Http::response([
                ['profileId' => 111, 'countryCode' => 'BR', 'accountInfo' => ['type' => 'seller']],
                ['profileId' => 222, 'countryCode' => 'MX', 'accountInfo' => ['type' => 'seller']],
            ]),
        ]);

        $profiles = MarketPlaces::Amazon()->ads(makeAmazonAdsIntegration(), 'client-abc')->profiles();

        expect($profiles)->toHaveCount(2)
            ->and($profiles[0]['countryCode'])->toBe('BR');

        Http::assertSent(function ($req) {
            return str_contains($req->url(), '/v2/profiles')
                && $req->header('Amazon-Advertising-API-ClientId')[0] === 'client-abc'
                && $req->header('Authorization')[0] === 'Bearer ads-access-token';
        });
    });

    it('ciclo do relatório v3: cria com o Scope do profile, consulta e baixa o GZIP_JSON', function () {
        $gz = gzencode(json_encode([
            ['date' => '2026-08-01', 'cost' => 123.45],
            ['date' => '2026-08-02', 'cost' => 10.0],
        ]));

        Http::fake([
            'advertising-api.amazon.com/reporting/reports' => Http::response(['reportId' => 'rep-1', 'status' => 'PENDING']),
            'advertising-api.amazon.com/reporting/reports/rep-1' => Http::response(['reportId' => 'rep-1', 'status' => 'COMPLETED', 'url' => 'https://s3.fake/report.gz']),
            's3.fake/report.gz' => Http::response($gz),
        ]);

        $ads = MarketPlaces::Amazon()->ads(makeAmazonAdsIntegration(), 'client-abc');

        $reportId = $ads->requestDailyCostReport('111', 'SPONSORED_PRODUCTS', '2026-08-01', '2026-08-02');
        expect($reportId)->toBe('rep-1');

        Http::assertSent(function ($req) {
            return str_contains($req->url(), '/reporting/reports')
                && $req->method() === 'POST'
                && $req->header('Amazon-Advertising-API-Scope')[0] === '111'
                && $req['configuration']['reportTypeId'] === 'spCampaigns'
                && $req['configuration']['columns'] === ['date', 'cost'];
        });

        $estado = $ads->getReport('111', 'rep-1');
        expect($estado['status'])->toBe('COMPLETED');

        $rows = $ads->downloadReportRows($estado['url']);
        expect($rows)->toHaveCount(2)
            ->and($rows[0]['cost'])->toBe(123.45);
    });

    it('erro HTTP vira AmazonAdsRequestException com a mensagem do corpo', function () {
        Http::fake([
            'advertising-api.amazon.com/v2/profiles' => Http::response(['message' => 'Unauthorized'], 401),
        ]);

        MarketPlaces::Amazon()->ads(makeAmazonAdsIntegration(), 'client-abc')->profiles();
    })->throws(AmazonAdsRequestException::class, 'Unauthorized');
});

describe('Amazon\Ads\Support\OAuth', function () {
    it('exchangeAuthorizationCode devolve access+refresh', function () {
        Http::fake([
            'api.amazon.com/auth/o2/token' => Http::response(['access_token' => 'A', 'refresh_token' => 'R', 'expires_in' => 3600]),
        ]);

        $data = AdsOAuth::exchangeAuthorizationCode('cid', 'sec', 'code-1', 'https://host/cb');

        expect($data['access_token'])->toBe('A')->and($data['refresh_token'])->toBe('R');
    });

    it('authorizationUrl aponta pro consent com o escopo de ads', function () {
        $url = AdsOAuth::authorizationUrl('cid', 'https://host/cb', 'st');

        expect($url)->toStartWith('https://www.amazon.com.br/ap/oa?')
            ->and($url)->toContain('scope=advertising%3A%3Acampaign_management')
            ->and($url)->toContain('state=st');
    });
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Account\AccountMethods;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function adsAccountMethods(): AccountMethods
{
    $integration = new FakeIntegration(
        accessToken: 'ads-access-token',
        refreshToken: 'ads-refresh',
        settings: [],
        active: true,
        expired: false,
    );

    return new AccountMethods($integration, 'client-abc');
}

/** Headers de auth comuns a toda chamada; `$scope` null = header ausente. */
function adsAccountAuthOk(\Illuminate\Http\Client\Request $req, ?string $scope): bool
{
    $base = $req->header('Amazon-Advertising-API-ClientId')[0] === 'client-abc'
        && $req->header('Authorization')[0] === 'Bearer ads-access-token';

    return $scope === null
        ? $base && ! $req->hasHeader('Amazon-Advertising-API-Scope')
        : $base && $req->header('Amazon-Advertising-API-Scope')[0] === $scope;
}

beforeEach(fn () => Http::preventStrayRequests());

describe('Ads AccountMethods — profiles v2', function () {
    it('listProfiles: GET /v2/profiles sem Scope, com filtros na query', function () {
        Http::fake(['advertising-api.amazon.com/v2/profiles*' => Http::response([
            ['profileId' => 111, 'countryCode' => 'BR'],
        ])]);

        $out = adsAccountMethods()->listProfiles(['profileTypeFilter' => 'seller,vendor']);

        expect($out)->toHaveCount(1)->and($out[0]['profileId'])->toBe(111);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://advertising-api.amazon.com/v2/profiles?profileTypeFilter=seller%2Cvendor'
            && adsAccountAuthOk($req, null));
    });

    it('updateProfiles: PUT /v2/profiles com a lista no body', function () {
        Http::fake(['advertising-api.amazon.com/v2/profiles' => Http::response([['profileId' => 111, 'code' => 'SUCCESS']])]);

        $out = adsAccountMethods()->updateProfiles([['profileId' => 111, 'dailyBudget' => 50.0]]);

        expect($out[0]['code'])->toBe('SUCCESS');
        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === 'https://advertising-api.amazon.com/v2/profiles'
            && $req->hasHeader('Content-Type', 'application/json')
            && $req[0]['dailyBudget'] == 50
            && adsAccountAuthOk($req, null));
    });

    it('getProfileById: GET /v2/profiles/{id} sem Scope', function () {
        Http::fake(['advertising-api.amazon.com/v2/profiles/111' => Http::response(['profileId' => 111, 'countryCode' => 'BR'])]);

        expect(adsAccountMethods()->getProfileById('111')['countryCode'])->toBe('BR');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://advertising-api.amazon.com/v2/profiles/111'
            && adsAccountAuthOk($req, null));
    });
});

describe('Ads AccountMethods — manager accounts', function () {
    it('getManagerAccountsForUser: GET /managerAccounts com Accept v1', function () {
        Http::fake(['advertising-api.amazon.com/managerAccounts' => Http::response(['managerAccounts' => [['managerAccountId' => 'ma-1']]])]);

        expect(adsAccountMethods()->getManagerAccountsForUser()['managerAccounts'][0]['managerAccountId'])->toBe('ma-1');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://advertising-api.amazon.com/managerAccounts'
            && $req->hasHeader('Accept', 'application/vnd.getmanageraccountsresponse.v1+json')
            && adsAccountAuthOk($req, null));
    });

    it('createManagerAccount: POST /managerAccounts com media types próprios', function () {
        Http::fake(['advertising-api.amazon.com/managerAccounts' => Http::response(['managerAccountId' => 'ma-2'])]);

        $out = adsAccountMethods()->createManagerAccount(['managerAccountName' => 'Soldiers', 'managerAccountType' => 'Advertiser']);

        expect($out['managerAccountId'])->toBe('ma-2');
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->hasHeader('Content-Type', 'application/vnd.createmanageraccountrequest.v1+json')
            && $req->hasHeader('Accept', 'application/vnd.manageraccount.v1+json')
            && $req['managerAccountName'] === 'Soldiers'
            && adsAccountAuthOk($req, null));
    });

    it('link/unlink: POST /managerAccounts/{id}/associate|disassociate', function () {
        Http::fake(['advertising-api.amazon.com/managerAccounts/*' => Http::response(['succeedAccounts' => [], 'failedAccounts' => []])]);

        $body = ['accounts' => [['id' => 'A1', 'type' => 'ACCOUNT_ID', 'roles' => ['ENTITY_USER']]]];
        adsAccountMethods()->linkAdvertisingAccountsToManagerAccount('ma 1', $body);
        adsAccountMethods()->unlinkAdvertisingAccountsToManagerAccount('ma 1', $body);

        foreach (['associate', 'disassociate'] as $action) {
            Http::assertSent(fn ($req) => $req->method() === 'POST'
                && $req->url() === "https://advertising-api.amazon.com/managerAccounts/ma%201/{$action}"
                && $req->hasHeader('Content-Type', 'application/vnd.updateadvertisingaccountsinmanageraccountrequest.v1+json')
                && $req->hasHeader('Accept', 'application/vnd.updateadvertisingaccountsinmanageraccountresponse.v1+json')
                && $req['accounts'][0]['id'] === 'A1'
                && adsAccountAuthOk($req, null));
        }
    });
});

describe('Ads AccountMethods — billing', function () {
    it('bulkGetBillingStatus: POST /billing/statuses com media type v1 e Scope opcional', function () {
        Http::fake(['advertising-api.amazon.com/billing/statuses' => Http::response(['success' => [['status' => 'ACTIVE']]])]);

        $body = ['advertiserMarketplaces' => [['advertiserId' => 'ENTITY1', 'marketplaceId' => 'A2Q3Y263D00KWC']]];
        expect(adsAccountMethods()->bulkGetBillingStatus($body, '111')['success'][0]['status'])->toBe('ACTIVE');

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/billing/statuses'
            && $req->hasHeader('Content-Type', 'application/vnd.bulkgetbillingstatusrequestbody.v1+json')
            && $req->hasHeader('Accept', 'application/vnd.bulkgetbillingstatusresponse.v1+json')
            && $req['advertiserMarketplaces'][0]['advertiserId'] === 'ENTITY1'
            && adsAccountAuthOk($req, '111'));
    });

    it('bulkGetBillingNotifications: POST /billing/notifications', function () {
        Http::fake(['advertising-api.amazon.com/billing/notifications' => Http::response(['success' => []])]);

        adsAccountMethods()->bulkGetBillingNotifications(['advertiserMarketplaces' => [], 'locale' => 'pt_BR']);

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/billing/notifications'
            && $req->hasHeader('Content-Type', 'application/vnd.billingnotifications.v1+json')
            && $req->hasHeader('Accept', 'application/vnd.bulkgetbillingnotificationsresponse.v1+json')
            && $req['locale'] === 'pt_BR'
            && adsAccountAuthOk($req, null));
    });
});

describe('Ads AccountMethods — eligibility', function () {
    it('productEligibility: POST /eligibility/product/list com Scope', function () {
        Http::fake(['advertising-api.amazon.com/eligibility/product/list' => Http::response([['productDetails' => ['asin' => 'B0X'], 'overallStatus' => 'ELIGIBLE']])]);

        $out = adsAccountMethods()->productEligibility('111', ['productDetailsList' => [['asin' => 'B0X']], 'adType' => 'sp']);

        expect($out[0]['overallStatus'])->toBe('ELIGIBLE');
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/eligibility/product/list'
            && $req->hasHeader('Content-Type', 'application/json')
            && $req['productDetailsList'][0]['asin'] === 'B0X'
            && adsAccountAuthOk($req, '111'));
    });

    it('programEligibility: POST /eligibility/programs com media type v2, AccountId e Accept-Language', function () {
        Http::fake(['advertising-api.amazon.com/eligibility/programs' => Http::response(['programs' => []])]);

        adsAccountMethods()->programEligibility('111', ['maxResults' => 10], accountId: 'amzn1.ads-account.g.x', acceptLanguage: 'pt-BR');

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/eligibility/programs'
            && $req->hasHeader('Content-Type', 'application/vnd.programeligibility.v2+json')
            && $req->hasHeader('Accept', 'application/vnd.programeligibility.v2+json')
            && $req->header('Amazon-Ads-AccountId')[0] === 'amzn1.ads-account.g.x'
            && $req->header('Accept-Language')[0] === 'pt-BR'
            && $req['maxResults'] === 10
            && adsAccountAuthOk($req, '111'));
    });
});

describe('Ads AccountMethods — localization', function () {
    it('4 rotas /localize: media type versionado + Amazon-Ads-AccountId (cai no profileId)', function () {
        Http::fake(['advertising-api.amazon.com/*' => Http::response(['localizedResponses' => []])]);

        $m = adsAccountMethods();
        $m->getLocalizedCurrencies('111', ['localizeCurrencyRequests' => []]);
        $m->getLocalizedKeywords('111', ['localizeKeywordRequests' => []], accountId: 'acct-9', version: 'v1');
        $m->getLocalizedProducts('111', ['adType' => 'SP']);
        $m->getLocalizedTargetingExpression('111', ['requests' => []]);

        $casos = [
            ['currencies/localize', 'application/vnd.currencylocalization.v2+json', '111'],
            ['keywords/localize', 'application/vnd.keywordlocalization.v1+json', 'acct-9'],
            ['products/localize', 'application/vnd.productlocalization.v2+json', '111'],
            ['targetingExpression/localize', 'application/vnd.targetingexpressionlocalization.v2+json', '111'],
        ];

        foreach ($casos as [$path, $mt, $acct]) {
            Http::assertSent(fn ($req) => $req->method() === 'POST'
                && $req->url() === "https://advertising-api.amazon.com/{$path}"
                && $req->hasHeader('Content-Type', $mt)
                && $req->hasHeader('Accept', $mt)
                && $req->header('Amazon-Ads-AccountId')[0] === $acct
                && adsAccountAuthOk($req, '111'));
        }
        Http::assertSentCount(4);
    });
});

describe('Ads AccountMethods — history + product selector', function () {
    it('getHistory: POST /history com Scope e body de janela', function () {
        Http::fake(['advertising-api.amazon.com/history' => Http::response(['events' => [], 'nextToken' => 'n2'])]);

        $out = adsAccountMethods()->getHistory('111', ['fromDate' => 1, 'toDate' => 2, 'eventTypes' => ['CAMPAIGN' => []]]);

        expect($out['nextToken'])->toBe('n2');
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/history'
            && $req->hasHeader('Content-Type', 'application/json')
            && $req['toDate'] === 2
            && adsAccountAuthOk($req, '111'));
    });

    it('productMetadata: POST /product/metadata com request/response v1', function () {
        Http::fake(['advertising-api.amazon.com/product/metadata' => Http::response(['ProductMetadataList' => []])]);

        adsAccountMethods()->productMetadata('111', ['pageIndex' => 0, 'pageSize' => 50, 'asins' => ['B0X']], accountId: 'dsp-1');

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://advertising-api.amazon.com/product/metadata'
            && $req->hasHeader('Content-Type', 'application/vnd.productmetadatarequest.v1+json')
            && $req->hasHeader('Accept', 'application/vnd.productmetadataresponse.v1+json')
            && $req->header('Amazon-Ads-AccountId')[0] === 'dsp-1'
            && $req['asins'] === ['B0X']
            && adsAccountAuthOk($req, '111'));
    });
});

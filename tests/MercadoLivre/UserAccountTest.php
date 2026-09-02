<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\User\UserMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function userAccountMethods(): UserMethods
{
    $integration = new FakeIntegration(accessToken: 'ml-bearer', refreshToken: 'rt', settings: ['client_id' => 'cli', 'client_secret' => 'sec'], active: true, expired: false);

    return new UserMethods(HttpClientFactory::make($integration), $integration);
}

function userAccountAssertSent(string $method, string $urlPart, ?callable $extra = null): void
{
    Http::assertSent(fn (Request $req) => $req->method() === $method
        && str_contains($req->url(), $urlPart)
        && $req->hasHeader('Authorization', 'Bearer ml-bearer')
        && ($extra === null || $extra($req)));
}

beforeEach(function () {
    config(['marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com', 'mercadolivre.api_base' => 'https://api.mercadolibre.com', 'mercadolivre.access_token_ttl_seconds' => 21600, 'mercadolivre.default_site_id' => 'MLB']);
    Http::preventStrayRequests();
    Http::fake(['api.mercadolibre.com/*' => Http::response(['ok' => true])]);
});

describe('UserMethods — aplicativos e conta', function () {
    it('application / missedFeeds / revokeApplication', function () {
        $m = userAccountMethods();
        $m->application(3022782903258037);
        userAccountAssertSent('GET', '/applications/3022782903258037');
        $m->missedFeeds(3022782903258037, ['topic' => 'orders_v2']);
        userAccountAssertSent('GET', '/missed_feeds?app_id=3022782903258037&topic=orders_v2');
        $m->revokeApplication(206946886, 3022782903258037);
        userAccountAssertSent('DELETE', '/users/206946886/applications/3022782903258037');
    });

    it('acceptedPaymentMethods / addresses / availableListingType', function () {
        $m = userAccountMethods();
        $m->acceptedPaymentMethods(206946886);
        userAccountAssertSent('GET', '/users/206946886/accepted_payment_methods');
        $m->addresses(206946886);
        userAccountAssertSent('GET', '/users/206946886/addresses');
        $m->availableListingType(206946886, 'gold_special', 'MLA6602');
        userAccountAssertSent('GET', '/users/206946886/available_listing_type/gold_special?category_id=MLA6602');
    });

    it('immediate_payment PUT e DELETE', function () {
        $m = userAccountMethods();
        $m->setImmediatePayment(1);
        userAccountAssertSent('PUT', '/users/1/immediate_payment', fn ($r) => $r->data() === ['reason' => 'by_user']);
        $m->removeImmediatePayment(1);
        userAccountAssertSent('DELETE', '/users/1/immediate_payment/by_user');
    });
});

describe('UserMethods — favoritos e classificados', function () {
    it('bookmarks CRUD em /users/me', function () {
        $m = userAccountMethods();
        $m->bookmarks();
        userAccountAssertSent('GET', '/users/me/bookmarks');
        $m->addBookmark('MLA5529');
        userAccountAssertSent('POST', '/users/me/bookmarks', fn ($r) => $r->data() === ['item_id' => 'MLA5529']);
        $m->removeBookmark('MLA5529');
        userAccountAssertSent('DELETE', '/users/me/bookmarks/MLA5529');
    });

    it('classifiedsPromotionPacks e disponibilidade (categoryId camelCase)', function () {
        $m = userAccountMethods();
        $m->classifiedsPromotionPacks(135146148);
        userAccountAssertSent('GET', '/users/135146148/classifieds_promotion_packs');
        $m->classifiedsPromotionPackAvailability(206946886, 'silver', 'MLA1459');
        userAccountAssertSent('GET', '/users/206946886/classifieds_promotion_packs/silver?categoryId=MLA1459');
    });
});

describe('UserMethods — lojas oficiais', function () {
    it('brands e brand', function () {
        $m = userAccountMethods();
        $m->brands(2275117700);
        userAccountAssertSent('GET', '/users/2275117700/brands', fn ($r) => str_ends_with($r->url(), '/brands'));
        $m->brand(2275117700, 294894);
        userAccountAssertSent('GET', '/users/2275117700/brands/294894');
    });
});

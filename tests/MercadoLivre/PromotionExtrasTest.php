<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Promotion\PromotionMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function promotionExtrasMethods(): PromotionMethods
{
    $integration = new FakeIntegration(accessToken: 'ml-bearer', refreshToken: 'rt', settings: ['client_id' => 'cli', 'client_secret' => 'sec'], active: true, expired: false);

    return new PromotionMethods(HttpClientFactory::make($integration), $integration);
}

function promotionExtrasAssertSent(string $method, string $urlPart, ?callable $extra = null): void
{
    Http::assertSent(fn (Request $req) => $req->method() === $method
        && str_contains($req->url(), $urlPart)
        && str_contains($req->url(), 'app_version=v2')
        && $req->hasHeader('Authorization', 'Bearer ml-bearer')
        && ($extra === null || $extra($req)));
}

beforeEach(function () {
    config(['marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com', 'mercadolivre.api_base' => 'https://api.mercadolibre.com', 'mercadolivre.access_token_ttl_seconds' => 21600, 'mercadolivre.default_site_id' => 'MLB']);
    Http::preventStrayRequests();
    Http::fake(['api.mercadolibre.com/*' => Http::response(['results' => [], 'paging' => []])]);
});

describe('PromotionMethods — candidatos, ofertas, promocoes', function () {
    it('getCandidate / getOffer / getPromotion', function () {
        $m = promotionExtrasMethods();
        $m->getCandidate('CANDIDATE-MLB1254949426-803130663');
        promotionExtrasAssertSent('GET', '/seller-promotions/candidates/CANDIDATE-MLB1254949426-803130663');
        $m->getOffer('OFFER-MLB1970246686-42701792');
        promotionExtrasAssertSent('GET', '/seller-promotions/offers/OFFER-MLB1970246686-42701792');
        $m->getPromotion('P-MLB369023', 'BANK');
        promotionExtrasAssertSent('GET', '/seller-promotions/promotions/P-MLB369023', fn ($r) => str_contains($r->url(), 'promotion_type=BANK'));
    });

    it('createPromotion / updatePromotion / deletePromotion', function () {
        $m = promotionExtrasMethods();
        $body = ['promotion_type' => 'VOLUME', 'sub_type' => 'BNGM', 'buy_quantity' => 3, 'pay_quantity' => 2];
        $m->createPromotion($body, ['version' => 'test']);
        promotionExtrasAssertSent('POST', '/seller-promotions/promotions', fn ($r) => str_contains($r->url(), 'version=test') && $r->data() === $body);
        $m->updatePromotion('C-MLB5783', $body);
        promotionExtrasAssertSent('PUT', '/seller-promotions/promotions/C-MLB5783', fn ($r) => $r->data()['buy_quantity'] === 3);
        $m->deletePromotion('C-MLB5783', 'VOLUME');
        promotionExtrasAssertSent('DELETE', '/seller-promotions/promotions/C-MLB5783', fn ($r) => str_contains($r->url(), 'promotion_type=VOLUME'));
    });
});

describe('PromotionMethods — lista de exclusao', function () {
    it('seller: status e set', function () {
        $m = promotionExtrasMethods();
        $m->sellerExclusionStatus();
        promotionExtrasAssertSent('GET', '/seller-promotions/exclusion-list/seller?');
        $m->setSellerExclusion(true);
        promotionExtrasAssertSent('POST', '/seller-promotions/exclusion-list/seller', fn ($r) => $r->data() === ['exclusion_status' => 'true']);
    });

    it('item: status e set', function () {
        $m = promotionExtrasMethods();
        $m->itemExclusionStatus('MLB123');
        promotionExtrasAssertSent('GET', '/seller-promotions/exclusion-list/seller/MLB123');
        $m->setItemExclusion('12345678', false);
        promotionExtrasAssertSent('POST', '/seller-promotions/exclusion-list/item', fn ($r) => $r->data() === ['item_id' => '12345678', 'exclusion_status' => 'false']);
    });
});

describe('PromotionMethods — itens (offer_id, exclusao em massa, filtros)', function () {
    it('enrollItem com offer_id (BANK) e withdrawItem com offer_id', function () {
        $m = promotionExtrasMethods();
        $m->enrollItem('MLB3293481659', 'P-MLA81', 'BANK', null, 'CANDIDATE-MLB1-0101');
        promotionExtrasAssertSent('POST', '/seller-promotions/items/MLB3293481659', fn ($r) => $r->data() === ['promotion_id' => 'P-MLA81', 'promotion_type' => 'BANK', 'offer_id' => 'CANDIDATE-MLB1-0101']);
        $m->withdrawItem('MLA632979587', '1804', 'VOLUME', 'MLA876618673-9eaf');
        promotionExtrasAssertSent('DELETE', '/seller-promotions/items/MLA632979587', fn ($r) => str_contains($r->url(), 'offer_id=MLA876618673-9eaf') && str_contains($r->url(), 'promotion_type=VOLUME'));
    });

    it('withdrawItemFromAll so manda app_version', function () {
        promotionExtrasMethods()->withdrawItemFromAll('MLB1399846831');
        promotionExtrasAssertSent('DELETE', '/seller-promotions/items/MLB1399846831?app_version=v2', fn ($r) => ! str_contains($r->url(), 'promotion_id'));
    });

    it('listItems aceita filtros status/item_id/status_item', function () {
        promotionExtrasMethods()->listItems('MLB1111', 'DEAL', 50, null, ['status' => 'started', 'item_id' => 'MLB604400000']);
        promotionExtrasAssertSent('GET', '/seller-promotions/promotions/MLB1111/items', fn ($r) => str_contains($r->url(), 'status=started') && str_contains($r->url(), 'item_id=MLB604400000') && str_contains($r->url(), 'promotion_type=DEAL'));
    });
});

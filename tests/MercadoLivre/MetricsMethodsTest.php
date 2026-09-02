<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Metrics\MetricsMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function metricsTestMethods(): MetricsMethods
{
    $integration = new FakeIntegration(accessToken: 'ml-bearer', refreshToken: 'rt', settings: ['client_id' => 'cli', 'client_secret' => 'sec'], active: true, expired: false);

    return new MetricsMethods(HttpClientFactory::make($integration), $integration);
}

function metricsTestAssertSent(string $method, string $urlPart, ?callable $extra = null): void
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

describe('MetricsMethods — visitas', function () {
    it('userItemsVisits / userItemsVisitsTimeWindow', function () {
        $m = metricsTestMethods();
        $m->userItemsVisits(1000011398, '2021-01-01', '2021-02-01');
        metricsTestAssertSent('GET', '/users/1000011398/items_visits?date_from=2021-01-01&date_to=2021-02-01');
        $m->userItemsVisitsTimeWindow(1000011398, 2, 'day', '2021-08-06');
        metricsTestAssertSent('GET', '/users/1000011398/items_visits/time_window?last=2&unit=day&ending=2021-08-06');
    });

    it('itemsVisits / itemVisitsTimeWindow / visitsTotal', function () {
        $m = metricsTestMethods();
        $m->itemsVisits(['MLB1', 'MLB2'], '2021-01-01', '2021-02-01');
        metricsTestAssertSent('GET', '/items/visits?ids=MLB1%2CMLB2&date_from=2021-01-01&date_to=2021-02-01');
        $m->itemVisitsTimeWindow('MLB1', 2);
        metricsTestAssertSent('GET', '/items/MLB1/visits/time_window?last=2&unit=day');
        $m->visitsTotal(['MLB9992242141']);
        metricsTestAssertSent('GET', '/visits/items?ids=MLB9992242141');
    });
});

describe('MetricsMethods — tendencias e destaques', function () {
    it('trends com e sem categoria', function () {
        $m = metricsTestMethods();
        $m->trends('MLB');
        metricsTestAssertSent('GET', '/trends/MLB');
        $m->trends('MLB', 'MLB1430');
        metricsTestAssertSent('GET', '/trends/MLB/MLB1430');
    });

    it('highlights por categoria (com atributo), item e produto', function () {
        $m = metricsTestMethods();
        $m->highlightsByCategory('MLB', 'MLB432825');
        metricsTestAssertSent('GET', '/highlights/MLB/category/MLB432825', fn ($r) => ! str_contains($r->url(), 'attribute'));
        $m->highlightsByCategory('MLA', 'MLA1055', 'BRAND', '59387');
        metricsTestAssertSent('GET', '/highlights/MLA/category/MLA1055?attribute=BRAND&attributeValue=59387');
        $m->highlightsForItem('MLB', 'MLB6868664726');
        metricsTestAssertSent('GET', '/highlights/MLB/item/MLB6868664726');
        $m->highlightsForProduct('MLA', 'MLA55323897');
        metricsTestAssertSent('GET', '/highlights/MLA/product/MLA55323897');
    });
});

describe('MetricsMethods — opinioes, performance, experiencia de compra', function () {
    it('reviews com paginacao', function () {
        metricsTestMethods()->reviews('MLB4330911507', ['limit' => 10, 'offset' => 5]);
        metricsTestAssertSent('GET', '/reviews/item/MLB4330911507?limit=10&offset=5');
    });

    it('itemPerformance / userProductPerformance', function () {
        $m = metricsTestMethods();
        $m->itemPerformance('MLA1435540505');
        metricsTestAssertSent('GET', '/item/MLA1435540505/performance');
        $m->userProductPerformance('MLAU395977691');
        metricsTestAssertSent('GET', '/user-product/MLAU395977691/performance');
    });

    it('itemPurchaseExperience / userProductPurchaseExperience com locale', function () {
        $m = metricsTestMethods();
        $m->itemPurchaseExperience('MLA1391786841', 'pt_BR');
        metricsTestAssertSent('GET', '/reputation/items/MLA1391786841/purchase_experience/integrators?locale=pt_BR');
        $m->userProductPurchaseExperience('MLAU1391786841');
        metricsTestAssertSent('GET', '/reputation/user_products/MLAU1391786841/purchase_experience/integrators');
    });
});

describe('MetricsMethods — seller_recovery e catalog_quality', function () {
    it('status / activate / cancel_guarantee / legal-document', function () {
        $m = metricsTestMethods();
        $m->sellerRecoveryStatus();
        metricsTestAssertSent('GET', '/users/reputation/seller_recovery/status');
        $m->activateSellerRecovery();
        metricsTestAssertSent('POST', '/users/reputation/seller_recovery/activate');
        $m->cancelSellerRecoveryGuarantee('goal_achieved');
        metricsTestAssertSent('PUT', '/users/reputation/seller_recovery/cancel_guarantee', fn ($r) => $r->data() === ['cancellation_reason' => 'goal_achieved']);
        $m->sellerRecoveryLegalDocument('COMPLETE');
        metricsTestAssertSent('GET', '/users/reputation/seller_recovery/legal-document?type=COMPLETE');
    });

    it('catalogQualityStatus por seller e por item', function () {
        $m = metricsTestMethods();
        $m->catalogQualityStatusForSeller(321654987, true);
        metricsTestAssertSent('GET', '/catalog_quality/status?seller_id=321654987&include_items=true&v=32');
        $m->catalogQualityStatusForItem('MLA123456789');
        metricsTestAssertSent('GET', '/catalog_quality/status?item_id=MLA123456789&v=3');
    });
});

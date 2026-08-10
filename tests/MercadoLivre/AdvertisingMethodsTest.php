<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function mlAdvertisingIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config(['marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com']);
    Http::preventStrayRequests();
});

describe('MarketPlaces::MercadoLivre()->advertising() — header Api-Version', function () {
    it('listAdvertisers manda Api-Version 1 e o product_id', function () {
        Http::fake([
            'api.mercadolibre.com/advertising/advertisers*' => Http::response([
                'advertisers' => [[
                    'advertiser_id' => 4495,
                    'site_id' => 'MLB',
                    'advertiser_name' => 'SOLDIERS NUTRITION',
                ]],
            ]),
        ]);

        $resp = MarketPlaces::MercadoLivre()->advertising(mlAdvertisingIntegration())->listAdvertisers('PADS');

        expect($resp['advertisers'][0]['advertiser_id'])->toBe(4495);

        Http::assertSent(fn ($req) => str_contains($req->url(), '/advertising/advertisers')
            && str_contains($req->url(), 'product_id=PADS')
            && $req->header('Api-Version') === ['1']);
    });

    it('searchProductAdsCampaigns usa o namespace /marketplace com Api-Version 2', function () {
        Http::fake([
            'api.mercadolibre.com/marketplace/advertising/MLB/advertisers/4495/product_ads/campaigns/search*' => Http::response([
                'paging' => ['offset' => 0, 'total' => 2, 'limit' => 50],
                'results' => [
                    ['id' => 1, 'name' => 'CREATINA', 'metrics' => ['cost' => 184848.07]],
                    ['id' => 2, 'name' => 'ALL', 'metrics' => ['cost' => 6231.47]],
                ],
                'metrics_summary' => ['cost' => 191079.54],
            ]),
        ]);

        $resp = MarketPlaces::MercadoLivre()->advertising(mlAdvertisingIntegration())
            ->searchProductAdsCampaigns('MLB', 4495, '2026-08-05', '2026-08-05');

        expect($resp['metrics_summary']['cost'])->toBe(191079.54)
            ->and($resp['results'])->toHaveCount(2);

        Http::assertSent(function ($req) {
            return str_contains($req->url(), '/marketplace/advertising/MLB/advertisers/4495/product_ads/campaigns/search')
                && str_contains($req->url(), 'date_from=2026-08-05')
                && str_contains($req->url(), 'date_to=2026-08-05')
                && str_contains($req->url(), 'metrics=cost')
                // metrics_summary=true exige metrics junto (senao 400)
                && str_contains($req->url(), 'metrics_summary=true')
                && $req->header('Api-Version') === ['2'];
        });
    });

    it('nao vaza o Api-Version de uma chamada pra outra no mesmo client', function () {
        Http::fake([
            'api.mercadolibre.com/marketplace/*' => Http::response(['metrics_summary' => ['cost' => 1.0]]),
            'api.mercadolibre.com/advertising/*' => Http::response(['results' => []]),
        ]);

        $advertising = MarketPlaces::MercadoLivre()->advertising(mlAdvertisingIntegration());
        $advertising->searchProductAdsCampaigns('MLB', 4495, '2026-08-05', '2026-08-05'); // Api-Version 2
        $advertising->listDisplayCampaigns(4495);                                          // Api-Version 1

        // Sem o clone no executeRequest, o withHeaders mutaria o PendingRequest
        // e o header viraria ['2', '1'] na segunda chamada.
        Http::assertSent(fn ($req) => str_contains($req->url(), '/display/campaigns')
            && $req->header('Api-Version') === ['1']);
    });
});

describe('MarketPlaces::MercadoLivre()->advertising() — Display e Brand', function () {
    it('displayCampaignMetrics devolve a serie diaria com consumed_budget', function () {
        Http::fake([
            'api.mercadolibre.com/advertising/advertisers/4495/display/campaigns/77/metrics*' => Http::response([
                'metrics' => [
                    ['date' => '2026-08-07', 'consumed_budget' => 4043.26],
                    ['date' => '2026-08-06', 'consumed_budget' => 4562.56],
                ],
            ]),
        ]);

        $resp = MarketPlaces::MercadoLivre()->advertising(mlAdvertisingIntegration())
            ->displayCampaignMetrics(4495, 77, '2026-08-06', '2026-08-07');

        expect($resp['metrics'])->toHaveCount(2)
            ->and($resp['metrics'][0]['consumed_budget'])->toBe(4043.26);

        Http::assertSent(fn ($req) => str_contains($req->url(), 'date_from=2026-08-06')
            && str_contains($req->url(), 'date_to=2026-08-07')
            && $req->header('Api-Version') === ['1']);
    });

    it('listBrandCampaigns le a chave campaigns (nao results)', function () {
        Http::fake([
            'api.mercadolibre.com/advertising/advertisers/4495/brand_ads/campaigns' => Http::response([
                'campaigns' => [['campaign_id' => 900, 'budget' => 100.0]],
            ]),
        ]);

        $resp = MarketPlaces::MercadoLivre()->advertising(mlAdvertisingIntegration())->listBrandCampaigns(4495);

        expect($resp['campaigns'][0]['campaign_id'])->toBe(900);
    });

    it('brandCampaignMetrics devolve consumed_budget aninhado em metrics', function () {
        Http::fake([
            'api.mercadolibre.com/advertising/advertisers/4495/brand_ads/campaigns/900/metrics*' => Http::response([
                'metrics' => [
                    ['date' => '2026-08-06', 'metrics' => ['consumed_budget' => 1256.10]],
                ],
            ]),
        ]);

        $resp = MarketPlaces::MercadoLivre()->advertising(mlAdvertisingIntegration())
            ->brandCampaignMetrics(4495, 900, '2026-08-06', '2026-08-06');

        expect($resp['metrics'][0]['metrics']['consumed_budget'])->toBe(1256.10);

        Http::assertSent(fn ($req) => $req->header('Api-Version') === ['1']);
    });
});

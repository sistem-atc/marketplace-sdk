<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;
use SistemAtc\Marketplaces\Tiktok\Endpoints\AffiliateCreator\AffiliateCreatorMethods;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

/**
 * Integration de CREATOR: sem `shop_cipher` de proposito. O token de creator
 * nao pertence a uma loja — mandar cipher aqui assina a chamada com um dono
 * que nao e' o do token.
 */
function acIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'creator-token',
        refreshToken: 'rt',
        settings: ['app_key' => 'ak', 'app_secret' => 'as'],
        active: true,
        expired: false,
    );
}

function ac(): AffiliateCreatorMethods
{
    return MarketPlaces::Tiktok()->affiliateCreator(acIntegration());
}

describe('MarketPlaces::Tiktok()->affiliateCreator()', function () {
    it('nao manda shop_cipher: o token de creator nao tem loja', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/affiliate_creator/202508/profiles*' => Http::response([
                'code' => 0, 'data' => ['username' => 'soldiers'],
            ]),
        ]);

        expect(ac()->getCreatorProfile()->username)->toBe('soldiers');

        Http::assertSent(fn ($req) => $req->method() === 'GET' && ! str_contains($req->url(), 'shop_cipher'));
    });

    it('searchSampleApplications pagina por page_token e filtra status no BODY', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/affiliate_creator/202412/sample_applications/search*' => Http::response([
                'code' => 0,
                'data' => ['sample_applications' => [['id' => 'A1', 'main_order_id' => 'O1', 'type' => 'FREE_SAMPLE']]],
            ]),
        ]);

        $resp = ac()->searchSampleApplications(['SHIPPED'], pageSize: 50, pageToken: 'tk');

        // O elo com o pedido: main_order_id de uma amostra NAO e venda.
        expect($resp->sampleApplications[0]->mainOrderId)->toBe('O1');

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && str_contains($req->url(), 'page_token=tk')
            && str_contains($req->url(), 'page_size=50')
            && str_contains($req->body(), '"application_statuses":["SHIPPED"]'));
    });

    it('getSampleApplicationDetail exige application_id em FREE_SAMPLE', function () {
        Http::fake();

        ac()->getSampleApplicationDetail('P1', 'FREE_SAMPLE');
    })->throws(InvalidArgumentException::class, 'application_id e obrigatorio');

    it('getSampleApplicationDetail exige main_order_id nos demais tipos', function () {
        Http::fake();

        ac()->getSampleApplicationDetail('P1', 'SAMPLE_COUPON', applicationId: 'A1');
    })->throws(InvalidArgumentException::class, 'main_order_id e obrigatorio');

    it('getApplicableSampleLabel manda product_id na QUERY (endpoint GET)', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/affiliate_creator/202412/samples/labels*' => Http::response([
                'code' => 0, 'data' => ['label' => ['can_apply' => true, 'status' => 'TO_APPLY']],
            ]),
        ]);

        expect(ac()->getApplicableSampleLabel('P9')->label->canApply)->toBeTrue();

        Http::assertSent(fn ($req) => $req->method() === 'GET' && str_contains($req->url(), 'product_id=P9'));
    });

    it('getOpenCollaborationProductsByIds e POST mas com os ids na QUERY, separados por virgula', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/affiliate_creator/202509/open_collaborations/products*' => Http::response([
                'code' => 0, 'data' => ['products' => [['id' => '1']]],
            ]),
        ]);

        ac()->getOpenCollaborationProductsByIds(['1', '2']);

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && str_contains(urldecode($req->url()), 'product_ids=1,2'));
    });

    it('generatePublisherSharingLinks coloca o publisher_id no PATH', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/affiliate_creator/202504/affiliate_sharing_links/publisher/PUB9/generate_batch*' => Http::response([
                'code' => 0, 'data' => ['sharing_links' => [['material_id' => 'M1']]],
            ]),
        ]);

        expect(ac()->generatePublisherSharingLinks('PUB9', ['M1'])->sharingLinks[0]->materialId)->toBe('M1');

        Http::assertSent(fn ($req) => str_contains($req->url(), '/publisher/PUB9/generate_batch')
            && str_contains($req->body(), '"ids":["M1"]'));
    });

    it('recusa lote de sharing link acima de 50 materiais antes de bater na API', function () {
        Http::fake();

        ac()->generateGeneralSharingLinks(array_fill(0, 51, 'M'));
    })->throws(InvalidArgumentException::class, 'Max 50 materiais');

    it('recusa mais de 20 produtos na adicao a vitrine', function () {
        Http::fake();

        ac()->addShowcaseProducts('PRODUCT_ID', array_fill(0, 21, 'P'));
    })->throws(InvalidArgumentException::class, 'Max 20 produtos');

    it('searchAffiliateTraceOrders manda a janela e o time_type no BODY', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/affiliate_creator/202505/orders/trace/search*' => Http::response([
                'code' => 0, 'data' => ['orders' => [], 'total_count' => 0],
            ]),
        ]);

        ac()->searchAffiliateTraceOrders(1_700_000_000, 1_700_086_400, 'SETTLE_TIME');

        Http::assertSent(fn ($req) => str_contains($req->body(), '"time_ge":1700000000')
            && str_contains($req->body(), '"time_lt":1700086400')
            && str_contains($req->body(), '"time_type":"SETTLE_TIME"'));
    });

    it('os uploads sao POST multipart, nao o GET que a doc anuncia', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/affiliate_creator/202505/videos/video_files*' => Http::response([
                'code' => 0, 'data' => ['video_file' => ['id' => 'F1', 'md5' => 'ABC']],
            ]),
        ]);

        expect(ac()->uploadShoppableVideoFile('bytes', 'clip.mp4')->videoFile->id)->toBe('F1');

        Http::assertSent(function ($req) {
            // Corpo multipart => o content-type NAO pode ser application/json,
            // senao o TikTok recusa (o boundary do Guzzle so' nasce sem header fixo).
            return $req->method() === 'POST'
                && ! str_contains(implode(',', $req->header('Content-Type')), 'application/json');
        });
    });

    it('searchMusic repassa o search_id — sem ele a paginacao quebra em silencio', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/affiliate_creator/202602/music/search*' => Http::response([
                'code' => 0, 'data' => ['music' => [], 'has_more' => false],
            ]),
        ]);

        ac()->searchMusic('funk', searchId: 'SID1', pageToken: '40', pageSize: 10);

        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && str_contains($req->url(), 'search_id=SID1')
            && str_contains($req->url(), 'page_token=40')
            // page_size vai como string neste endpoint (unico do grupo assim)
            && str_contains($req->url(), 'page_size=10'));
    });

    it('postShoppableVideo monta video_info e product_link_info separados', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/affiliate_creator/202607/videos*' => Http::response([
                'code' => 0, 'data' => ['video' => ['id' => 'V1'], 'quota' => '3/day'],
            ]),
        ]);

        ac()->postShoppableVideo('F1', 'Legenda #whey', 'P1', 'Compre aqui', coverTimestampMs: 1500);

        Http::assertSent(fn ($req) => str_contains($req->body(), '"file_id":"F1"')
            && str_contains($req->body(), '"cover_timestamp_ms":1500')
            && str_contains($req->body(), '"product_link_info":{"product_id":"P1","title":"Compre aqui"}'));
    });

    it('getShoppableVideoStatus e o precheck result usam id no PATH', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/affiliate_creator/202509/videos/V7/status*' => Http::response([
                'code' => 0, 'data' => ['video' => ['id' => 'V7', 'post_status' => 'PROCESSING']],
            ]),
            'open-api.tiktokglobalshop.com/affiliate_creator/202601/videos/precheck_tasks/T7*' => Http::response([
                'code' => 0, 'data' => ['precheck_task' => ['id' => 'T7']],
            ]),
        ]);

        expect(ac()->getShoppableVideoStatus('V7')->video->postStatus)->toBe('PROCESSING')
            ->and(ac()->getShoppableVideoPrecheckResult('T7')->precheckTask->id)->toBe('T7');
    });
});

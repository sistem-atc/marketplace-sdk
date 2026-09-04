<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Video\VideoMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function videoMethods(): VideoMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: [
            'partner_id' => 2030136,
            'partner_key' => 'fake-key',
            'shop_id' => 999999,
            'merchant_id' => 777,
            'user_id' => 424242,
        ],
        active: true,
        expired: false,
    );

    return new VideoMethods(HttpClientFactory::make($integration), $integration);
}

/** Verbo + path + assinatura de User API (user_id, sem shop_id/merchant_id). */
function videoSigned(Request $req, string $method, string $path): bool
{
    $url = $req->url();

    return $req->method() === $method
        && str_contains($url, '/api/v2/video/'.$path.'?')
        && str_contains($url, 'partner_id=2030136')
        && str_contains($url, 'timestamp=')
        && str_contains($url, 'access_token=shopee-token')
        && str_contains($url, 'user_id=424242')
        && str_contains($url, 'sign=')
        && ! str_contains($url, 'shop_id=')
        && ! str_contains($url, 'merchant_id=');
}

function videoFake(string $path, array $response = []): void
{
    Http::fake([
        'partner.shopeemobile.com/api/v2/video/'.$path.'*' => Http::response([
            'error' => '', 'message' => '', 'request_id' => 'req-1', 'response' => $response,
        ]),
    ]);
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

describe('VideoMethods publicacao', function () {
    it('getCoverList GET video_upload_id', function () {
        videoFake('get_cover_list', ['cover_list' => ['https://c/1.jpg']]);
        expect(videoMethods()->getCoverList('sg-111')['cover_list'])->toHaveCount(1);
        Http::assertSent(fn (Request $r) => videoSigned($r, 'GET', 'get_cover_list')
            && str_contains($r->url(), 'video_upload_id=sg-111'));
    });

    it('editVideoInfo POST video_upload_list + aigc_label', function () {
        videoFake('edit_video_info', ['success_list' => ['sg-111'], 'failure_list' => []]);
        $list = [[
            'video_upload_id' => 'sg-111',
            'caption' => 'teste',
            'cover_image_url' => 'https://c/1.jpg',
            'item_info' => [['item_id' => 2]],
            'allow_info' => ['allow_duet' => true, 'allow_stitch' => false],
            'scheduled_info' => ['scheduled_post' => false],
        ]];

        $resp = videoMethods()->editVideoInfo($list, aigcLabel: true);

        expect($resp['success_list'])->toBe(['sg-111']);
        Http::assertSent(fn (Request $r) => videoSigned($r, 'POST', 'edit_video_info')
            && $r['video_upload_list'] === $list && $r['aigc_label'] === true);
    });

    it('postVideo POST video_upload_id_list', function () {
        videoFake('post_video', ['success_list' => []]);
        videoMethods()->postVideo(['sg-111', 'sg-222']);
        Http::assertSent(fn (Request $r) => videoSigned($r, 'POST', 'post_video')
            && $r['video_upload_id_list'] === ['sg-111', 'sg-222']);
    });

    it('deleteVideo POST aceita so um dos dois ids', function () {
        videoFake('delete_video', ['success_list' => [], 'failure_list' => []]);

        videoMethods()->deleteVideo(videoUploadIdList: ['sg-111']);
        Http::assertSent(fn (Request $r) => videoSigned($r, 'POST', 'delete_video')
            && ($r->data()['video_upload_id_list'] ?? null) === ['sg-111'] && ! array_key_exists('post_id_list', $r->data()));

        videoMethods()->deleteVideo(postIdList: ['P==']);
        Http::assertSent(fn (Request $r) => videoSigned($r, 'POST', 'delete_video')
            && ($r->data()['post_id_list'] ?? null) === ['P=='] && ! array_key_exists('video_upload_id_list', $r->data()));

        expect(fn () => videoMethods()->deleteVideo())->toThrow(InvalidArgumentException::class)
            ->and(fn () => videoMethods()->deleteVideo(['a'], ['b']))->toThrow(InvalidArgumentException::class);
    });

    it('getVideoDetail GET aceita so um dos dois ids', function () {
        videoFake('get_video_detail', ['status' => 300]);

        expect(videoMethods()->getVideoDetail(postId: 'P==')['status'])->toBe(300);
        Http::assertSent(fn (Request $r) => videoSigned($r, 'GET', 'get_video_detail')
            && str_contains($r->url(), 'post_id=P%3D%3D') && ! str_contains($r->url(), 'video_upload_id='));

        videoMethods()->getVideoDetail(videoUploadId: 'sg-111');
        Http::assertSent(fn (Request $r) => str_contains($r->url(), 'video_upload_id=sg-111') && ! str_contains($r->url(), 'post_id='));

        expect(fn () => videoMethods()->getVideoDetail())->toThrow(InvalidArgumentException::class);
    });

    it('getVideoList GET page_no/page_size/list_type', function () {
        videoFake('get_video_list', ['video_list' => []]);
        videoMethods()->getVideoList(listType: 1, pageNo: 2, pageSize: 20);
        Http::assertSent(fn (Request $r) => videoSigned($r, 'GET', 'get_video_list')
            && str_contains($r->url(), 'page_no=2') && str_contains($r->url(), 'page_size=20')
            && str_contains($r->url(), 'list_type=1'));
    });
});

describe('VideoMethods analytics agregado', function () {
    it('getOverviewPerformance GET period_type + end_date', function () {
        videoFake('get_overview_performance', ['views' => 1]);
        expect(videoMethods()->getOverviewPerformance('Last7d', '2026-09-03')['views'])->toBe(1);
        Http::assertSent(fn (Request $r) => videoSigned($r, 'GET', 'get_overview_performance')
            && str_contains($r->url(), 'period_type=Last7d') && str_contains($r->url(), 'end_date=2026-09-03'));
    });

    it('getMetricTrend GET period_type + end_date', function () {
        videoFake('get_metric_trend', ['trend' => []]);
        videoMethods()->getMetricTrend('Week', '2026-08-30');
        Http::assertSent(fn (Request $r) => videoSigned($r, 'GET', 'get_metric_trend')
            && str_contains($r->url(), 'period_type=Week') && str_contains($r->url(), 'end_date=2026-08-30'));
    });

    it('getUserDemographics GET sem parametros', function () {
        videoFake('get_user_demographics', ['gender' => []]);
        expect(videoMethods()->getUserDemographics())->toHaveKey('gender');
        Http::assertSent(fn (Request $r) => videoSigned($r, 'GET', 'get_user_demographics'));
    });

    it('getVideoPerformanceList GET com order_by/sort e caption opcional', function () {
        videoFake('get_video_performance_list', ['list' => []]);
        videoMethods()->getVideoPerformanceList('Day', '2026-09-03', 1, 20, 'Likes', 'asc', 'promo');
        Http::assertSent(fn (Request $r) => videoSigned($r, 'GET', 'get_video_performance_list')
            && str_contains($r->url(), 'page_no=1') && str_contains($r->url(), 'page_size=20')
            && str_contains($r->url(), 'period_type=Day') && str_contains($r->url(), 'end_date=2026-09-03')
            && str_contains($r->url(), 'order_by=Likes') && str_contains($r->url(), 'sort=asc')
            && str_contains($r->url(), 'caption=promo'));

        videoMethods()->getVideoPerformanceList('Day', '2026-09-03');
        Http::assertSent(fn (Request $r) => str_contains($r->url(), 'order_by=Views') && ! str_contains($r->url(), 'caption='));
    });

    it('getProductPerformanceList GET usa o path com typo oficial e filtros opcionais', function () {
        videoFake('get_prodcut_performance_list', ['list' => []]);
        videoMethods()->getProductPerformanceList('Month', '2026-08-31', itemId: 55, itemName: 'whey');
        Http::assertSent(fn (Request $r) => videoSigned($r, 'GET', 'get_prodcut_performance_list')
            && str_contains($r->url(), 'period_type=Month') && str_contains($r->url(), 'end_date=2026-08-31')
            && str_contains($r->url(), 'order_by=PlacedOrders') && str_contains($r->url(), 'sort=desc')
            && str_contains($r->url(), 'item_id=55') && str_contains($r->url(), 'item_name=whey'));
    });
});

describe('VideoMethods analytics por video', function () {
    it('getVideoDetailPerformance GET post_id', function () {
        videoFake('get_video_detail_performance', ['views' => 9]);
        expect(videoMethods()->getVideoDetailPerformance('P1')['views'])->toBe(9);
        Http::assertSent(fn (Request $r) => videoSigned($r, 'GET', 'get_video_detail_performance')
            && str_contains($r->url(), 'post_id=P1'));
    });

    it('getVideoDetailMetricTrend GET post_id + metric_name', function () {
        videoFake('get_video_detail_metric_trend', ['trend' => []]);
        videoMethods()->getVideoDetailMetricTrend('P1', 'PlacedSales');
        Http::assertSent(fn (Request $r) => videoSigned($r, 'GET', 'get_video_detail_metric_trend')
            && str_contains($r->url(), 'post_id=P1') && str_contains($r->url(), 'metric_name=PlacedSales'));
    });

    it('getVideoDetailAudienceDistribution GET post_id', function () {
        videoFake('get_video_detail_audience_distribution', ['age' => []]);
        videoMethods()->getVideoDetailAudienceDistribution('P1');
        Http::assertSent(fn (Request $r) => videoSigned($r, 'GET', 'get_video_detail_audience_distribution')
            && str_contains($r->url(), 'post_id=P1'));
    });

    it('getVideoDetailProductPerformance GET paginado com filtros opcionais', function () {
        videoFake('get_video_detail_product_performance', ['list' => []]);
        videoMethods()->getVideoDetailProductPerformance('P1', 2, 10, 55);
        Http::assertSent(fn (Request $r) => videoSigned($r, 'GET', 'get_video_detail_product_performance')
            && str_contains($r->url(), 'post_id=P1') && str_contains($r->url(), 'page_no=2')
            && str_contains($r->url(), 'page_size=10') && str_contains($r->url(), 'item_id=55')
            && ! str_contains($r->url(), 'item_name='));
    });
});

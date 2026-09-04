<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Livestream\LivestreamMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function livestreamIntegration(array $extraSettings = []): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: array_merge([
            'partner_id' => 2030136,
            'partner_key' => 'fake-key',
            'shop_id' => 999999,
            'merchant_id' => 777,
            'user_id' => 424242,
        ], $extraSettings),
        active: true,
        expired: false,
    );
}

function livestreamMethods(array $extraSettings = []): LivestreamMethods
{
    $integration = livestreamIntegration($extraSettings);

    return new LivestreamMethods(HttpClientFactory::make($integration), $integration);
}

/** Verbo + path + assinatura de User API (user_id, sem shop_id/merchant_id). */
function livestreamSigned(Request $req, string $method, string $path): bool
{
    $url = $req->url();

    return $req->method() === $method
        && str_contains($url, '/api/v2/livestream/'.$path.'?')
        && str_contains($url, 'partner_id=2030136')
        && str_contains($url, 'timestamp=')
        && str_contains($url, 'access_token=shopee-token')
        && str_contains($url, 'user_id=424242')
        && str_contains($url, 'sign=')
        && ! str_contains($url, 'shop_id=')
        && ! str_contains($url, 'merchant_id=');
}

function livestreamFake(string $path, array $response = []): void
{
    Http::fake([
        'partner.shopeemobile.com/api/v2/livestream/'.$path.'*' => Http::response([
            'error' => '', 'message' => '', 'request_id' => 'req-1', 'response' => $response,
        ]),
    ]);
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

describe('UserApiMethods (auth)', function () {
    it('assina com user_id e recusa integracao sem user_id', function () {
        livestreamFake('get_session_detail', ['session_id' => 1]);
        livestreamMethods()->getSessionDetail(1);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'GET', 'get_session_detail'));

        expect(fn () => livestreamMethods(['user_id' => null])->getSessionDetail(1))
            ->toThrow(InvalidArgumentException::class);
    });

    it('aceita main_account_id como fallback do user_id', function () {
        livestreamFake('get_session_detail', ['session_id' => 1]);
        livestreamMethods(['user_id' => null, 'main_account_id' => 424242])->getSessionDetail(1);
        Http::assertSent(fn (Request $r) => str_contains($r->url(), 'user_id=424242'));
    });
});

describe('LivestreamMethods sessao', function () {
    it('uploadImage POST multipart com campo image e devolve image_url', function () {
        livestreamFake('upload_image', ['image_url' => 'https://cf.shopee/cover.jpg']);

        $resp = livestreamMethods()->uploadImage('BINARY', 'capa.jpg');

        expect($resp['image_url'])->toBe('https://cf.shopee/cover.jpg');
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'POST', 'upload_image')
            && $r->isMultipart()
            && $r->hasFile('image', 'BINARY', 'capa.jpg')
            && ! str_contains((string) $r->header('Content-Type')[0], 'application/json'));
    });

    it('createSession POST body completo', function () {
        livestreamFake('create_session', ['session_id' => 6236215]);

        $resp = livestreamMethods()->createSession(title: 'Live', description: 'Desc', coverImageUrl: 'https://c/x.jpg', isTest: true);

        expect($resp['session_id'])->toBe(6236215);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'POST', 'create_session')
            && $r['title'] === 'Live' && $r['description'] === 'Desc'
            && $r['cover_image_url'] === 'https://c/x.jpg' && $r['is_test'] === true);
    });

    it('getSessionDetail GET session_id', function () {
        livestreamFake('get_session_detail', ['session_id' => 5, 'title' => 'x']);
        expect(livestreamMethods()->getSessionDetail(5)['title'])->toBe('x');
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'GET', 'get_session_detail')
            && str_contains($r->url(), 'session_id=5'));
    });

    it('updateSession POST body completo', function () {
        livestreamFake('update_session');
        livestreamMethods()->updateSession(5, 'T', 'D', 'https://c/y.jpg');
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'POST', 'update_session')
            && $r['session_id'] === 5 && $r['title'] === 'T' && $r['description'] === 'D'
            && $r['cover_image_url'] === 'https://c/y.jpg' && $r['is_test'] === false);
    });

    it('startSession POST session_id + domain_id + ai_stream', function () {
        livestreamFake('start_session');
        livestreamMethods()->startSession(5, 9, true);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'POST', 'start_session')
            && $r['session_id'] === 5 && $r['domain_id'] === 9 && $r['ai_stream'] === true);
    });

    it('endSession POST session_id', function () {
        livestreamFake('end_session');
        livestreamMethods()->endSession(5);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'POST', 'end_session') && $r['session_id'] === 5);
    });
});

describe('LivestreamMethods metricas', function () {
    it('getSessionMetric GET', function () {
        livestreamFake('get_session_metric', ['views' => 10]);
        expect(livestreamMethods()->getSessionMetric(5)['views'])->toBe(10);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'GET', 'get_session_metric')
            && str_contains($r->url(), 'session_id=5'));
    });

    it('getSessionItemMetric GET paginado', function () {
        livestreamFake('get_session_item_metric', ['list' => []]);
        livestreamMethods()->getSessionItemMetric(5, 40, 20);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'GET', 'get_session_item_metric')
            && str_contains($r->url(), 'session_id=5') && str_contains($r->url(), 'offset=40')
            && str_contains($r->url(), 'page_size=20'));
    });
});

describe('LivestreamMethods sacola', function () {
    it('getItemCount GET', function () {
        livestreamFake('get_item_count', ['count' => 3, 'max_count' => 500]);
        expect(livestreamMethods()->getItemCount(5)['max_count'])->toBe(500);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'GET', 'get_item_count') && str_contains($r->url(), 'session_id=5'));
    });

    it('getLikeItemList GET com keyword opcional', function () {
        livestreamFake('get_like_item_list', ['list' => []]);
        livestreamMethods()->getLikeItemList(0, 10, 'whey');
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'GET', 'get_like_item_list')
            && str_contains($r->url(), 'offset=0') && str_contains($r->url(), 'page_size=10')
            && str_contains($r->url(), 'keyword=whey'));

        livestreamMethods()->getLikeItemList();
        Http::assertSent(fn (Request $r) => str_contains($r->url(), 'get_like_item_list') && ! str_contains($r->url(), 'keyword='));
    });

    it('getRecentItemList GET', function () {
        livestreamFake('get_recent_item_list', ['list' => []]);
        livestreamMethods()->getRecentItemList(20, 20);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'GET', 'get_recent_item_list')
            && str_contains($r->url(), 'offset=20') && str_contains($r->url(), 'page_size=20'));
    });

    it('addItemList / deleteItemList / updateItemList POST item_list com shop_id por item', function () {
        $items = [['item_id' => 11, 'shop_id' => 999999]];
        foreach (['add_item_list' => 'addItemList', 'delete_item_list' => 'deleteItemList', 'update_item_list' => 'updateItemList'] as $path => $method) {
            livestreamFake($path);
            livestreamMethods()->{$method}(5, $items);
            Http::assertSent(fn (Request $r) => livestreamSigned($r, 'POST', $path)
                && $r['session_id'] === 5 && $r['item_list'] === $items);
        }
    });

    it('getItemList GET paginado', function () {
        livestreamFake('get_item_list', ['list' => []]);
        livestreamMethods()->getItemList(5, 0, 50);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'GET', 'get_item_list')
            && str_contains($r->url(), 'session_id=5') && str_contains($r->url(), 'page_size=50'));
    });

    it('getShowItem GET / deleteShowItem POST / updateShowItem POST', function () {
        livestreamFake('get_show_item', ['item_id' => 11]);
        expect(livestreamMethods()->getShowItem(5)['item_id'])->toBe(11);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'GET', 'get_show_item') && str_contains($r->url(), 'session_id=5'));

        livestreamFake('delete_show_item');
        livestreamMethods()->deleteShowItem(5);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'POST', 'delete_show_item') && $r['session_id'] === 5);

        livestreamFake('update_show_item');
        livestreamMethods()->updateShowItem(5, 11, 999999);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'POST', 'update_show_item')
            && $r['session_id'] === 5 && $r['item_id'] === 11 && $r['shop_id'] === 999999);
    });
});

describe('LivestreamMethods conjuntos', function () {
    it('getItemSetList GET com keyword', function () {
        livestreamFake('get_item_set_list', ['list' => []]);
        livestreamMethods()->getItemSetList(0, 20, 'kit');
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'GET', 'get_item_set_list')
            && str_contains($r->url(), 'keyword=kit') && str_contains($r->url(), 'page_size=20'));
    });

    it('getItemSetItemList GET', function () {
        livestreamFake('get_item_set_item_list', ['list' => []]);
        livestreamMethods()->getItemSetItemList(77, 0, 20);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'GET', 'get_item_set_item_list')
            && str_contains($r->url(), 'item_set_id=77'));
    });

    it('applyItemSet POST item_set_ids', function () {
        livestreamFake('apply_item_set');
        livestreamMethods()->applyItemSet(5, [77, 78]);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'POST', 'apply_item_set')
            && $r['session_id'] === 5 && $r['item_set_ids'] === [77, 78]);
    });
});

describe('LivestreamMethods comentarios', function () {
    it('getLatestCommentList GET devolve next_offset', function () {
        livestreamFake('get_latest_comment_list', ['next_offset' => 321, 'list' => [['comment_id' => 1]]]);
        expect(livestreamMethods()->getLatestCommentList(5, 300)['next_offset'])->toBe(321);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'GET', 'get_latest_comment_list')
            && str_contains($r->url(), 'session_id=5') && str_contains($r->url(), 'offset=300'));
    });

    it('postComment POST content', function () {
        livestreamFake('post_comment');
        livestreamMethods()->postComment(5, 'ola');
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'POST', 'post_comment')
            && $r['session_id'] === 5 && $r['content'] === 'ola');
    });

    it('banUserComment / unbanUserComment POST', function () {
        livestreamFake('ban_user_comment');
        livestreamMethods()->banUserComment(5, 123);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'POST', 'ban_user_comment')
            && $r['session_id'] === 5 && $r['ban_user_id'] === 123);

        livestreamFake('unban_user_comment');
        livestreamMethods()->unbanUserComment(5, 123);
        Http::assertSent(fn (Request $r) => livestreamSigned($r, 'POST', 'unban_user_comment')
            && $r['session_id'] === 5 && $r['unban_user_id'] === 123);
    });
});

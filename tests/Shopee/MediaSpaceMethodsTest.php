<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\MediaSpace\MediaSpaceMethods;
use SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeRequestException;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function mediaSpaceMethods(): MediaSpaceMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );

    return new MediaSpaceMethods(HttpClientFactory::make($integration), $integration);
}

function mediaSpaceCommonSigned(Request $req, string $path): bool
{
    $url = $req->url();

    return str_contains($url, $path)
        && str_contains($url, 'partner_id=2030136')
        && preg_match('/[?&]timestamp=\d+/', $url) === 1
        && preg_match('/[?&]sign=[0-9a-f]{64}/', $url) === 1;
}

function mediaSpaceIsPublic(Request $req): bool
{
    return ! str_contains($req->url(), 'shop_id=') && ! str_contains($req->url(), 'access_token=');
}

function mediaSpaceIsShop(Request $req): bool
{
    return str_contains($req->url(), 'shop_id=999999') && str_contains($req->url(), 'access_token=shopee-token');
}

/** Campo simples dentro do multipart. */
function mediaSpaceMultipartField(Request $req, string $name, string $value): bool
{
    foreach ($req->data() as $part) {
        if (($part['name'] ?? null) === $name && (string) ($part['contents'] ?? '') === $value) {
            return true;
        }
    }

    return false;
}

/**
 * Arquivo dentro do multipart. Nao usa Request::hasFile(): o Http::fake grava
 * a request ANTES do Guzzle montar o header multipart, entao isMultipart()
 * devolve false mesmo com attach() — mas data() traz as partes.
 */
function mediaSpaceHasPart(Request $req, string $name, ?string $contents = null, ?string $filename = null): bool
{
    foreach ($req->data() as $part) {
        if (($part['name'] ?? null) !== $name) {
            continue;
        }
        if ($contents !== null && ($part['contents'] ?? null) !== $contents) {
            continue;
        }
        if ($filename !== null && ($part['filename'] ?? null) !== $filename) {
            continue;
        }

        return true;
    }

    return false;
}

beforeEach(function () {
    config(['marketplaces.shopee.base_url' => 'https://partner.shopeemobile.com']);
    Http::preventStrayRequests();
});

it('uploadImage: POST multipart Public com N arquivos "image" + scene', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/media_space/upload_image*' => Http::response([
        'error' => '', 'response' => ['image_info' => ['image_id' => 'img-1'], 'image_info_list' => []],
    ])]);

    $resp = mediaSpaceMethods()->uploadImage([
        ['contents' => 'BYTES-A', 'filename' => 'a.jpg'],
        ['contents' => 'BYTES-B', 'filename' => 'b.png'],
    ], scene: 'desc', ratio: '1:1');

    expect($resp['image_info']['image_id'])->toBe('img-1');

    Http::assertSent(fn (Request $req) => $req->method() === 'POST'
        && mediaSpaceCommonSigned($req, '/api/v2/media_space/upload_image')
        && mediaSpaceIsPublic($req)
                && mediaSpaceHasPart($req, 'image', 'BYTES-A', 'a.jpg')
        && mediaSpaceHasPart($req, 'image', 'BYTES-B', 'b.png')
        && mediaSpaceMultipartField($req, 'scene', 'desc')
        && mediaSpaceMultipartField($req, 'ratio', '1:1'));
});

it('uploadImage: aceita o conteudo de UMA imagem como string', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/media_space/upload_image*' => Http::response(['error' => '', 'response' => []])]);

    mediaSpaceMethods()->uploadImage('RAW', filename: 'capa.jpg');

    Http::assertSent(fn (Request $req) => mediaSpaceHasPart($req, 'image', 'RAW', 'capa.jpg') && mediaSpaceMultipartField($req, 'scene', 'normal'));
});

it('uploadImage: erro de negocio (HTTP 200 + error) lanca ShopeeRequestException', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/media_space/upload_image*' => Http::response(['error' => 'error_param', 'message' => 'bad image'])]);

    mediaSpaceMethods()->uploadImage('RAW');
})->throws(ShopeeRequestException::class);

it('initVideoUpload: POST JSON Shop com file_md5/file_size, devolve video_upload_id', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/media_space/init_video_upload*' => Http::response([
        'error' => '', 'response' => ['video_upload_id' => 'vid-1'],
    ])]);

    expect(mediaSpaceMethods()->initVideoUpload('md5abc', 4096))->toBe('vid-1');

    Http::assertSent(fn (Request $req) => $req->method() === 'POST'
        && mediaSpaceCommonSigned($req, '/api/v2/media_space/init_video_upload')
        && mediaSpaceIsShop($req)
        && $req['file_md5'] === 'md5abc' && $req['file_size'] === 4096);
});

it('uploadVideoPart: POST multipart Public com part_content + part_seq + content_md5', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/media_space/upload_video_part*' => Http::response(['error' => '', 'response' => []])]);

    mediaSpaceMethods()->uploadVideoPart('vid-1', 2, 'md5part', 'CHUNK');

    Http::assertSent(fn (Request $req) => $req->method() === 'POST'
        && mediaSpaceCommonSigned($req, '/api/v2/media_space/upload_video_part')
        && mediaSpaceIsPublic($req)
        && mediaSpaceHasPart($req, 'part_content', 'CHUNK')
        && mediaSpaceMultipartField($req, 'video_upload_id', 'vid-1')
        && mediaSpaceMultipartField($req, 'part_seq', '2')
        && mediaSpaceMultipartField($req, 'content_md5', 'md5part'));
});

it('completeVideoUpload: POST JSON Public com part_seq_list e report_data.upload_cost', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/media_space/complete_video_upload*' => Http::response(['error' => '', 'response' => []])]);

    mediaSpaceMethods()->completeVideoUpload('vid-1', [0, 1, 2], 1500);

    Http::assertSent(fn (Request $req) => $req->method() === 'POST'
        && mediaSpaceCommonSigned($req, '/api/v2/media_space/complete_video_upload')
        && mediaSpaceIsPublic($req)
        && $req['video_upload_id'] === 'vid-1'
        && $req['part_seq_list'] === [0, 1, 2]
        && $req['report_data'] === ['upload_cost' => 1500]);
});

it('getVideoUploadResult: GET Shop com video_upload_id', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/media_space/get_video_upload_result*' => Http::response([
        'error' => '', 'response' => ['status' => 'SUCCEEDED', 'video_info' => ['duration' => 10]],
    ])]);

    expect(mediaSpaceMethods()->getVideoUploadResult('vid-1')['status'])->toBe('SUCCEEDED');

    Http::assertSent(fn (Request $req) => $req->method() === 'GET'
        && mediaSpaceCommonSigned($req, '/api/v2/media_space/get_video_upload_result')
        && mediaSpaceIsShop($req)
        && str_contains($req->url(), 'video_upload_id=vid-1'));
});

it('cancelVideoUpload: POST Shop com video_upload_id', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/media_space/cancel_video_upload*' => Http::response(['error' => '', 'request_id' => 'r1'])]);

    expect(mediaSpaceMethods()->cancelVideoUpload('vid-1'))->toBe(['error' => '', 'request_id' => 'r1']);

    Http::assertSent(fn (Request $req) => $req->method() === 'POST'
        && mediaSpaceCommonSigned($req, '/api/v2/media_space/cancel_video_upload')
        && mediaSpaceIsShop($req)
        && $req['video_upload_id'] === 'vid-1');
});

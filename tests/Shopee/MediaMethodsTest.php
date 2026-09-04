<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Endpoints\Media\MediaMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function mediaMethods(): MediaMethods
{
    $integration = new FakeIntegration(
        accessToken: 'shopee-token',
        refreshToken: 'rt',
        settings: ['partner_id' => 2030136, 'partner_key' => 'fake-key', 'shop_id' => 999999, 'merchant_id' => 777],
        active: true,
        expired: false,
    );

    return new MediaMethods(HttpClientFactory::make($integration), $integration);
}

/** Assinatura Public: partner_id + timestamp + sign, SEM shop_id/access_token. */
function mediaPublicSigned(Request $req, string $path): bool
{
    $url = $req->url();

    return str_contains($url, $path)
        && str_contains($url, 'partner_id=2030136')
        && preg_match('/[?&]timestamp=\d+/', $url) === 1
        && preg_match('/[?&]sign=[0-9a-f]{64}/', $url) === 1
        && ! str_contains($url, 'shop_id=')
        && ! str_contains($url, 'access_token=');
}

function mediaMultipartField(Request $req, string $name, string $value): bool
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
function mediaHasPart(Request $req, string $name, ?string $contents = null, ?string $filename = null): bool
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

it('uploadImage: POST multipart Public com business/scene + arquivos "images", devolve image_list', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/media/upload_image*' => Http::response([
        'error' => '', 'response' => ['image_list' => [['image_id' => 'i1', 'image_url' => 'u1']]],
    ])]);

    $list = mediaMethods()->uploadImage(2, 1, [['contents' => 'PIC', 'filename' => 'p.jpg']]);

    expect($list)->toBe([['image_id' => 'i1', 'image_url' => 'u1']]);

    Http::assertSent(fn (Request $req) => $req->method() === 'POST'
        && mediaPublicSigned($req, '/api/v2/media/upload_image')
        && mediaHasPart($req, 'images', 'PIC', 'p.jpg')
        && mediaMultipartField($req, 'business', '2')
        && mediaMultipartField($req, 'scene', '1'));
});

it('initVideoUpload: POST JSON Public com business/scene/file_name/file_size/duration', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/media/init_video_upload*' => Http::response([
        'error' => '', 'response' => ['video_upload_id' => 'v1', 'part_size' => 5242880],
    ])]);

    $resp = mediaMethods()->initVideoUpload(3, 1, 'clip.mp4', 10485760, 30);

    expect($resp['part_size'])->toBe(5242880);

    Http::assertSent(fn (Request $req) => $req->method() === 'POST'
        && mediaPublicSigned($req, '/api/v2/media/init_video_upload')
        && $req['business'] === 3 && $req['scene'] === 1
        && $req['file_name'] === 'clip.mp4' && $req['file_size'] === 10485760 && $req['duration'] === 30);
});

it('uploadVideoPart: POST multipart Public com part_content + part_md5', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/media/upload_video_part*' => Http::response(['error' => '', 'response' => []])]);

    mediaMethods()->uploadVideoPart('v1', 0, 'md5x', 'CHUNK');

    Http::assertSent(fn (Request $req) => $req->method() === 'POST'
        && mediaPublicSigned($req, '/api/v2/media/upload_video_part')
        && mediaHasPart($req, 'part_content', 'CHUNK')
        && mediaMultipartField($req, 'video_upload_id', 'v1')
        && mediaMultipartField($req, 'part_seq', '0')
        && mediaMultipartField($req, 'part_md5', 'md5x'));
});

it('completeVideoUpload: POST JSON Public so com video_upload_id', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/media/complete_video_upload*' => Http::response(['error' => '', 'response' => []])]);

    mediaMethods()->completeVideoUpload('v1');

    Http::assertSent(fn (Request $req) => $req->method() === 'POST'
        && mediaPublicSigned($req, '/api/v2/media/complete_video_upload')
        && $req->data() === ['video_upload_id' => 'v1']);
});

it('getVideoUploadResult: GET Public com video_upload_id', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/media/get_video_upload_result*' => Http::response([
        'error' => '', 'response' => ['status' => 'SUCCEEDED', 'reason' => '', 'video_info' => []],
    ])]);

    expect(mediaMethods()->getVideoUploadResult('v1')['status'])->toBe('SUCCEEDED');

    Http::assertSent(fn (Request $req) => $req->method() === 'GET'
        && mediaPublicSigned($req, '/api/v2/media/get_video_upload_result')
        && str_contains($req->url(), 'video_upload_id=v1'));
});

it('cancelVideoUpload: POST Public com video_upload_id', function () {
    Http::fake(['partner.shopeemobile.com/api/v2/media/cancel_video_upload*' => Http::response(['error' => '', 'request_id' => 'r'])]);

    mediaMethods()->cancelVideoUpload('v1');

    Http::assertSent(fn (Request $req) => $req->method() === 'POST'
        && mediaPublicSigned($req, '/api/v2/media/cancel_video_upload')
        && $req['video_upload_id'] === 'v1');
});

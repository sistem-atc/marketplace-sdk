<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Message\MessageMethods;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function messageActionGuideMethods(): MessageMethods
{
    $integration = new FakeIntegration(accessToken: 'ml-bearer', refreshToken: 'rt', settings: ['client_id' => 'cli', 'client_secret' => 'sec'], active: true, expired: false);

    return new MessageMethods(HttpClientFactory::make($integration), $integration);
}

function messageActionGuideAssertSent(string $method, string $urlPart, ?callable $extra = null): void
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

describe('MessageMethods — action guide', function () {
    it('actionGuide e caps_available usam tag=post_sale', function () {
        $m = messageActionGuideMethods();
        $m->actionGuide(20000000000);
        messageActionGuideAssertSent('GET', '/messages/action_guide/packs/20000000000?tag=post_sale');
        $m->actionGuideCapsAvailable(20000000000);
        messageActionGuideAssertSent('GET', '/messages/action_guide/packs/20000000000/caps_available?tag=post_sale');
    });

    it('sendActionGuideOption com template', function () {
        messageActionGuideMethods()->sendActionGuideOption(2000, 'REQUEST_BILLING_INFO', 'TEMPLATE___REQUEST_BILLING_INFO___1');
        messageActionGuideAssertSent('POST', '/messages/action_guide/packs/2000/option?tag=post_sale', fn ($r) => $r->data() === ['option_id' => 'REQUEST_BILLING_INFO', 'template_id' => 'TEMPLATE___REQUEST_BILLING_INFO___1']);
    });

    it('sendActionGuideOption OTHER com texto', function () {
        messageActionGuideMethods()->sendActionGuideOption(2000, 'OTHER', null, 'Ola Maria');
        messageActionGuideAssertSent('POST', '/messages/action_guide/packs/2000/option', fn ($r) => $r->data() === ['option_id' => 'OTHER', 'text' => 'Ola Maria']);
    });
});

describe('MessageMethods — anexos e nao lidas', function () {
    it('uploadAttachment envia multipart com tag e site_id', function () {
        messageActionGuideMethods()->uploadAttachment('img', 'Anexo.jpg', 'MLB');
        messageActionGuideAssertSent('POST', '/messages/attachments?tag=post_sale&site_id=MLB', fn ($r) => $r->hasFile('file', 'img', 'Anexo.jpg'));
    });

    it('getAttachment manda tag e site_id opcional', function () {
        messageActionGuideMethods()->getAttachment('76601286_abc.png', 'MLB');
        messageActionGuideAssertSent('GET', '/messages/attachments/76601286_abc.png', fn ($r) => str_contains($r->url(), 'tag=post_sale') && str_contains($r->url(), 'site_id=MLB'));
    });

    it('unreadForPack e unreadByRole', function () {
        $m = messageActionGuideMethods();
        $m->unreadForPack(1234, 2345);
        messageActionGuideAssertSent('GET', '/messages/unread/packs/1234/sellers/2345?tag=post_sale');
        $m->unreadByRole('seller');
        messageActionGuideAssertSent('GET', '/messages/unread?role=seller&tag=post_sale');
    });
});

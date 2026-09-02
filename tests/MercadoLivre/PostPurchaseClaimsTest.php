<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Claim\ClaimMethods;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function postPurchaseClaimsMethods(): ClaimMethods
{
    $integration = new FakeIntegration(accessToken: 'ml-bearer', refreshToken: 'rt', settings: ['client_id' => 'cli', 'client_secret' => 'sec'], active: true, expired: false);

    return new ClaimMethods(HttpClientFactory::make($integration), $integration);
}

function postPurchaseClaimsAssertSent(string $method, string $urlPart, ?callable $extra = null): void
{
    Http::assertSent(fn (Request $req) => $req->method() === $method
        && str_contains($req->url(), $urlPart)
        && $req->hasHeader('Authorization', 'Bearer ml-bearer')
        && ($extra === null || $extra($req)));
}

beforeEach(function () {
    config(['marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com', 'mercadolivre.api_base' => 'https://api.mercadolibre.com', 'mercadolivre.access_token_ttl_seconds' => 21600, 'mercadolivre.default_site_id' => 'MLB']);
    Http::preventStrayRequests();
});

function postPurchaseClaimsFakeOk(): void
{
    Http::fake(['api.mercadolibre.com/*' => Http::response(['ok' => true])]);
}

describe('ClaimMethods — /post-purchase/v1/claims (consulta)', function () {
    it('searchV1 manda os filtros na query', function () {
        postPurchaseClaimsFakeOk();
        postPurchaseClaimsMethods()->searchV1(['players.user_id' => 123, 'players.role' => 'respondent', 'status' => 'opened', 'limit' => 30]);
        postPurchaseClaimsAssertSent('GET', '/post-purchase/v1/claims/search', fn ($r) => str_contains($r->url(), 'players.user_id=123')
            && str_contains($r->url(), 'players.role=respondent') && str_contains($r->url(), 'status=opened') && str_contains($r->url(), 'limit=30'));
    });

    it('getV1 / detail / actionsHistory / statusHistory / affectsReputation / messagesV1 / evidences / expectedResolutions / partialRefundAvailableOffers', function () {
        postPurchaseClaimsFakeOk();
        $m = postPurchaseClaimsMethods();
        $m->getV1(5281510459);
        postPurchaseClaimsAssertSent('GET', '/post-purchase/v1/claims/5281510459');
        $m->detail(5281510459);
        postPurchaseClaimsAssertSent('GET', '/post-purchase/v1/claims/5281510459/detail');
        $m->actionsHistory(5281510459);
        postPurchaseClaimsAssertSent('GET', '/post-purchase/v1/claims/5281510459/actions-history');
        $m->statusHistory(5281510459);
        postPurchaseClaimsAssertSent('GET', '/post-purchase/v1/claims/5281510459/status-history');
        $m->affectsReputation(5281510459);
        postPurchaseClaimsAssertSent('GET', '/post-purchase/v1/claims/5281510459/affects-reputation');
        $m->messagesV1(5281510459);
        postPurchaseClaimsAssertSent('GET', '/post-purchase/v1/claims/5281510459/messages');
        $m->evidences(5281510459);
        postPurchaseClaimsAssertSent('GET', '/post-purchase/v1/claims/5281510459/evidences');
        $m->expectedResolutions(5281510459);
        postPurchaseClaimsAssertSent('GET', '/post-purchase/v1/claims/5281510459/expected-resolutions');
        $m->partialRefundAvailableOffers(5281510459);
        postPurchaseClaimsAssertSent('GET', '/post-purchase/v1/claims/5281510459/partial-refund/available-offers');
    });

    it('reason manda o id e a query (deep/flow)', function () {
        postPurchaseClaimsFakeOk();
        postPurchaseClaimsMethods()->reason('PDD9939', ['deep' => 'true', 'flow' => 'mediations']);
        postPurchaseClaimsAssertSent('GET', '/post-purchase/v1/claims/reasons/PDD9939', fn ($r) => str_contains($r->url(), 'deep=true') && str_contains($r->url(), 'flow=mediations'));
    });
});

describe('ClaimMethods — mensagens e anexos', function () {
    it('sendMessageV1 posta receiver_role, message e attachments', function () {
        postPurchaseClaimsFakeOk();
        postPurchaseClaimsMethods()->sendMessageV1(5204934310, 'Ola', 'complainant', ['abc_1.jpg']);
        postPurchaseClaimsAssertSent('POST', '/post-purchase/v1/claims/5204934310/actions/send-message', fn ($r) => $r->data() === ['receiver_role' => 'complainant', 'message' => 'Ola', 'attachments' => ['abc_1.jpg']]);
    });

    it('uploadAttachment envia multipart file', function () {
        postPurchaseClaimsFakeOk();
        postPurchaseClaimsMethods()->uploadAttachment(5204934310, 'bytes', 'foto.jpg');
        postPurchaseClaimsAssertSent('POST', '/post-purchase/v1/claims/5204934310/attachments', fn ($r) => $r->hasFile('file', 'bytes', 'foto.jpg'));
    });

    it('attachmentInfo e downloadAttachment usam o id do arquivo', function () {
        postPurchaseClaimsFakeOk();
        $m = postPurchaseClaimsMethods();
        $m->attachmentInfo(555, '1325224382_abc.jpg');
        postPurchaseClaimsAssertSent('GET', '/post-purchase/v1/claims/555/attachments/1325224382_abc.jpg');
        $body = $m->downloadAttachment(555, '1325224382_abc.jpg');
        expect($body)->toBe('{"ok":true}');
        postPurchaseClaimsAssertSent('GET', '/post-purchase/v1/claims/555/attachments/1325224382_abc.jpg/download');
    });

    it('downloadAttachment lanca excecao em erro HTTP', function () {
        Http::fake(['api.mercadolibre.com/*' => Http::response('', 404)]);
        postPurchaseClaimsMethods()->downloadAttachment(555, 'x.jpg');
    })->throws(MercadoLivreRequestException::class);
});

describe('ClaimMethods — evidencias', function () {
    it('addEvidence posta no /evidences', function () {
        postPurchaseClaimsFakeOk();
        postPurchaseClaimsMethods()->addEvidence(949903019, ['type' => 'handling_shipping_evidence', 'handling_date' => '2024-06-13']);
        postPurchaseClaimsAssertSent('POST', '/post-purchase/v1/claims/949903019/evidences', fn ($r) => $r->data()['handling_date'] === '2024-06-13');
    });

    it('submitEvidence posta no /actions/evidences', function () {
        postPurchaseClaimsFakeOk();
        postPurchaseClaimsMethods()->submitEvidence(949903015, ['type' => 'shipping_evidence', 'tracking_number' => 'XX1']);
        postPurchaseClaimsAssertSent('POST', '/post-purchase/v1/claims/949903015/actions/evidences', fn ($r) => $r->data()['tracking_number'] === 'XX1');
    });

    it('uploadEvidenceAttachment envia multipart + x-public quando pedido', function () {
        postPurchaseClaimsFakeOk();
        postPurchaseClaimsMethods()->uploadEvidenceAttachment(5123456, 'png', 'pendrive.png', true);
        postPurchaseClaimsAssertSent('POST', '/post-purchase/v1/claims/5123456/attachments-evidences', fn ($r) => $r->hasFile('file', 'png', 'pendrive.png') && $r->hasHeader('x-public', 'true'));
    });

    it('evidenceAttachmentInfo e downloadEvidenceAttachment', function () {
        postPurchaseClaimsFakeOk();
        $m = postPurchaseClaimsMethods();
        $m->evidenceAttachmentInfo(5123456, 'abcdef.png');
        postPurchaseClaimsAssertSent('GET', '/post-purchase/v1/claims/5123456/attachments-evidences/abcdef.png');
        expect($m->downloadEvidenceAttachment(5123456, 'abcdef.png'))->toBe('{"ok":true}');
        postPurchaseClaimsAssertSent('GET', '/post-purchase/v1/claims/5123456/attachments-evidences/abcdef.png/download');
    });
});

describe('ClaimMethods — resolucao', function () {
    it('offerPartialRefund posta percentage', function () {
        postPurchaseClaimsFakeOk();
        postPurchaseClaimsMethods()->offerPartialRefund(5204934310, 50);
        postPurchaseClaimsAssertSent('POST', '/post-purchase/v1/claims/5204934310/expected-resolutions/partial-refund', fn ($r) => $r->data() === ['percentage' => 50]);
    });

    it('refund / allowReturn / openDispute sao POST sem body', function () {
        postPurchaseClaimsFakeOk();
        $m = postPurchaseClaimsMethods();
        $m->refund(1);
        postPurchaseClaimsAssertSent('POST', '/post-purchase/v1/claims/1/expected-resolutions/refund');
        $m->allowReturn(1);
        postPurchaseClaimsAssertSent('POST', '/post-purchase/v1/claims/1/expected-resolutions/allow-return');
        $m->openDispute(1);
        postPurchaseClaimsAssertSent('POST', '/post-purchase/v1/claims/1/actions/open-dispute');
    });
});

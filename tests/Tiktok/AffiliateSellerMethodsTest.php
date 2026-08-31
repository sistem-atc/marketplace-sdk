<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttAffSellerIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'access',
        refreshToken: 'rt',
        settings: [
            'app_key' => 'ak',
            'app_secret' => 'as',
            'shop_id' => 'shop1',
            'shop_cipher' => 'cipher1',
        ],
        active: true,
        expired: false,
    );
}

function ttAffSeller()
{
    return MarketPlaces::Tiktok()->affiliateSeller(ttAffSellerIntegration());
}

describe('paths que a doc escreve errado — e que precisam continuar errados', function () {
    it('chama /202412/conversatons/read com o typo da API', function () {
        Http::fake(['open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'data' => []])]);

        ttAffSeller()->markConversationsRead(['c1', 'c2']);

        // "conversatons" sem o segundo `i`: corrigir aqui derruba a chamada.
        Http::assertSent(fn ($req) => str_contains($req->url(), '/affiliate_seller/202412/conversatons/read'));
    });

    it('usa target_collaboration no SINGULAR so pra gerar o link', function () {
        Http::fake(['open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'data' => ['link' => 'https://x']])]);

        ttAffSeller()->generateTargetCollaborationLink('TC1');

        Http::assertSent(fn ($req) => str_contains($req->url(), '/affiliate_seller/202509/target_collaboration/TC1/link'));
    });

    it('usa conversation no SINGULAR pra ler e no PLURAL pra enviar', function () {
        Http::fake(['open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'data' => []])]);

        ttAffSeller()->getConversationMessages('CV1');
        ttAffSeller()->sendImMessage('CV1', 'TEXT', '{"content":"oi"}');

        Http::assertSent(fn ($req) => str_contains($req->url(), '/202412/conversation/CV1/messages') && $req->method() === 'GET');
        Http::assertSent(fn ($req) => str_contains($req->url(), '/202412/conversations/CV1/messages') && $req->method() === 'POST');
    });

    it('manda os filtros de amostra nos nomes com typo que a API espera', function () {
        Http::fake(['open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'data' => []])]);

        ttAffSeller()->searchSampleApplications(
            creatorUserOpenId: 'OPEN1',
            targetCollaborationId: 'TC1',
        );

        Http::assertSent(function ($req) {
            // "oepn" e "collabration": corrigir = filtro silenciosamente ignorado.
            return str_contains($req->body(), '"creator_user_oepn_id":"OPEN1"')
                && str_contains($req->body(), '"target_collabration_id":"TC1"');
        });
    });
});

describe('paginacao', function () {
    it('OMITE page_token na primeira pagina em vez de mandar string vazia', function () {
        Http::fake(['open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'data' => []])]);

        ttAffSeller()->searchAffiliateOrders(pageSize: 50);

        Http::assertSent(fn ($req) => str_contains($req->url(), 'page_size=50')
            && ! str_contains($req->url(), 'page_token'));
    });

    it('manda page_token quando ha cursor', function () {
        Http::fake(['open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'data' => []])]);

        ttAffSeller()->searchAffiliateOrders(pageToken: 'CURSOR');

        Http::assertSent(fn ($req) => str_contains($req->url(), 'page_token=CURSOR'));
    });

    it('pagina os registros de abordagem por INDICE, nao por cursor', function () {
        Http::fake(['open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'data' => []])]);

        ttAffSeller()->getWeeklyCreatorOutreachRecords(pageIndex: 3, pageSize: 50);

        Http::assertSent(fn ($req) => str_contains($req->body(), '"page_index":3')
            && ! str_contains($req->url(), 'page_token'));
    });

    it('recusa page_index 0 — a rota e base 1', function () {
        Http::fake();

        expect(fn () => ttAffSeller()->getWeeklyCreatorOutreachRecords(pageIndex: 0))
            ->toThrow(InvalidArgumentException::class);
    });
});

describe('guardas que evitam 400 da API', function () {
    it('recusa page_size fora de 20/50/100 na busca de convites dirigidos', function () {
        Http::fake();

        expect(fn () => ttAffSeller()->searchTargetCollaborations('VALID', pageSize: 30))
            ->toThrow(InvalidArgumentException::class);
    });

    it('recusa page_size fora de 12/20 na busca de creators', function () {
        Http::fake();

        expect(fn () => ttAffSeller()->searchMarketplaceCreators(pageSize: 50))
            ->toThrow(InvalidArgumentException::class);
    });

    it('recusa mais de 20 conversas por marcacao de leitura', function () {
        Http::fake();

        expect(fn () => ttAffSeller()->markConversationsRead(range(1, 21)))
            ->toThrow(InvalidArgumentException::class);
    });

    it('recusa convite dirigido com mais de 50 creators ou 100 produtos', function () {
        Http::fake();

        expect(fn () => ttAffSeller()->createTargetCollaboration(
            'n', 1, [], array_fill(0, 51, 'C'), ['email' => 'a@b.c'],
        ))->toThrow(InvalidArgumentException::class);

        expect(fn () => ttAffSeller()->createTargetCollaboration(
            'n', 1, array_fill(0, 101, ['id' => 'P']), [], ['email' => 'a@b.c'],
        ))->toThrow(InvalidArgumentException::class);
    });

    it('exige reject_reason quando o parecer da amostra e REJECT', function () {
        Http::fake();

        expect(fn () => ttAffSeller()->reviewSampleApplication('A1', 'REJECT'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('nao chama a API quando a lista de product_ids das regras de amostra vem vazia', function () {
        Http::fake();

        $r = ttAffSeller()->getOpenCollaborationSampleRules([]);

        expect($r->sampleRules)->toBeNull();
        Http::assertNothingSent();
    });
});

describe('serializacao do corpo', function () {
    it('manda product_ids como lista separada por virgula na query, nao como array', function () {
        Http::fake(['open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'data' => []])]);

        ttAffSeller()->getOpenCollaborationSampleRules(['P1', 'P2']);

        Http::assertSent(fn ($req) => str_contains(urldecode($req->url()), 'product_ids=P1,P2'));
    });

    it('manda end_time do convite dirigido como STRING, como a doc declara', function () {
        Http::fake(['open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'data' => []])]);

        ttAffSeller()->createTargetCollaboration(
            name: 'Campanha',
            endTime: 1715654330,
            products: [['id' => 'P1', 'target_commission_rate' => 1500]],
            creatorUserOpenIds: ['OPEN1'],
            sellerContactInfo: ['email' => 'a@b.c'],
        );

        Http::assertSent(fn ($req) => str_contains($req->body(), '"end_time":"1715654330"'));
    });

    it('empacota auto_add_product ao editar as configuracoes da colaboracao aberta', function () {
        Http::fake(['open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'data' => []])]);

        ttAffSeller()->editOpenCollaborationSettings(enable: true, commissionRate: 1000);

        Http::assertSent(fn ($req) => str_contains($req->body(), '"auto_add_product":{"enable":true,"commission_rate":1000}'));
    });

    it('costura activate_status dentro do sample_rule', function () {
        Http::fake(['open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'data' => []])]);

        ttAffSeller()->editOpenCollaborationSampleRule('P1', 'DEACTIVATE');

        Http::assertSent(fn ($req) => str_contains($req->body(), '"product_id":"P1"')
            && str_contains($req->body(), '"activate_status":"DEACTIVATE"'));
    });

    it('converte os tipos de opcao de filtro em objetos option_type', function () {
        Http::fake(['open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'data' => []])]);

        ttAffSeller()->getMarketplaceCreatorSearchFilters(['OPTION_LANGUAGE', 'OPTION_BRAND']);

        Http::assertSent(fn ($req) => str_contains(
            $req->body(),
            '"options":[{"option_type":"OPTION_LANGUAGE"},{"option_type":"OPTION_BRAND"}]',
        ));
    });

    it('injeta o searchKey no corpo da busca de creators', function () {
        Http::fake(['open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'data' => []])]);

        ttAffSeller()->searchMarketplaceCreators(['keyword' => 'fit'], searchKey: 'KEY1', pageSize: 12);

        Http::assertSent(fn ($req) => str_contains($req->body(), '"search_key":"KEY1"'));
    });
});

describe('uploads de imagem de mensagem', function () {
    it('sobe multipart via POST apesar de a doc declarar GET', function () {
        Http::fake([
            'open-api.tiktokglobalshop.com/*' => Http::response([
                'code' => 0,
                'data' => ['url' => 'https://cdn/x.jpg', 'width' => 1280, 'height' => 720],
            ]),
        ]);

        $r = ttAffSeller()->uploadMessageImageV2('binario', 'foto.jpg');

        expect($r->url)->toBe('https://cdn/x.jpg')
            ->and($r->width)->toBe(1280);

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && str_contains($req->url(), '/affiliate_seller/202608/media/upload'));
    });

    it('mantem a rota antiga de imagem em /202511/images/upload', function () {
        Http::fake(['open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'data' => ['url' => 'https://cdn/y.jpg']])]);

        ttAffSeller()->uploadMessageImage('binario');

        Http::assertSent(fn ($req) => str_contains($req->url(), '/affiliate_seller/202511/images/upload'));
    });
});

describe('endpoints de data vazio devolvem DTO tipado, nao array', function () {
    it('confirma a acao sem payload', function () {
        Http::fake(['open-api.tiktokglobalshop.com/*' => Http::response(['code' => 0, 'data' => []])]);

        expect(ttAffSeller()->removeTargetCollaboration('TC1')->toArray())->toBe([])
            ->and(ttAffSeller()->updateConversationStatus('CV1', 'ARCHIVED')->toArray())->toBe([])
            ->and(ttAffSeller()->reviewSampleApplication('A1', 'APPROVE')->toArray())->toBe([])
            ->and(ttAffSeller()->removeCreatorFromOpenCollaboration('OC1', 'OPEN1', 'P1')->toArray())->toBe([]);
    });
});

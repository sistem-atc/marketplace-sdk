<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttPartnerIntegration(): FakeIntegration
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

function apOk(array $data = []): array
{
    return ['code' => 0, 'message' => 'Success', 'data' => $data];
}

describe('MarketPlaces::Tiktok()->affiliatePartner()', function () {
    it('manda o category_asset_cipher na QUERY, ao lado do shop_cipher da loja', function () {
        Http::fake(['*' => Http::response(apOk(['campaigns' => []]))]);

        MarketPlaces::Tiktok()->affiliatePartner(ttPartnerIntegration())
            ->getCampaigns(categoryAssetCipher: 'PARTNER_CIPHER');

        // Os dois convivem: `shop_cipher` e' injetado pelo BaseMethods (loja) e
        // `category_asset_cipher` identifica o PARCEIRO. Confundir os dois da'
        // erro de permissao sem mensagem util.
        Http::assertSent(fn ($req) => str_contains($req->url(), 'category_asset_cipher=PARTNER_CIPHER')
            && str_contains($req->url(), 'shop_cipher=cipher1'));
    });

    it('cada endpoint carrega a versao da SUA doc, nao uma versao global', function () {
        Http::fake(['*' => Http::response(apOk())]);

        $api = MarketPlaces::Tiktok()->affiliatePartner(ttPartnerIntegration());
        $api->getCampaignDetail('C', '7360');                          // 202405
        $api->getCampaignProductsPerformance('7360');                  // 202501
        $api->generateProductPromotionLinks('7360', ['p1']);           // 202505
        $api->getCreatorSampleStatus('C', '7360', 'p1', 'tmp');        // 202508
        $api->searchTapOrders('C');                                    // 202603

        $urls = [];
        Http::assertSent(function ($req) use (&$urls) {
            $urls[] = $req->url();

            return true;
        });

        expect($urls[0])->toContain('/affiliate_partner/202405/campaigns/7360')
            ->and($urls[1])->toContain('/affiliate_partner/202501/')
            ->and($urls[2])->toContain('/affiliate_partner/202505/')
            ->and($urls[3])->toContain('/affiliate_partner/202508/')
            ->and($urls[4])->toContain('/affiliate_partner/202603/orders/search');
    });

    it('recusa comissao fora da faixa ANTES de gastar a chamada', function () {
        Http::fake(['*' => Http::response(apOk())]);

        // 8500 = 85%, acima do teto de 8000 do TikTok. O erro da API e'
        // generico; falhar aqui diz o que esta' errado.
        expect(fn () => MarketPlaces::Tiktok()->affiliatePartner(ttPartnerIntegration())->createCampaign(
            categoryAssetCipher: 'C',
            name: 'x',
            description: 'y',
            campaignStartTime: 1,
            campaignEndTime: 2,
            registrationStartTime: 1,
            registrationEndTime: 2,
            commissionRate: 8500,
            contactInfo: ['email' => 'a@b.c'],
        ))->toThrow(InvalidArgumentException::class);

        Http::assertNothingSent();
    });

    it('recusa lote de link acima de 50 produtos', function () {
        Http::fake(['*' => Http::response(apOk())]);

        expect(fn () => MarketPlaces::Tiktok()->affiliatePartner(ttPartnerIntegration())
            ->generateProductPromotionLinks('7360', array_fill(0, 51, 'p')))
            ->toThrow(InvalidArgumentException::class);

        Http::assertNothingSent();
    });
});

describe('MarketPlaces::Tiktok()->customerEngagement()', function () {
    it('manda a chave de idempotencia na QUERY, nao no body', function () {
        Http::fake(['*' => Http::response(apOk(['task_id' => '475910475643']))]);

        $dto = MarketPlaces::Tiktok()->customerEngagement(ttPartnerIntegration())->createTask(
            idempotencyKey: 'uuid-v4-aqui',
            templateId: 'T1',
            taskName: 'reengajamento',
            endTime: 1770000000,
        );

        expect($dto->taskId)->toBe('475910475643');

        // Se fosse no body a assinatura mudaria a cada retry e a protecao
        // contra duplicata nao valeria de nada.
        Http::assertSent(fn ($req) => str_contains($req->url(), 'idempotency_key=uuid-v4-aqui')
            && ! str_contains($req->body(), 'idempotency_key'));
    });

    it('task com texto proprio vai pra rota /custom e na versao 202502', function () {
        Http::fake(['*' => Http::response(apOk(['task_id' => '1']))]);

        MarketPlaces::Tiktok()->customerEngagement(ttPartnerIntegration())->createCustomTask(
            idempotencyKey: 'k',
            taskName: 'promo',
            endTime: 1770000000,
            title: 'Oi',
            body: 'Volta pra loja',
        );

        Http::assertSent(fn ($req) => str_contains($req->url(), '/customer_engagement/202502/engagement_tasks/custom')
            && str_contains($req->body(), '"custom_message"')
            && str_contains($req->body(), '"title":"Oi"'));
    });
});

describe('MarketPlaces::Tiktok()->customerService()', function () {
    it('respeita o teto de 10 mensagens por pagina mesmo pedindo mais', function () {
        Http::fake(['*' => Http::response(apOk(['messages' => []]))]);

        MarketPlaces::Tiktok()->customerService(ttPartnerIntegration())
            ->getConversationMessages('7494', pageSize: 100);

        // Teto do endpoint de mensagens e' 10 (o de conversas e' 20). Pedir 100
        // faz o TikTok recusar a chamada inteira.
        Http::assertSent(fn ($req) => str_contains($req->url(), 'page_size=10'));
    });

    it('o envio de mensagem usa a versao 202606, nao a 202309 do resto', function () {
        Http::fake(['*' => Http::response(apOk(['message_id' => '749']))]);

        $dto = MarketPlaces::Tiktok()->customerService(ttPartnerIntegration())
            ->sendText('7494', 'ola', senderRole: 'CUSTOMER_SERVICE');

        expect($dto->messageId)->toBe('749');

        Http::assertSent(fn ($req) => str_contains($req->url(), '/customer_service/202606/conversations/7494/messages')
            && str_contains($req->body(), '"sender_role":"CUSTOMER_SERVICE"')
            // O texto vai EMBRULHADO em JSON dentro de `content`.
            && str_contains($req->body(), 'content'));
    });
});

describe('MarketPlaces::Tiktok()->supplyChain() e ->open()', function () {
    it('confirmPackageShipment posta o provedor de armazem junto do lote', function () {
        Http::fake(['*' => Http::response(apOk(['success_packages' => ['1']]))]);

        $dto = MarketPlaces::Tiktok()->supplyChain(ttPartnerIntegration())
            ->confirmPackageShipment('WP1', [['id' => '1', 'ship_time_millis' => 1700000000000]]);

        expect($dto->successPackages)->toBe(['1']);

        Http::assertSent(fn ($req) => str_contains($req->url(), '/supply_chain/202309/packages/sync')
            && str_contains($req->body(), '"warehouse_provider_id":"WP1"')
            // MILISSEGUNDOS: 13 digitos passam intactos, sem virar segundos.
            && str_contains($req->body(), '1700000000000'));
    });

    it('uploadFileInit so abre a sessao — o arquivo vai depois pra outra url', function () {
        Http::fake(['*' => Http::response(apOk([
            'upload_url' => 'https://open-api.tiktokglobalshop.com/file/202512/upload',
            'upload_token' => 'tok',
        ]))]);

        $dto = MarketPlaces::Tiktok()->open(ttPartnerIntegration())
            ->uploadFileInit('v.mp4', 'video', 1024, 2, '/target');

        expect($dto->uploadToken)->toBe('tok');

        Http::assertSent(fn ($req) => str_contains($req->url(), '/open/202512/file/init')
            && str_contains($req->body(), '"total_chunk_count":2'));
    });
});

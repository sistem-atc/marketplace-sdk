<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\AffiliateOrderSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CompassTaskCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CompassTaskFileResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CompassTaskListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\ConversationCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\ConversationListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\ConversationMessageListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CreatorContentDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CreatorMarketplaceFiltersResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CreatorOutreachQuotaResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CreatorOutreachRecordsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CreatorPromotionDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\InboxCategoryCountsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\LatestUnreadMessagesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\MarkConversationReadResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\MarketplaceCreatorPerformanceResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\MarketplaceCreatorSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\MessageImageUploadResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\OpenCollaborationCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\OpenCollaborationRemoveResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\OpenCollaborationSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\OpenCollaborationSettingsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\AffiliateProductSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\ProductPromotionLinkResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\SampleApplicationSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\SampleFulfillmentSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\SampleRequestDeeplinkResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\SampleRuleListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\SendImMessageResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\TargetCollaborationConflictCheckResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\TargetCollaborationConflictResolveResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\TargetCollaborationCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\TargetCollaborationDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\TargetCollaborationLinkResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\TargetCollaborationSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\TargetCollaborationUpdateResponseDTO;

/** Conteudo de `data` do exemplo OFICIAL da doc do endpoint. */
function ttAffSellerFixture(string $slug): array
{
    return json_decode(file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'), true);
}

/**
 * Diferenca RECURSIVA entre o payload original e o roundtrip.
 *
 * Um `array_diff` de chaves de topo nao pega perda dentro de sub-objeto — e e'
 * exatamente no fundo (order > skus > actual_paid_commission > amount) que um
 * campo some sem ninguem notar. Aqui qualquer chave OU valor perdido em
 * qualquer profundidade vira um caminho na lista.
 *
 * @return list<string> caminhos perdidos
 */
function ttAffSellerLostPaths(mixed $original, mixed $roundtrip, string $path = ''): array
{
    if (! is_array($original)) {
        return $original === $roundtrip ? [] : [$path.' (valor mudou: '.var_export($original, true).' -> '.var_export($roundtrip, true).')'];
    }

    if (! is_array($roundtrip)) {
        return [$path.' (deixou de ser array)'];
    }

    $lost = [];
    foreach ($original as $key => $value) {
        if (! array_key_exists($key, $roundtrip)) {
            $lost[] = $path.'.'.$key;

            continue;
        }
        $lost = array_merge($lost, ttAffSellerLostPaths($value, $roundtrip[$key], $path.'.'.$key));
    }

    return $lost;
}

/** slug do fixture => DTO de resposta que o SDK devolve pra aquele endpoint. */
dataset('affiliate_seller_endpoints', [
    'orders/search' => ['affiliate-seller-orders-search', AffiliateOrderSearchResponseDTO::class],
    'create open collaboration' => ['affiliate-seller-create-open-collaboration', OpenCollaborationCreateResponseDTO::class],
    'search open collaborations' => ['affiliate-seller-search-open-collaborations', OpenCollaborationSearchResponseDTO::class],
    'remove open collaboration' => ['affiliate-seller-remove-open-collaboration', OpenCollaborationRemoveResponseDTO::class],
    'open collaboration settings' => ['affiliate-seller-open-collaboration-settings', OpenCollaborationSettingsResponseDTO::class],
    'sample rules' => ['affiliate-seller-open-collaboration-sample-rules', SampleRuleListResponseDTO::class],
    'creator content details' => ['affiliate-seller-creator-content-details', CreatorContentDetailResponseDTO::class],
    'open collaboration products' => ['affiliate-seller-open-collaboration-products-search', AffiliateProductSearchResponseDTO::class],
    'promotion link' => ['affiliate-seller-product-promotion-link', ProductPromotionLinkResponseDTO::class],
    'create target collaboration' => ['affiliate-seller-create-target-collaboration', TargetCollaborationCreateResponseDTO::class],
    'update target collaboration' => ['affiliate-seller-update-target-collaboration', TargetCollaborationUpdateResponseDTO::class],
    'target collaboration link' => ['affiliate-seller-target-collaboration-link', TargetCollaborationLinkResponseDTO::class],
    'search target collaborations' => ['affiliate-seller-search-target-collaborations', TargetCollaborationSearchResponseDTO::class],
    'target collaboration detail' => ['affiliate-seller-target-collaboration-detail', TargetCollaborationDetailResponseDTO::class],
    'conflicts check' => ['affiliate-seller-target-collaboration-conflicts-check', TargetCollaborationConflictCheckResponseDTO::class],
    'conflicts resolve' => ['affiliate-seller-target-collaboration-conflicts-resolve', TargetCollaborationConflictResolveResponseDTO::class],
    'creator promotion details' => ['affiliate-seller-creator-promotion-details', CreatorPromotionDetailResponseDTO::class],
    'sample applications' => ['affiliate-seller-sample-applications-search', SampleApplicationSearchResponseDTO::class],
    'sample fulfillments' => ['affiliate-seller-sample-application-fulfillments', SampleFulfillmentSearchResponseDTO::class],
    'sample deeplink' => ['affiliate-seller-sample-request-deeplink', SampleRequestDeeplinkResponseDTO::class],
    'marketplace creators search' => ['affiliate-seller-marketplace-creators-search', MarketplaceCreatorSearchResponseDTO::class],
    'marketplace creator performance' => ['affiliate-seller-marketplace-creator-performance', MarketplaceCreatorPerformanceResponseDTO::class],
    'marketplace creator filters' => ['affiliate-seller-marketplace-creator-filters', CreatorMarketplaceFiltersResponseDTO::class],
    'outreach quota' => ['affiliate-seller-creator-outreach-quota', CreatorOutreachQuotaResponseDTO::class],
    'outreach records' => ['affiliate-seller-creator-outreach-records', CreatorOutreachRecordsResponseDTO::class],
    'compass create task' => ['affiliate-seller-compass-create-task', CompassTaskCreateResponseDTO::class],
    'compass tasks' => ['affiliate-seller-compass-tasks', CompassTaskListResponseDTO::class],
    'compass task file' => ['affiliate-seller-compass-task-file', CompassTaskFileResponseDTO::class],
    'create conversation' => ['affiliate-seller-create-conversation', ConversationCreateResponseDTO::class],
    'conversations' => ['affiliate-seller-conversations', ConversationListResponseDTO::class],
    'conversation messages' => ['affiliate-seller-conversation-messages', ConversationMessageListResponseDTO::class],
    'send im message' => ['affiliate-seller-send-im-message', SendImMessageResponseDTO::class],
    'latest unread messages' => ['affiliate-seller-latest-unread-messages', LatestUnreadMessagesResponseDTO::class],
    'mark conversation read' => ['affiliate-seller-mark-conversation-read', MarkConversationReadResponseDTO::class],
    'inbox group counts' => ['affiliate-seller-inbox-group-counts', InboxCategoryCountsResponseDTO::class],
    'upload message image' => ['affiliate-seller-upload-message-image', MessageImageUploadResponseDTO::class],
    'upload message image v2' => ['affiliate-seller-upload-message-image-v2', MessageImageUploadResponseDTO::class],
]);

it('nao descarta NENHUM campo do exemplo OFICIAL da doc, em NENHUMA profundidade', function (string $slug, string $dto) {
    $payload = ttAffSellerFixture($slug);

    $perdidos = ttAffSellerLostPaths($payload, $dto::fromArray($payload)->toArray());

    expect($perdidos)->toBe([], $slug.' — campos descartados: '.implode(', ', $perdidos));
})->with('affiliate_seller_endpoints');

describe('pedido de afiliado — a origem da comissao do extrato', function () {
    it('mantem dinheiro e taxa como STRING (tipar float comeria a casa decimal)', function () {
        $sku = AffiliateOrderSearchResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-orders-search')
        )->orders[0]->skus[0];

        expect($sku->price->amount)->toBe('1000')
            ->and($sku->actualPaidCommission->amount)->toBe('10000')
            ->and($sku->commissionRate)->toBeString();
    });

    it('expressa a taxa em CENTESIMOS DE POR CENTO, nao em fracao nem percentual', function () {
        $sku = AffiliateOrderSearchResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-orders-search')
        )->orders[0]->skus[0];

        // "1000" = 10,00%. Quem tratar como percentual direto vira 1000%;
        // quem tratar como fracao vira 100000%.
        expect((float) ((int) $sku->commissionRate / 100))->toBe(10.0)
            ->and((float) ((int) $sku->shopAdsCommissionRate / 100))->toBe(50.0);
    });

    it('separa comissao ESTIMADA (venda bruta) da EFETIVA (pos-devolucao)', function () {
        $sku = AffiliateOrderSearchResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-orders-search')
        )->orders[0]->skus[0];

        // Conciliar repasse com o valor estimado da erro: o base efetivo ja
        // desconta devolucao/reembolso e nao bate com o estimado.
        expect($sku->estimatedPaidCommission->amount)->toBe('20000')
            ->and($sku->actualPaidCommission->amount)->toBe('10000')
            ->and($sku->estimatedCommissionBase->amount)
            ->not->toBe($sku->actualCommissionBase->amount);
    });

    it('preserva os campos que a doc marca como DEPRECIADOS em vez de descarta-los', function () {
        $order = AffiliateOrderSearchResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-orders-search')
        )->orders[0];

        // A doc diz que `status` e `refunded_quantity` nao retornam valor. Se
        // um dia voltarem, o SDK nao pode ser o motivo de eles sumirem.
        expect($order->status)->toBe('COMPLETED')
            ->and($order->skus[0]->refundedQuantity)->toBe(1)
            ->and($order->skus[0]->fullyReturn)->toBe('Yes');
    });

    it('conta SKU-orders em totalCount, nao pedidos', function () {
        $resp = AffiliateOrderSearchResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-orders-search')
        );

        expect($resp->totalCount)->toBe(10000)
            ->and($resp->orders)->toHaveCount(1);
    });
});

describe('as tres convencoes de porcentagem convivendo no mesmo grupo', function () {
    it('usa centesimos de por cento na comissao da colaboracao aberta', function () {
        $c = OpenCollaborationSearchResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-search-open-collaborations')
        )->openCollaborations[0]->currentCommission;

        expect($c->rate)->toBe(3000); // 30,00%
    });

    it('usa FRACAO em string na comissao do pedido de amostra', function () {
        $app = SampleApplicationSearchResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-sample-applications-search')
        )->sampleApplications[0];

        // "0.1" = 10%. O parser de centesimos leria 0% aqui.
        expect($app->commissionRate)->toBe('0.1');
    });

    it('usa FRACAO em string na distribuicao de seguidores do creator', function () {
        $creator = MarketplaceCreatorPerformanceResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-marketplace-creator-performance')
        )->creator;

        // Duas convencoes no MESMO objeto: distribuicao e fracao,
        // engagement rate e x10.000.
        expect($creator->followerLocation[0]->value)->toBe('0.3705')
            ->and($creator->ecVideoEngagementRate)->toBe('3000');
    });
});

describe('quirks que quebram quem assume o padrao do grupo', function () {
    it('le a comissao do creator em MILISSEGUNDOS, nao em segundos', function () {
        $base = CreatorPromotionDetailResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-creator-promotion-details')
        )->data->productDetails[0]->productBaseInfo;

        // 1786592565735 lido como segundos cairia no ano 58.600.
        expect($base->commissionEffectiveTime)->toBe(1786592565735)
            ->and((int) ($base->commissionEffectiveTime / 1000))->toBeGreaterThan(1_700_000_000);
    });

    it('devolve productStatus como CODIGO numerico aqui e como enum textual nas outras rotas', function () {
        $base = CreatorPromotionDetailResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-creator-promotion-details')
        )->data->productDetails[0]->productBaseInfo;

        $outra = TargetCollaborationDetailResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-target-collaboration-detail')
        )->targetCollaboration->products[0];

        expect($base->productStatus)->toBe('1')
            ->and($outra->status)->toBe('LIVE');
    });

    it('aceita o typo creator_inivited_count da busca e a grafia certa no detalhe', function () {
        $busca = TargetCollaborationSearchResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-search-target-collaborations')
        )->targetCollaborations[0];

        $detalhe = TargetCollaborationDetailResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-target-collaboration-detail')
        )->targetCollaboration;

        // As duas grafias sao reais na API. Normalizar uma delas dropava o campo.
        expect($busca->creatorInivitedCount)->toBe(10)
            ->and($detalhe->creatorInvitedCount)->toBe(10);
    });

    it('mantem effectiveTime da comissao como STRING nesta rota, apesar de ser epoch', function () {
        $c = TargetCollaborationDetailResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-target-collaboration-detail')
        )->targetCollaboration->products[0]->commission;

        expect($c->effectiveTime)->toBe('1715654330')->toBeString();
    });

    it('le os campos *_30d/*_90d do perfil avancado, que o camel->snake automatico perderia', function () {
        $p = MarketplaceCreatorPerformanceResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-marketplace-creator-performance')
        )->creator->creatorProfile;

        // Sem #[JsonKey] o SDK procuraria `live_engagement_rate30d` e o campo
        // viria null em silencio.
        expect($p->liveEngagementRate30d)->toBe(50)
            ->and($p->videoPlayCnt30dMed)->toBe(10)
            ->and($p->creatorFulfillmentScore90d)->toBe('4.1')
            ->and($p->hasInvited90d)->toBeFalse();
    });

    it('desembrulha o envelope duplicado data.data das rotas novas', function () {
        expect(CreatorOutreachQuotaResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-creator-outreach-quota')
        )->data->shopId)->toBe('1234');

        expect(InboxCategoryCountsResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-inbox-group-counts')
        )->data->groupCounts[0]->groupType)->toBe('UNREAD');

        expect(CreatorMarketplaceFiltersResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-marketplace-creator-filters')
        )->data->sorters[0]->algorithmId)->toBe('1');

        expect(CreatorOutreachRecordsResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-creator-outreach-records')
        )->data->total)->toBe(10);
    });

    it('trata a cota de abordagem como string em milissegundos', function () {
        $q = CreatorOutreachQuotaResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-creator-outreach-quota')
        )->data;

        // Contador que vem entre aspas: somar direto concatena.
        expect($q->quotaSum)->toBe('1000')->toBeString()
            ->and($q->startTime)->toBe('1754296736852');
    });
});

describe('sucesso parcial: code=0 nao significa que tudo entrou', function () {
    it('lista no create do convite dirigido o que NAO foi convidado', function () {
        $r = TargetCollaborationCreateResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-create-target-collaboration')
        );

        expect($r->targetCollaboration->id)->toBe('7365861555575916210')
            ->and($r->targetCollaborationConflicts)->toHaveCount(1)
            ->and($r->invalidOpenIdList)->toBe(['xxxx'])
            ->and($r->invalidProductIdList)->toBe(['1234']);
    });

    it('lista no update o que nao foi aplicado, campo a campo', function () {
        $f = TargetCollaborationUpdateResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-update-target-collaboration')
        )->updateFailed;

        expect($f->removeProductIds)->toBe(['12345'])
            ->and($f->addProducts->id)->toBe('789078671231312312')
            ->and($f->changeCommissions->commissionRate)->toBe(1000)
            ->and($f->sellerContactInfo->email)->toBe('test@bytedance.com');
    });

    it('devolve sucesso E falha na resolucao de conflito', function () {
        $r = TargetCollaborationConflictResolveResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-target-collaboration-conflicts-resolve')
        );

        expect($r->successItems)->toHaveCount(1)
            ->and($r->failedItems)->toHaveCount(1);
    });

    it('mantem cru o failed_conversation_ids, onde a API mistura int e string', function () {
        $r = MarkConversationReadResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-mark-conversation-read')
        );

        // O tipo declarado e []string mas o exemplo oficial manda [123].
        expect($r->failedConversationIds)->toBe([123]);
    });
});

describe('mensagens e remocao adiada', function () {
    it('entrega o content da mensagem como JSON dentro de string, sem decodificar', function () {
        $body = ConversationMessageListResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-conversation-messages')
        )->messages[0]->messageBody;

        // O shape muda por `type`; decodificar aqui exigiria adivinhar.
        expect($body->content)->toBe('{"content": "simple text message"}')
            ->and(json_decode($body->content, true)['content'])->toBe('simple text message');
    });

    it('ordena o historico por conversationIndex, que vem como string', function () {
        $m = ConversationMessageListResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-conversation-messages')
        )->messages[0];

        expect($m->conversationIndex)->toBe('1735032460678899')->toBeString();
    });

    it('marca a remocao da colaboracao aberta como FUTURA, nao imediata', function () {
        $r = OpenCollaborationRemoveResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-remove-open-collaboration')
        );

        // Ate esse instante o creator segue promovendo e gerando comissao.
        expect($r->terminatedEffectiveTime)->toBe(1725334422);
    });

    it('distingue conversa nova de conversa reaproveitada', function () {
        $r = ConversationCreateResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-create-conversation')
        );

        expect($r->isNew)->toBeTrue()
            ->and($r->creatorImId)->toBe('12345678');
    });
});

describe('dado exato do creator e opcional', function () {
    it('cai pra faixa quando o creator nao autoriza numero preciso', function () {
        $c = MarketplaceCreatorSearchResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-marketplace-creators-search')
        )->creators[0];

        expect($c->gmvRange->formattedRange)->toBe('$0-$100')
            ->and($c->unitsSoldRange->minimumAmount)->toBe(100);
    });

    it('devolve o searchKey que estabiliza a paginacao', function () {
        $r = MarketplaceCreatorSearchResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-marketplace-creators-search')
        );

        // Sem reenviar isto, a pagina 2 sai de um rank recalculado.
        expect($r->searchKey)->toBe('k1ChOzI9e+j5BHHHEnt+VhItjVFEkPUCCXNsrU8/v4U=');
    });

    it('recebe o avatar cru no pedido de amostra e embrulhado no resto do grupo', function () {
        $creator = SampleApplicationSearchResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-sample-applications-search')
        )->sampleApplications[0]->creator;

        $outro = MarketplaceCreatorSearchResponseDTO::fromArray(
            ttAffSellerFixture('affiliate-seller-marketplace-creators-search')
        )->creators[0];

        expect($creator->avatarUrl)->toBeString()
            ->and($outro->avatar->url)->toBeString();
    });
});

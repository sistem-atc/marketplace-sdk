<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\CalculateRefundResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\CancelOrderResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\DecisionEligibilityResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\RejectReasonsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\ReviewAftersalesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\ReviewDecisionResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\SearchAftersalesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\SearchCancellationsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\SearchRmaResponseDTO;

function reverseFixture(string $slug): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'),
        true,
    );
}

/**
 * O teste que impede a regressao que originou este trabalho: o roundtrip
 * comparado contra fixture escrito a MAO passava verde enquanto campo que a API
 * mandava morria na desserializacao. Aqui a fonte e' o exemplo OFICIAL da doc,
 * comparado INTEIRO (nao so' as chaves de primeiro nivel) — se o TikTok
 * adicionar campo, atualiza-se o fixture e o teste avisa.
 */
dataset('reverse_fixtures', [
    'calculate refund' => ['calculate-refund', CalculateRefundResponseDTO::class],
    'search aftersales request' => ['search-aftersales-request', SearchAftersalesResponseDTO::class],
    'search cancellations' => ['search-cancellations', SearchCancellationsResponseDTO::class],
    'search RMA' => ['search-rma', SearchRmaResponseDTO::class],
    'get reject reasons' => ['get-reject-reasons', RejectReasonsResponseDTO::class],
    'get decision eligibility' => ['get-decision-eligibility', DecisionEligibilityResponseDTO::class],
    'get review decision' => ['get-review-decision', ReviewDecisionResponseDTO::class],
    'review aftersales' => ['review-aftersales', ReviewAftersalesResponseDTO::class],
    'cancel order' => ['cancel-order', CancelOrderResponseDTO::class],
]);

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function (string $slug, string $dto) {
    $payload = reverseFixture($slug);

    expect($dto::fromArray($payload)->toArray())->toEqual($payload);
})->with('reverse_fixtures');

// ─────────────────────────────────────────────────────────────────────────
// Calculate Refund — o numero que fecha titulo no Contas a Receber
// ─────────────────────────────────────────────────────────────────────────

it('calculate refund mantem dinheiro como STRING, inclusive o "1" sem decimal', function () {
    $dto = CalculateRefundResponseDTO::fromArray(reverseFixture('calculate-refund'));
    $valor = $dto->orderRefundAmount;

    // O MESMO objeto mistura "1.23" e "1": tipar float faria "1" virar 1.0 e o
    // raw perderia a forma que o TikTok mandou.
    expect($valor?->refundTotal)->toBe('1.23')
        ->and($valor?->refundSubtotal)->toBe('1')
        ->and($valor?->refundShippingFee)->toBe('0.2');

    // Conta so' no consumidor.
    expect((float) $valor->refundTotal)->toBe(1.23);
});

it('calculate refund: o total NAO e a soma de subtotal + frete + imposto', function () {
    $valor = CalculateRefundResponseDTO::fromArray(reverseFixture('calculate-refund'))->orderRefundAmount;

    $soma = (float) $valor->refundSubtotal
        + (float) $valor->refundShippingFee
        + (float) $valor->refundTax
        + (float) $valor->retailDeliveryFee;

    // 1 + 0.2 + 0.03 + 0.1 = 1.33, mas o TikTok reembolsa 1.23. Quem recalcular
    // o estorno somando as partes fecha o titulo com o valor ERRADO — o
    // `refundTotal` e' a unica fonte.
    expect(round($soma, 2))->not->toBe((float) $valor->refundTotal)
        ->and((float) $valor->refundTotal)->toBe(1.23);
});

// ─────────────────────────────────────────────────────────────────────────
// Search Aftersales Request — a visao direta da devolucao
// ─────────────────────────────────────────────────────────────────────────

it('search aftersales amarra a devolucao ao pedido de VENDA', function () {
    $dto = SearchAftersalesResponseDTO::fromArray(reverseFixture('search-aftersales-request'));
    $pedido = $dto->aftersalesRequests[0];

    // `mainOrderId` (nos line_items) e o `orderId` (na sku return request) sao
    // a MESMA coisa: o id do pedido no /order. E' por ele que o financeiro
    // acha o titulo a baixar.
    expect($pedido->lineItems[0]->mainOrderId)->toBe('577686530908261117')
        ->and($pedido->skuReturnRequests[0]->orderId)->toBe('577686530908261117')
        ->and($pedido->status)->toBe('PENDING_REQUEST_REVIEW');
});

it('search aftersales: REFUND nao devolve mercadoria — nao ha nota de entrada', function () {
    $return = SearchAftersalesResponseDTO::fromArray(reverseFixture('search-aftersales-request'))
        ->aftersalesRequests[0]->skuReturnRequests[0];

    // returnType REFUND = dinheiro volta, produto NAO. So' RETURN_AND_REFUND
    // gera entrada fiscal. Tratar os dois igual cria estoque fantasma.
    expect($return->returnType)->toBe('REFUND')
        ->and($return->refundAmount?->refundTotal)->toBe('1.23')
        ->and($return->returnStatus)->toBe('RETURN_OR_REFUND_REQUEST_PENDING');
});

it('search aftersales expoe o PRAZO da nossa resposta (silencio = aprovado)', function () {
    $return = SearchAftersalesResponseDTO::fromArray(reverseFixture('search-aftersales-request'))
        ->aftersalesRequests[0]->skuReturnRequests[0];

    // epoch em SEGUNDOS, nao milissegundos.
    expect($return->sellerNextActionResponse[0]->action)->toBe('SELLER_RESPOND_REFUND')
        ->and($return->sellerNextActionResponse[0]->deadline)->toBe(1690554680)
        ->and($return->createTime)->toBe(1690451136);
});

it('search aftersales: frete pago por NOS nao aparece no reembolso', function () {
    $return = SearchAftersalesResponseDTO::fromArray(reverseFixture('search-aftersales-request'))
        ->aftersalesRequests[0]->skuReturnRequests[0];

    // O custo real da devolucao = refundTotal + frete de retorno que bancamos.
    // Quem so' olha refundAmount subestima a perda.
    expect($return->shippingFee?->sellerPaidReturnShippingFeeAmount)->toBe('0.1')
        ->and($return->discount?->productPlatformDiscount)->toBe('0.1')
        ->and($return->discount?->productSellerDiscount)->toBe('0.1');
});

it('search aftersales desdobra o bundle em sub-itens (o SKU real do estoque)', function () {
    $linha = SearchAftersalesResponseDTO::fromArray(reverseFixture('search-aftersales-request'))
        ->aftersalesRequests[0]->skuReturnRequests[0]->returnLineItems[0];

    // Baixar estoque pela linha "pai" devolve um SKU que nao existe no
    // deposito; o SKU real esta' no sub-item.
    expect($linha->returnSubLineItems)->toHaveCount(1)
        ->and($linha->returnSubLineItems[0]->subOrderLineItemId)->toBe('576473917261451852')
        ->and($linha->returnSubLineItems[0]->sellerSku)->toBe('PUTIH 1 TALI');
});

it('search aftersales traz o RMA (pacote fisico) separado da devolucao', function () {
    $pedido = SearchAftersalesResponseDTO::fromArray(reverseFixture('search-aftersales-request'))
        ->aftersalesRequests[0];

    // Hierarquia: aftersales request -> sku return request (dinheiro) -> RMA
    // (mercadoria). O recebimento se controla pelo status do RMA.
    expect($pedido->returnMerchandiseAuthorizations[0]->status)->toBe('AWAITING_BUYER_RETURN')
        ->and($pedido->returnMerchandiseAuthorizations[0]->packageId)->toBe('2894521704838895645')
        ->and($pedido->returnMerchandiseAuthorizations[0]->aftersalesRequestId)->toBe($pedido->id);
});

// ─────────────────────────────────────────────────────────────────────────
// Cancelamento
// ─────────────────────────────────────────────────────────────────────────

it('cancel order confirma a SOLICITACAO, nao o cancelamento', function () {
    $dto = CancelOrderResponseDTO::fromArray(reverseFixture('cancel-order'));

    // CANCELLATION_REQUEST_SUCCESS = o pedido de cancelamento entrou. O estado
    // final chega depois (webhook type 11 / searchCancellations).
    expect($dto->cancelId)->toBe('4035319218955782461')
        ->and($dto->cancelStatus)->toBe('CANCELLATION_REQUEST_SUCCESS');
});

it('search cancellations diz se o estoque deve voltar', function () {
    $cancel = SearchCancellationsResponseDTO::fromArray(reverseFixture('search-cancellations'))
        ->cancellations[0];

    // `false` precisa sobreviver a serializacao — se o DTO tratasse false como
    // "vazio", o campo sumiria e o estoque seria reposto por engano.
    expect($cancel->shouldReplenishStock)->toBeFalse()
        ->and($cancel->cancelType)->toBe('REQUEST_CANCEL_REFUND')
        ->and($cancel->role)->toBe('BUYER')
        ->and($cancel->refundAmount?->refundTotal)->toBe('1.23');
});

it('search cancellations mantem buyer_service_fee "1000" como STRING', function () {
    $cancel = SearchCancellationsResponseDTO::fromArray(reverseFixture('search-cancellations'))
        ->cancellations[0];

    // Taxa so' do mercado ID. Vem sem decimal ("1000") — o roundtrip precisa
    // devolver exatamente isso, nao 1000.0.
    expect($cancel->refundAmount?->buyerServiceFee)->toBe('1000')
        ->and($cancel->cancelLineItems[0]->refundAmount?->buyerServiceFee)->toBe('1000');
});

// ─────────────────────────────────────────────────────────────────────────
// Decisoes
// ─────────────────────────────────────────────────────────────────────────

it('elegibilidade se decide pelo BOOLEANO, nao pelo motivo de inelegibilidade', function () {
    $decisao = DecisionEligibilityResponseDTO::fromArray(reverseFixture('get-decision-eligibility'))
        ->decisions[0];

    // Pegadinha do exemplo OFICIAL: eligible = true E ineligible_reason
    // preenchido. Quem checa "tem ineligibleReason?" bloqueia uma acao valida.
    expect($decisao->eligible)->toBeTrue()
        ->and($decisao->ineligibleCode)->toBe(25001001)
        ->and($decisao->ineligibleReason)->not->toBeNull()
        ->and($decisao->decision)->toBe('APPROVE_REFUND');
});

it('review decision traz a FAIXA do reembolso parcial, por linha', function () {
    $dto = ReviewDecisionResponseDTO::fromArray(reverseFixture('get-review-decision'));
    $decisao = $dto->lineItems[0]->decisions[0];

    // Propor fora da faixa e' recusado pela API — validar antes evita queimar
    // tentativa. Valores STRING, como todo dinheiro do TikTok.
    expect($decisao->partialRefundAmountRange?->minAmount)->toBe('2.34')
        ->and($decisao->partialRefundAmountRange?->maxAmount)->toBe('3.33')
        ->and($decisao->eligible)->toBeFalse()
        ->and($decisao->availableRejectReasons[0]->name)->toBe('seller_reject_apply_product_has_been_packed');
});

it('review decision e review aftersales devolvem erro PARCIAL com code 0 no envelope', function () {
    $revisao = ReviewDecisionResponseDTO::fromArray(reverseFixture('get-review-decision'));
    $lote = ReviewAftersalesResponseDTO::fromArray(reverseFixture('review-aftersales'));

    // `data.errors` e' o unico lugar onde aparece o que falhou dentro do lote;
    // o envelope volta code 0 (sucesso). E o code AQUI e' STRING.
    expect($revisao->errors[0]->code)->toBe('98001004')
        ->and($lote->errors[0]->code)->toBe('98001004')
        ->and($lote->errors[0]->message)->toContain('Invalid parameters');

    expect($revisao->requestLogId)->not->toBeNull(); // pedido pelo suporte TikTok
});

it('reject reasons devolve a CHAVE e o texto traduzido separados', function () {
    $motivo = RejectReasonsResponseDTO::fromArray(reverseFixture('get-reject-reasons'))->reasons[0];

    // Mandar o `text` de volta na rejeicao e' erro: a API so' aceita `name`.
    expect($motivo->name)->toBe('seller_reject_apply_you_have_reached_an_agreement_with_the_buyer')
        ->and($motivo->text)->toBe('You have reached an agreement with the buyer');
});

it('search RMA e search aftersales compartilham a MESMA shape de return', function () {
    $doRma = SearchRmaResponseDTO::fromArray(reverseFixture('search-rma'))
        ->returnMerchandiseAuthorizations[0]->skuReturnRequests[0];
    $doAftersales = SearchAftersalesResponseDTO::fromArray(reverseFixture('search-aftersales-request'))
        ->aftersalesRequests[0]->skuReturnRequests[0];

    // Os dois endpoints existem porque o EIXO da busca muda (pacote x
    // solicitacao), nao porque o dado muda — vale reusar o mesmo consumidor.
    expect($doRma->returnId)->toBe($doAftersales->returnId)
        ->and($doRma->refundAmount?->refundTotal)->toBe($doAftersales->refundAmount?->refundTotal);
});

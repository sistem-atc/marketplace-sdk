<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

beforeEach(function () {
    config(['marketplaces.tiktok.base_url' => 'https://open-api.tiktokglobalshop.com']);
});

function ttReverseIntegration(): FakeIntegration
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

function ttReverse()
{
    return MarketPlaces::Tiktok()->reverse(ttReverseIntegration());
}

/** Envelope de sucesso do TikTok: code 0 + data. */
function ttOk(array $data): array
{
    return ['code' => 0, 'message' => 'Success', 'request_id' => 'r1', 'data' => $data];
}

describe('MarketPlaces::Tiktok()->reverse()', function () {
    it('calculateRefund manda o request_type: e ele que muda o valor', function () {
        Http::fake([
            '*/return_refund/202602/refunds/calculate*' => Http::response(ttOk([
                'order_refund_amount' => ['currency' => 'BRL', 'refund_total' => '45.90'],
            ])),
        ]);

        $dto = ttReverse()->calculateRefund(
            orderId: '577750646121497000',
            requestType: 'RETURN_AND_REFUND',
            reasonName: 'ecom_order_delivered_refund_reason_missing_product_seller',
            orderLineItemIds: ['576469648086306986'],
        );

        expect($dto->orderRefundAmount?->refundTotal)->toBe('45.90');

        Http::assertSent(function ($req) {
            // CANCEL / REFUND / RETURN_AND_REFUND usam politicas de calculo
            // diferentes pro MESMO pedido: omitir o tipo devolve outro numero.
            return $req->method() === 'POST'
                && str_contains($req->body(), '"request_type":"RETURN_AND_REFUND"')
                && str_contains($req->body(), '"order_line_item_ids":["576469648086306986"]')
                // Campos opcionais nao enviados nao viram null no corpo.
                && ! str_contains($req->body(), 'shipment_type');
        });
    });

    it('searchAftersalesRequests manda o whitelist por padrao (sem ele a resposta vem oca)', function () {
        Http::fake([
            '*/return_refund/202603/aftersales/search*' => Http::response(ttOk([
                'aftersales_requests' => [['id' => 'A1', 'status' => 'PENDING_REQUEST_REVIEW']],
                'total_count' => 1,
                'next_page_token' => 'TOKEN',
            ])),
        ]);

        $dto = ttReverse()->searchAftersalesRequests(
            filters: ['main_order_ids' => ['577686530908261117']],
        );

        expect($dto->aftersalesRequests)->toHaveCount(1)
            ->and($dto->nextPageToken)->toBe('TOKEN');

        Http::assertSent(function ($req) {
            // Sem SKU_RETURN_REQUESTS a busca nao traz valor de reembolso —
            // e' o dado que justifica o endpoint. Por isso o default liga tudo.
            return str_contains($req->body(), 'SKU_RETURN_REQUESTS')
                && str_contains($req->body(), 'RETURN_MERCHANDISE_AUTHORIZATIONS')
                // paginacao vai no BODY neste endpoint
                && str_contains($req->body(), '"pagination":{"page_size":20}');
        });
    });

    it('searchCancellations poe paginacao na QUERY e filtros no BODY (inverso do aftersales)', function () {
        Http::fake([
            '*/return_refund/202602/cancellations/search*' => Http::response(ttOk([
                'cancellations' => [['cancel_id' => 'C1', 'order_id' => 'O1']],
                'total_count' => 1,
            ])),
        ]);

        $dto = ttReverse()->searchCancellations(
            filters: ['order_ids' => ['O1']],
            pageSize: 50,
            pageToken: 'PT',
            sortField: 'create_time',
            sortOrder: 'DESC',
        );

        expect($dto->cancellations[0]->cancelId)->toBe('C1');

        Http::assertSent(function ($req) {
            return str_contains($req->url(), 'page_size=50')
                && str_contains($req->url(), 'page_token=PT')
                && str_contains($req->url(), 'sort_order=DESC')
                && str_contains($req->body(), '"order_ids":["O1"]')
                && ! str_contains($req->body(), 'page_size');
        });
    });

    it('check_decisions vai como CSV na query — array quebraria a assinatura', function () {
        Http::fake([
            '*/return_refund/202601/decision_eligibility*' => Http::response(ttOk([
                'decisions' => [['decision' => 'APPROVE_REFUND', 'eligible' => true]],
            ])),
        ]);

        $dto = ttReverse()->getDecisionEligibility('4035318504086604100', ['APPROVE_REFUND', 'REJECT_REFUND']);

        expect($dto->decisions[0]->eligible)->toBeTrue();

        Http::assertSent(function ($req) {
            // O SignatureGenerator serializa array em JSON; o servidor recalcula
            // o `sign` a partir da URL, onde o array chegaria como CSV. Mandar
            // array daria erro de assinatura, nao erro de parametro — bem mais
            // dificil de diagnosticar.
            return $req->method() === 'GET'
                && str_contains(urldecode($req->url()), 'check_decisions=APPROVE_REFUND,REJECT_REFUND');
        });
    });

    it('approveCancellation manda o idempotency_key na query e nao tem corpo', function () {
        Http::fake([
            '*/return_refund/202309/cancellations/*/approve*' => Http::response(ttOk([])),
        ]);

        ttReverse()->approveCancellation('4035318504086604100', 'chave-estavel-do-cancelamento');

        Http::assertSent(function ($req) {
            return str_contains($req->url(), '/cancellations/4035318504086604100/approve')
                && str_contains($req->url(), 'idempotency_key=chave-estavel-do-cancelamento')
                && $req->method() === 'POST';
        });
    });

    it('rejectCancellation manda a CHAVE do motivo, nunca o texto', function () {
        Http::fake([
            '*/return_refund/202309/cancellations/*/reject*' => Http::response(ttOk([])),
        ]);

        ttReverse()->rejectCancellation(
            cancelId: 'C1',
            rejectReason: 'seller_reject_apply_product_has_been_packed',
            comment: 'Pedido ja despachado',
        );

        Http::assertSent(function ($req) {
            return str_contains($req->body(), '"reject_reason":"seller_reject_apply_product_has_been_packed"')
                && str_contains($req->body(), '"comment":"Pedido ja despachado"')
                // `images` nao enviado nao vira lista vazia
                && ! str_contains($req->body(), 'images');
        });
    });

    it('uploadReturnShippingDocument sobe multipart com os return_ids na query', function () {
        Http::fake([
            '*/return_refund/202405/returns/shipping_documents*' => Http::response(ttOk([])),
        ]);

        ttReverse()->uploadReturnShippingDocument(
            returnIds: ['4035309948547270559', '4035309948547270560'],
            returnIdType: 'RMA',
            trackingNumber: 'TTHERMES2882481596119090868',
            returnProviderId: '6599541761693270018',
            files: ['return_shipping_label' => 'PDF-BYTES'],
        );

        Http::assertSent(function ($req) {
            // Unico endpoint multipart do grupo: o corpo fica FORA da
            // assinatura, por isso ele nao passa pelo makeRequest.
            return $req->method() === 'POST'
                && str_contains(urldecode($req->url()), 'return_ids=4035309948547270559,4035309948547270560')
                && str_contains(urldecode($req->url()), 'return_id_type=RMA')
                && str_contains($req->body(), 'return_shipping_label')
                && str_contains($req->body(), 'TTHERMES2882481596119090868');
        });
    });

    it('reviewAftersales envolve as decisoes em aftersales_request_decisions', function () {
        Http::fake([
            '*/return_refund/202606/aftersales/review*' => Http::response(ttOk([
                'errors' => [['code' => '98001004', 'message' => 'Invalid parameters']],
            ])),
        ]);

        $dto = ttReverse()->reviewAftersales([[
            'request_id' => 'A1',
            'request_id_type' => 'AFTERSALES',
            'idempotency_key' => 'chave-estavel',
        ]]);

        // Sucesso HTTP com falha dentro: `code: 0` no envelope e o erro em data.
        expect($dto->errors)->toHaveCount(1)
            ->and($dto->errors[0]->code)->toBe('98001004');

        Http::assertSent(fn ($req) => str_contains($req->body(), '"aftersales_request_decisions":[{'));
    });
});

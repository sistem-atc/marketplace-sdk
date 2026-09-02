<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\MercadoPago\DTO\Response\AdvancedPayments\AdvancedPaymentResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\AdvancedPayments\AdvancedPaymentSearchResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\AdvancedPayments\DisbursementRefundListResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\AdvancedPayments\DisbursementRefundResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\AuthorizedPayments\AuthorizedPaymentResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\AuthorizedPayments\AuthorizedPaymentSearchResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\CardTokens\CardTokenResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Catalog\IdentificationTypeResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Catalog\PaymentMethodResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Chargebacks\ChargebackResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Chargebacks\ChargebackSearchResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Customers\CustomerAddress;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Customers\CustomerCardResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Customers\CustomerResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Customers\CustomerSearchResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders\MerchantOrderResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders\MerchantOrderSearchResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\OAuth\OAuthResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders\OrderResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders\OrderSearchResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders\OrderTransactionsResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders\OrderTransactionUpdateResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments\FeeDetails;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments\PaymentResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments\PaymentSearchResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point\PaymentIntentCancelResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Refunds\PaymentRefundResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point\PaymentIntentListResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point\PaymentIntentResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point\PaymentIntentStatusResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point\PointDeviceOperatingModeResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point\PointDevicesResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\PreApprovalPlans\PreApprovalPlanResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\PreApprovalPlans\PreApprovalPlanSearchResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\PreApprovals\PreApprovalResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\PreApprovals\PreApprovalSearchResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences\PreferenceListResult;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences\PreferenceResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences\PreferenceSearchResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users\UserResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Paging;
use SistemAtc\Marketplaces\Tests\Support\RecursiveFieldCoverage;

/**
 * Cobertura dos DTOs do Mercado Pago contra as 52 respostas de exemplo do
 * SDK oficial (mercadopago/sdk-php, tests/.../Mocks/Response). Cada fixture
 * e' a resposta REAL documentada — se um campo dela nao sobrevive ao
 * roundtrip, o DTO esta' descartando dado da API.
 */
function mpDtoFixture(string $slug): array
{
    return json_decode(
        file_get_contents(__DIR__.'/../Fixtures/MercadoPago/'.$slug.'.json'),
        true,
        512,
        JSON_THROW_ON_ERROR,
    );
}

/**
 * Remove chaves nulas recursivamente. O CastToArray omite nulo de proposito
 * (mesma regra do RecursiveFieldCoverage), e as respostas do MP vem CHEIAS
 * de `null` explicito (`"sponsor_id": null`) — sem isso o roundtrip
 * compararia ausencia com null e falharia sem perda real de dado.
 */
function mpDtoWithoutNulls(array $data): array
{
    $out = [];
    foreach ($data as $k => $v) {
        if ($v === null) {
            continue;
        }
        $out[$k] = is_array($v) ? mpDtoWithoutNulls($v) : $v;
    }

    return $out;
}

/**
 * [slug do fixture, DTO, a resposta e' LISTA pura de DTO?]
 *
 * @return array<string, array{string, class-string, bool}>
 */
function mpDtoFixtureRows(): array
{
    return [
    // Payments / Refunds
    'payment base' => ['payment-base', PaymentResponseDTO::class, false],
    'payment cancelled' => ['payment-cancelled', PaymentResponseDTO::class, false],
    'payment captured' => ['payment-captured', PaymentResponseDTO::class, false],
    'payment search' => ['payment-search', PaymentSearchResponseDTO::class, false],
    'payment refund' => ['payment-refund', PaymentRefundResponseDTO::class, false],
    'payment refund list (lista pura)' => ['payment-refund-list', PaymentRefundResponseDTO::class, true],
    // Chargebacks
    'chargeback base' => ['chargeback-base', ChargebackResponseDTO::class, false],
    'chargeback list' => ['chargeback-list', ChargebackSearchResponseDTO::class, false],
    // Merchant orders
    'merchant order base' => ['merchant-order-base', MerchantOrderResponseDTO::class, false],
    'merchant order search (elements)' => ['merchant-order-search', MerchantOrderSearchResponseDTO::class, false],
    // Preferences
    'preference base' => ['preference-base', PreferenceResponseDTO::class, false],
    'preference update' => ['preference-update', PreferenceResponseDTO::class, false],
    'preference search (elements)' => ['preference-search', PreferenceSearchResponseDTO::class, false],
    // Customers + cards
    'customer base' => ['customer-base', CustomerResponseDTO::class, false],
    'customer search' => ['customer-search', CustomerSearchResponseDTO::class, false],
    'customer card base' => ['customer-card-base', CustomerCardResponseDTO::class, false],
    'customer card list (lista pura)' => ['customer-card-list', CustomerCardResponseDTO::class, true],
    // Card tokens
    'card token get' => ['card-token-get', CardTokenResponseDTO::class, false],
    // Catalog
    'payment methods (lista pura)' => ['payment-method-get', PaymentMethodResponseDTO::class, true],
    'identification types (lista pura)' => ['identification-type-list', IdentificationTypeResponseDTO::class, true],
    // Orders v2
    'order' => ['order', OrderResponseDTO::class, false],
    'order get' => ['get-order-response', OrderResponseDTO::class, false],
    'order checkout pro' => ['order-checkout-pro', OrderResponseDTO::class, false],
    'order capture' => ['order-capture', OrderResponseDTO::class, false],
    'order cancel' => ['order-cancel', OrderResponseDTO::class, false],
    'order process' => ['order-process', OrderResponseDTO::class, false],
    'order refund total' => ['order-refund-total', OrderResponseDTO::class, false],
    'order refund partial' => ['order-refund-partial', OrderResponseDTO::class, false],
    'order search (data + paging string)' => ['order-search', OrderSearchResponseDTO::class, false],
    'order transaction create' => ['transaction', OrderTransactionsResponseDTO::class, false],
    'order transaction update' => ['updated-transaction', OrderTransactionUpdateResponseDTO::class, false],
    // Subscriptions
    'preapproval base' => ['preapproval-base', PreApprovalResponseDTO::class, false],
    'preapproval update' => ['preapproval-update', PreApprovalResponseDTO::class, false],
    'preapproval list' => ['preapproval-list', PreApprovalSearchResponseDTO::class, false],
    'preapproval plan base' => ['preapprovalplan-base', PreApprovalPlanResponseDTO::class, false],
    'preapproval plan update' => ['preapprovalplan-update', PreApprovalPlanResponseDTO::class, false],
    'preapproval plan list' => ['preapprovalplan-list', PreApprovalPlanSearchResponseDTO::class, false],
    'authorized payment (invoice) base' => ['invoice-base', AuthorizedPaymentResponseDTO::class, false],
    'authorized payment (invoice) list' => ['invoice-list', AuthorizedPaymentSearchResponseDTO::class, false],
    // Point
    'point payment intent base' => ['payment-intent-base', PaymentIntentResponseDTO::class, false],
    'point payment intent search' => ['payment-intent-search', PaymentIntentResponseDTO::class, false],
    'point payment intent cancel' => ['payment-intent-cancel', PaymentIntentCancelResponseDTO::class, false],
    'point payment intent list' => ['payment-intent-list', PaymentIntentListResponseDTO::class, false],
    'point payment intent status' => ['payment-intent-status', PaymentIntentStatusResponseDTO::class, false],
    'point devices list' => ['devices-list', PointDevicesResponseDTO::class, false],
    'point device operating mode' => ['devices-operating-mode', PointDeviceOperatingModeResponseDTO::class, false],
    // Advanced payments
    'advanced payment base' => ['advanced-payment-base', AdvancedPaymentResponseDTO::class, false],
    'advanced payment list' => ['advanced-payment-list', AdvancedPaymentSearchResponseDTO::class, false],
    'disbursement refund base' => ['disbursement-refund-base', DisbursementRefundResponseDTO::class, false],
    'disbursement refund list' => ['disbursement-refund-list', DisbursementRefundListResponseDTO::class, false],
    // Users / OAuth
    'user base' => ['user-base', UserResponseDTO::class, false],
    'oauth base' => ['oauth-base', OAuthResponseDTO::class, false],
    ];
}

dataset('mp_fixtures', mpDtoFixtureRows());

it('nao descarta NENHUM campo do exemplo OFICIAL do SDK', function (string $slug, string $dto, bool $isList) {
    $payload = mpDtoFixture($slug);
    $items = $isList ? $payload : [$payload];

    foreach ($items as $i => $item) {
        $perdidos = RecursiveFieldCoverage::missing($item, $dto::fromArray($item)->toArray());

        expect($perdidos)->toBe([], "[$slug#$i] campos descartados: ".implode(', ', $perdidos));
    }
})->with('mp_fixtures');

it('faz roundtrip lossless do payload inteiro', function (string $slug, string $dto, bool $isList) {
    $payload = mpDtoFixture($slug);
    $items = $isList ? $payload : [$payload];

    foreach ($items as $item) {
        expect(mpDtoWithoutNulls($dto::fromArray($item)->toArray()))->toEqual(mpDtoWithoutNulls($item));
    }
})->with('mp_fixtures');

it('todo fixture do SDK oficial tem uma linha no dataset', function () {
    $slugs = array_map(
        fn (string $f) => basename($f, '.json'),
        glob(__DIR__.'/../Fixtures/MercadoPago/*.json'),
    );
    $cobertos = array_unique(array_column(array_values(mpDtoFixtureRows()), 0));

    sort($slugs);
    sort($cobertos);

    expect(array_values($cobertos))->toBe($slugs);
});

// ─────────────────────────────────────────────────────────────────────────
// Comportamento real — o que o financeiro/ERP precisa ler do DTO
// ─────────────────────────────────────────────────────────────────────────

it('payment: bruto, liquido e taxas ficam tipados (fee_details e lista de DTO)', function () {
    $p = PaymentResponseDTO::fromArray(mpDtoFixture('payment-base'));

    expect($p->status)->toBe('approved')
        ->and($p->transactionAmount)->toBeFloat()
        ->and($p->transactionDetails?->netReceivedAmount)->toBeFloat()
        ->and($p->feeDetails)->not->toBeEmpty()
        ->and($p->feeDetails[0])->toBeInstanceOf(FeeDetails::class)
        ->and($p->feeDetails[0]->type)->toBe('mercadopago_fee');
});

it('payment: payer.id vem como STRING (nao e o collector_id int)', function () {
    $p = PaymentResponseDTO::fromArray(mpDtoFixture('payment-base'));

    expect($p->payer?->id)->toBeString()
        ->and($p->collectorId)->toBeInt();
});

it('payment search: results sao PaymentResponseDTO, paging total inteiro', function () {
    $s = PaymentSearchResponseDTO::fromArray(mpDtoFixture('payment-search'));

    expect($s->paging)->toBeInstanceOf(Paging::class)
        ->and($s->paging->total)->toBeInt()
        ->and($s->results)->each->toBeInstanceOf(PaymentResponseDTO::class);
});

it('orders v2: paging.total pode vir como STRING — o DTO nao forca cast', function () {
    $s = OrderSearchResponseDTO::fromArray(['paging' => ['total' => '3', 'limit' => 2, 'offset' => 0], 'data' => []]);

    expect($s->paging?->total)->toBe('3')
        ->and(OrderSearchResponseDTO::fromArray(mpDtoFixture('order-search'))->data)
        ->each->toBeInstanceOf(OrderResponseDTO::class);
});

it('orders v2: transactions e OBJETO {payments, refunds}, nao lista', function () {
    $pago = OrderResponseDTO::fromArray(mpDtoFixture('get-order-response'));
    $reembolsado = OrderResponseDTO::fromArray(mpDtoFixture('order-refund-partial'));

    expect($pago->transactions)->toBeInstanceOf(OrderTransactionsResponseDTO::class)
        ->and($pago->transactions->payments[0]->status)->toBeString()
        // Reembolso parcial: so' `refunds` vem preenchido, e amount e' STRING ("25.00").
        ->and($reembolsado->transactions->payments)->toBeNull()
        ->and($reembolsado->transactions->refunds[0]->amount)->toBe('25.00')
        ->and($reembolsado->transactions->refunds[0]->status)->toBe('processed');
});

it('merchant order search usa `elements` + `total`, sem `paging`', function () {
    $s = MerchantOrderSearchResponseDTO::fromArray(mpDtoFixture('merchant-order-search'));

    expect($s->elements)->each->toBeInstanceOf(MerchantOrderResponseDTO::class)
        ->and($s->total)->toBeInt()
        ->and($s->nextOffset)->toBeInt()
        ->and($s->elements[0]->orderStatus)->toBeString()
        ->and($s->elements[0]->payments)->toBe([]);
});

it('preference search devolve um resumo (PreferenceListResult), nao a Preference inteira', function () {
    // `items` no search vem como lista de STRINGS (titulos), nao de objetos —
    // por isso o element tem DTO proprio e nao reaproveita PreferenceResponseDTO.
    $s = PreferenceSearchResponseDTO::fromArray(mpDtoFixture('preference-search'));

    expect($s->elements[0])->toBeInstanceOf(PreferenceListResult::class)
        ->and($s->elements[0]->items)->toBeArray()
        ->and($s->elements[0]->items[0])->toBeString();
});

it('customer: address salvo e mais rico que o Address comum (estado/bairro como objeto)', function () {
    $c = CustomerResponseDTO::fromArray(mpDtoFixture('customer-base'));

    expect($c->addresses[0])->toBeInstanceOf(CustomerAddress::class)
        ->and($c->addresses[0]->state)->toBeArray()->toHaveKey('name')
        ->and($c->addresses[0]->city?->name)->toBe('Osasco');
});

it('oauth: refresh_token rolling e user_id tipados', function () {
    $o = OAuthResponseDTO::fromArray(mpDtoFixture('oauth-base'));

    expect($o->accessToken)->toBeString()
        ->and($o->refreshToken)->toBeString()
        ->and($o->userId)->toBeInt()
        ->and($o->expiresIn)->toBeInt();
});

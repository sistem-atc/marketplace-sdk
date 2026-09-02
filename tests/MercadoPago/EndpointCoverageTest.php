<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MarketPlaces;
use SistemAtc\Marketplaces\MercadoPago\MercadoPago;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

/**
 * Cobertura 1:1 dos clients do SDK oficial (mercadopago/sdk-php): cada
 * metodo bate no verbo + path certos, com Bearer. Idempotencia e paginacao
 * tem testes proprios em BaseMethodsTest.
 */
function mpCoverageIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'APP_USR-fake',
        refreshToken: 'rt-fake',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

beforeEach(function () {
    config(['marketplaces.mercadopago.api_base' => 'https://api.mercadopago.com']);
    Http::fake(fn () => Http::response(['ok' => true]));
});

// [grupo, chamada, verbo, path esperado, body esperado (subset) | null]
dataset('mp_endpoints', [
    // Payments
    'payments.create' => ['payments', fn ($m) => $m->create(['transaction_amount' => 10.5]), 'POST', '/v1/payments', ['transaction_amount' => 10.5]],
    'payments.get' => ['payments', fn ($m) => $m->get(123), 'GET', '/v1/payments/123', null],
    'payments.update' => ['payments', fn ($m) => $m->update(123, ['metadata' => ['a' => 1]]), 'PUT', '/v1/payments/123', ['metadata' => ['a' => 1]]],
    'payments.cancel' => ['payments', fn ($m) => $m->cancel(123), 'PUT', '/v1/payments/123', ['status' => 'cancelled']],
    'payments.capture total' => ['payments', fn ($m) => $m->capture(123), 'PUT', '/v1/payments/123', ['capture' => true]],
    'payments.capture parcial' => ['payments', fn ($m) => $m->capture(123, 5.0), 'PUT', '/v1/payments/123', ['capture' => true, 'transaction_amount' => 5.0]],
    'payments.search' => ['payments', fn ($m) => $m->search(['status' => 'approved']), 'GET', '/v1/payments/search?status=approved', null],
    // Refunds
    'refunds.create parcial' => ['refunds', fn ($m) => $m->create(123, 4.2), 'POST', '/v1/payments/123/refunds', ['amount' => 4.2]],
    'refunds.refundTotal' => ['refunds', fn ($m) => $m->refundTotal(123), 'POST', '/v1/payments/123/refunds', []],
    'refunds.list' => ['refunds', fn ($m) => $m->list(123), 'GET', '/v1/payments/123/refunds', null],
    'refunds.get' => ['refunds', fn ($m) => $m->get(123, 9), 'GET', '/v1/payments/123/refunds/9', null],
    // Chargebacks
    'chargebacks.get' => ['chargebacks', fn ($m) => $m->get('cb1'), 'GET', '/v1/chargebacks/cb1', null],
    'chargebacks.search' => ['chargebacks', fn ($m) => $m->search(['payment_id' => 1]), 'GET', '/v1/chargebacks/search?payment_id=1', null],
    // Merchant orders
    'merchantOrders.create' => ['merchantOrders', fn ($m) => $m->create(['external_reference' => 'X']), 'POST', '/merchant_orders', ['external_reference' => 'X']],
    'merchantOrders.get' => ['merchantOrders', fn ($m) => $m->get(55), 'GET', '/merchant_orders/55', null],
    'merchantOrders.update' => ['merchantOrders', fn ($m) => $m->update(55, ['notification_url' => 'u']), 'PUT', '/merchant_orders/55', ['notification_url' => 'u']],
    'merchantOrders.search' => ['merchantOrders', fn ($m) => $m->search(['external_reference' => 'X']), 'GET', '/merchant_orders/search?external_reference=X', null],
    // Preferences
    'preferences.create' => ['preferences', fn ($m) => $m->create(['items' => []]), 'POST', '/checkout/preferences', ['items' => []]],
    'preferences.get' => ['preferences', fn ($m) => $m->get('1-abc'), 'GET', '/checkout/preferences/1-abc', null],
    'preferences.update' => ['preferences', fn ($m) => $m->update('1-abc', ['expires' => true]), 'PUT', '/checkout/preferences/1-abc', ['expires' => true]],
    'preferences.search' => ['preferences', fn ($m) => $m->search(['sponsor_id' => 1]), 'GET', '/checkout/preferences/search?sponsor_id=1', null],
    // Customers + cards
    'customers.create' => ['customers', fn ($m) => $m->create(['email' => 'a@b.c']), 'POST', '/v1/customers', ['email' => 'a@b.c']],
    'customers.createByEmail' => ['customers', fn ($m) => $m->createByEmail('a@b.c'), 'POST', '/v1/customers', ['email' => 'a@b.c']],
    'customers.get' => ['customers', fn ($m) => $m->get('c1'), 'GET', '/v1/customers/c1', null],
    'customers.update' => ['customers', fn ($m) => $m->update('c1', ['first_name' => 'A']), 'PUT', '/v1/customers/c1', ['first_name' => 'A']],
    'customers.delete' => ['customers', fn ($m) => $m->delete('c1'), 'DELETE', '/v1/customers/c1', null],
    'customers.search' => ['customers', fn ($m) => $m->search(['email' => 'a@b.c']), 'GET', '/v1/customers/search?email=a%40b.c', null],
    'customers.createCard' => ['customers', fn ($m) => $m->createCard('c1', ['token' => 't']), 'POST', '/v1/customers/c1/cards', ['token' => 't']],
    'customers.listCards' => ['customers', fn ($m) => $m->listCards('c1'), 'GET', '/v1/customers/c1/cards', null],
    'customers.getCard' => ['customers', fn ($m) => $m->getCard('c1', 'k1'), 'GET', '/v1/customers/c1/cards/k1', null],
    'customers.updateCard' => ['customers', fn ($m) => $m->updateCard('c1', 'k1', ['x' => 1]), 'PUT', '/v1/customers/c1/cards/k1', ['x' => 1]],
    'customers.deleteCard' => ['customers', fn ($m) => $m->deleteCard('c1', 'k1'), 'DELETE', '/v1/customers/c1/cards/k1', null],
    // Card tokens
    'cardTokens.create' => ['cardTokens', fn ($m) => $m->create(['card_id' => '1', 'security_code' => '123']), 'POST', '/v1/card_tokens', ['card_id' => '1', 'security_code' => '123']],
    'cardTokens.get' => ['cardTokens', fn ($m) => $m->get('tok'), 'GET', '/v1/card_tokens/tok', null],
    // Catalog
    'catalog.paymentMethods' => ['catalog', fn ($m) => $m->paymentMethods(), 'GET', '/v1/payment_methods', null],
    'catalog.identificationTypes' => ['catalog', fn ($m) => $m->identificationTypes(), 'GET', '/v1/identification_types', null],
    // Orders v2
    'orders.create' => ['orders', fn ($m) => $m->create(['type' => 'online']), 'POST', '/v1/orders', ['type' => 'online']],
    'orders.get' => ['orders', fn ($m) => $m->get('ORD1'), 'GET', '/v1/orders/ORD1', null],
    'orders.search sem /search' => ['orders', fn ($m) => $m->search(['external_reference' => 'X']), 'GET', '/v1/orders?external_reference=X', null],
    'orders.capture' => ['orders', fn ($m) => $m->capture('ORD1'), 'POST', '/v1/orders/ORD1/capture', []],
    'orders.cancel' => ['orders', fn ($m) => $m->cancel('ORD1'), 'POST', '/v1/orders/ORD1/cancel', []],
    'orders.process' => ['orders', fn ($m) => $m->process('ORD1'), 'POST', '/v1/orders/ORD1/process', []],
    'orders.refund total' => ['orders', fn ($m) => $m->refund('ORD1'), 'POST', '/v1/orders/ORD1/refund', []],
    'orders.refund parcial' => ['orders', fn ($m) => $m->refund('ORD1', ['transactions' => [['id' => 'PAY1', 'amount' => '1.00']]]), 'POST', '/v1/orders/ORD1/refund', ['transactions' => [['id' => 'PAY1', 'amount' => '1.00']]]],
    'orders.createTransaction' => ['orders', fn ($m) => $m->createTransaction('ORD1', ['payments' => []]), 'POST', '/v1/orders/ORD1/transactions', ['payments' => []]],
    'orders.updateTransaction' => ['orders', fn ($m) => $m->updateTransaction('ORD1', 'PAY1', ['amount' => '2']), 'PUT', '/v1/orders/ORD1/transactions/PAY1', ['amount' => '2']],
    'orders.deleteTransaction' => ['orders', fn ($m) => $m->deleteTransaction('ORD1', 'PAY1'), 'DELETE', '/v1/orders/ORD1/transactions/PAY1', null],
    // PreApproval
    'preApprovals.create' => ['preApprovals', fn ($m) => $m->create(['reason' => 'r']), 'POST', '/preapproval', ['reason' => 'r']],
    'preApprovals.get' => ['preApprovals', fn ($m) => $m->get('pa1'), 'GET', '/preapproval/pa1', null],
    'preApprovals.update' => ['preApprovals', fn ($m) => $m->update('pa1', ['status' => 'paused']), 'PUT', '/preapproval/pa1', ['status' => 'paused']],
    'preApprovals.search' => ['preApprovals', fn ($m) => $m->search(['status' => 'authorized']), 'GET', '/preapproval/search?status=authorized', null],
    'preApprovalPlans.create' => ['preApprovalPlans', fn ($m) => $m->create(['reason' => 'r']), 'POST', '/preapproval_plan', ['reason' => 'r']],
    'preApprovalPlans.get' => ['preApprovalPlans', fn ($m) => $m->get('pl1'), 'GET', '/preapproval_plan/pl1', null],
    'preApprovalPlans.update' => ['preApprovalPlans', fn ($m) => $m->update('pl1', ['reason' => 'z']), 'PUT', '/preapproval_plan/pl1', ['reason' => 'z']],
    'preApprovalPlans.search' => ['preApprovalPlans', fn ($m) => $m->search(['status' => 'active']), 'GET', '/preapproval_plan/search?status=active', null],
    'authorizedPayments.get' => ['authorizedPayments', fn ($m) => $m->get(77), 'GET', '/authorized_payments/77', null],
    'authorizedPayments.search' => ['authorizedPayments', fn ($m) => $m->search(['preapproval_id' => 'pa1']), 'GET', '/authorized_payments/search?preapproval_id=pa1', null],
    // Point
    'point.createPaymentIntent' => ['point', fn ($m) => $m->createPaymentIntent('DEV1', ['amount' => 1500]), 'POST', '/point/integration-api/devices/DEV1/payment-intents', ['amount' => 1500]],
    'point.getPaymentIntent' => ['point', fn ($m) => $m->getPaymentIntent('PI1'), 'GET', '/point/integration-api/payment-intents/PI1', null],
    'point.cancelPaymentIntent' => ['point', fn ($m) => $m->cancelPaymentIntent('DEV1', 'PI1'), 'DELETE', '/point/integration-api/devices/DEV1/payment-intents/PI1', null],
    'point.listPaymentIntents' => ['point', fn ($m) => $m->listPaymentIntents('2026-01-01', '2026-01-31'), 'GET', '/point/integration-api/payment-intents/events?startDate=2026-01-01&endDate=2026-01-31', null],
    'point.getPaymentIntentStatus' => ['point', fn ($m) => $m->getPaymentIntentStatus('PI1'), 'GET', '/point/integration-api/payment-intents/PI1/events', null],
    'point.getDevices' => ['point', fn ($m) => $m->getDevices(['store_id' => 1]), 'GET', '/point/integration-api/devices?store_id=1', null],
    'point.changeDeviceOperatingMode' => ['point', fn ($m) => $m->changeDeviceOperatingMode('DEV1', 'PDV'), 'PATCH', '/point/integration-api/devices/DEV1', ['operating_mode' => 'PDV']],
    // Advanced payments
    'advancedPayments.create' => ['advancedPayments', fn ($m) => $m->create(['payer' => []]), 'POST', '/v1/advanced_payments', ['payer' => []]],
    'advancedPayments.get' => ['advancedPayments', fn ($m) => $m->get(9), 'GET', '/v1/advanced_payments/9', null],
    'advancedPayments.search' => ['advancedPayments', fn ($m) => $m->search(['status' => 'approved']), 'GET', '/v1/advanced_payments/search?status=approved', null],
    'advancedPayments.update' => ['advancedPayments', fn ($m) => $m->update(9, ['metadata' => []]), 'PUT', '/v1/advanced_payments/9', ['metadata' => []]],
    'advancedPayments.capture' => ['advancedPayments', fn ($m) => $m->capture(9), 'PUT', '/v1/advanced_payments/9', ['capture' => true]],
    'advancedPayments.cancel' => ['advancedPayments', fn ($m) => $m->cancel(9), 'PUT', '/v1/advanced_payments/9', ['status' => 'cancelled']],
    'advancedPayments.updateReleaseDate' => ['advancedPayments', fn ($m) => $m->updateReleaseDate(9, '2026-09-10T00:00:00.000-03:00'), 'POST', '/v1/advanced_payments/9/disburses', ['money_release_date' => '2026-09-10T00:00:00.000-03:00']],
    'advancedPayments.listRefunds' => ['advancedPayments', fn ($m) => $m->listRefunds(9), 'GET', '/v1/advanced_payments/9/refunds', null],
    'advancedPayments.refundAll' => ['advancedPayments', fn ($m) => $m->refundAll(9), 'POST', '/v1/advanced_payments/9/refunds', []],
    'advancedPayments.refundDisbursement' => ['advancedPayments', fn ($m) => $m->refundDisbursement(9, 3, 2.5), 'POST', '/v1/advanced_payments/9/disbursements/3/refunds', ['amount' => 2.5]],
    // Users
    'users.me' => ['users', fn ($m) => $m->me(), 'GET', '/users/me', null],
]);

it('bate no verbo e path do SDK oficial com Bearer', function (string $group, Closure $call, string $verb, string $path, ?array $body) {
    $methods = MarketPlaces::MercadoPago()->{$group}(mpCoverageIntegration());

    expect($call($methods))->toBe(['ok' => true]);

    Http::assertSent(function (Request $req) use ($verb, $path, $body) {
        $urlOk = $req->url() === 'https://api.mercadopago.com'.$path;
        $bodyOk = $body === null || $req->data() === $body || ($body === [] && in_array($req->data(), [[], null], true));

        return $req->method() === $verb
            && $urlOk
            && $bodyOk
            && $req->hasHeader('Authorization', 'Bearer APP_USR-fake');
    });
})->with('mp_endpoints');

it('expoe TODOS os clients do SDK oficial (menos AdvancedPayment/Disbursement que viraram um so)', function () {
    $expected = [
        'payments', 'refunds', 'settlement', 'chargebacks', 'merchantOrders', 'preferences', 'customers',
        'cardTokens', 'catalog', 'orders', 'preApprovals', 'preApprovalPlans', 'authorizedPayments',
        'point', 'advancedPayments', 'users',
    ];

    $public = array_map(
        fn (ReflectionMethod $m) => $m->getName(),
        (new ReflectionClass(MercadoPago::class))->getMethods(ReflectionMethod::IS_PUBLIC),
    );

    expect(array_diff($expected, $public))->toBe([]);
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopify\Endpoints\Payments\DisputeEvidenceMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Payments\PaymentGatewayMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Payments\PaymentMethods;

require_once __DIR__.'/Helpers/ShopifyRest2Helpers.php';

beforeEach(function () {
    Http::preventStrayRequests();
});

describe('Shopify DisputeEvidenceMethods', function () {
    it('get', fn () => shopifyRest2Call(fn () => shopifyRest2Make(DisputeEvidenceMethods::class)->get(90), 'GET', 'shopify_payments/disputes/90/dispute_evidences.json'));
    it('update embrulha em dispute_evidence', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(DisputeEvidenceMethods::class)->update(90, ['uncategorized_text' => 'x', 'submit_evidence' => true]),
        'PUT', 'shopify_payments/disputes/90/dispute_evidences.json', ['dispute_evidence' => ['uncategorized_text' => 'x', 'submit_evidence' => true]],
    ));
    it('uploadFile embrulha em dispute_file_upload', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(DisputeEvidenceMethods::class)->uploadFile(90, ['evidence_type' => 'shipping_documentation', 'file' => 'QUJD', 'filename' => 'a.pdf']),
        'POST', 'shopify_payments/disputes/90/dispute_file_uploads.json', ['dispute_file_upload' => ['evidence_type' => 'shipping_documentation', 'file' => 'QUJD', 'filename' => 'a.pdf']],
    ));
    it('deleteFile', fn () => shopifyRest2Call(fn () => shopifyRest2Make(DisputeEvidenceMethods::class)->deleteFile(90, 3), 'DELETE', 'shopify_payments/disputes/90/dispute_file_uploads/3.json'));
});

describe('Shopify PaymentMethods (checkout)', function () {
    it('get', fn () => shopifyRest2Call(fn () => shopifyRest2Make(PaymentMethods::class)->get('tok', 4), 'GET', 'checkouts/tok/payments/4.json'));
    it('create embrulha em payment', fn () => shopifyRest2Call(
        fn () => shopifyRest2Make(PaymentMethods::class)->create('tok', ['amount' => '10.00', 'session_id' => 's']),
        'POST', 'checkouts/tok/payments.json', ['payment' => ['amount' => '10.00', 'session_id' => 's']],
    ));
});

describe('Shopify PaymentGatewayMethods', function () {
    it('list', fn () => shopifyRest2Call(fn () => shopifyRest2Make(PaymentGatewayMethods::class)->list(), 'GET', 'payment_gateways.json'));
    it('get', fn () => shopifyRest2Call(fn () => shopifyRest2Make(PaymentGatewayMethods::class)->get(1), 'GET', 'payment_gateways/1.json'));
    it('create embrulha', fn () => shopifyRest2Call(fn () => shopifyRest2Make(PaymentGatewayMethods::class)->create(['name' => 'g']), 'POST', 'payment_gateways.json', ['payment_gateway' => ['name' => 'g']]));
    it('update embrulha', fn () => shopifyRest2Call(fn () => shopifyRest2Make(PaymentGatewayMethods::class)->update(1, ['enabled' => false]), 'PUT', 'payment_gateways/1.json', ['payment_gateway' => ['enabled' => false]]));
    it('delete', fn () => shopifyRest2Call(fn () => shopifyRest2Make(PaymentGatewayMethods::class)->delete(1), 'DELETE', 'payment_gateways/1.json'));
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\MercadoLivre\Endpoints\Invoice\InvoiceMethods;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;
use SistemAtc\Marketplaces\MercadoLivre\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

function invoiceFiscalRulesIntegration(): FakeIntegration
{
    return new FakeIntegration(
        accessToken: 'ml-bearer',
        refreshToken: 'rt',
        settings: ['client_id' => 'cli', 'client_secret' => 'sec'],
        active: true,
        expired: false,
    );
}

function invoiceFiscalRulesMethods(): InvoiceMethods
{
    $integration = invoiceFiscalRulesIntegration();

    return new InvoiceMethods(HttpClientFactory::make($integration), $integration);
}

beforeEach(function () {
    config([
        'marketplaces.mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.api_base' => 'https://api.mercadolibre.com',
        'mercadolivre.access_token_ttl_seconds' => 21600,
        'mercadolivre.default_site_id' => 'MLB',
    ]);
    Http::preventStrayRequests();
});

describe('InvoiceMethods dados pra emissao (Enviar Nota Fiscal)', function () {
    it('getBillingInfoById faz GET /orders/billing-info/{site}/{id}', function () {
        Http::fake(['api.mercadolibre.com/orders/billing-info/MLB/677487519924852462' => Http::response(['site_id' => 'MLB', 'buyer' => ['cust_id' => 1]])]);

        $out = invoiceFiscalRulesMethods()->getBillingInfoById('677487519924852462');

        expect($out['site_id'])->toBe('MLB');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/orders/billing-info/MLB/677487519924852462'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('shipmentBillingInfo faz GET /shipments/{id}/billing_info', function () {
        Http::fake(['api.mercadolibre.com/shipments/26474580996/billing_info' => Http::response(['receiver' => ['id' => 154808171], 'senders' => [], 'carrier' => []])]);

        $out = invoiceFiscalRulesMethods()->shipmentBillingInfo(26474580996);

        expect($out['receiver']['id'])->toBe(154808171);
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/shipments/26474580996/billing_info'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('shipmentInvoiceData faz GET /shipments/{id}/invoice_data?siteId=MLB', function () {
        Http::fake(['api.mercadolibre.com/shipments/53932058390/invoice_data*' => Http::response(['id' => 429348053945853, 'status' => 'approved'])]);

        $out = invoiceFiscalRulesMethods()->shipmentInvoiceData(53932058390);

        expect($out['status'])->toBe('approved');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/shipments/53932058390/invoice_data?siteId=MLB'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('importShipmentInvoice faz POST XML cru em /shipments/{id}/invoice_data/?siteId=', function () {
        Http::fake(['api.mercadolibre.com/shipments/53932058390/invoice_data/*' => Http::response(['id' => 1, 'status' => 'approved'])]);

        $xml = '<?xml version="1.0" encoding="UTF-8"?><nfeProc versao="4.00"></nfeProc>';
        $out = invoiceFiscalRulesMethods()->importShipmentInvoice(53932058390, $xml);

        expect($out['status'])->toBe('approved');
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/shipments/53932058390/invoice_data/?siteId=MLB'
            && $req->body() === $xml
            && str_starts_with((string) $req->header('Content-Type')[0], 'application/xml')
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('importShipmentInvoice estoura exception no erro de negocio (400 duplicated_fiscal_key)', function () {
        Http::fake(['api.mercadolibre.com/shipments/1/invoice_data/*' => Http::response(['error_code' => 'duplicated_fiscal_key'], 400)]);

        invoiceFiscalRulesMethods()->importShipmentInvoice(1, '<nfeProc/>');
    })->throws(MercadoLivreRequestException::class);

    it('updateShipmentInvoice faz PUT XML cru em /shipment_invoice/{invoiceId}/?siteId=', function () {
        Http::fake(['api.mercadolibre.com/shipment_invoice/429348053945853/*' => Http::response(['id' => 429348053945853])]);

        $xml = '<?xml version="1.0" encoding="UTF-8"?><nfeProc versao="4.00"></nfeProc>';
        invoiceFiscalRulesMethods()->updateShipmentInvoice(429348053945853, $xml);

        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === 'https://api.mercadolibre.com/shipment_invoice/429348053945853/?siteId=MLB'
            && $req->body() === $xml
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });
});

describe('InvoiceMethods Faturador — can_invoice / tax_rules / mensagens', function () {
    it('canInvoice por item faz GET /can_invoice/items/{id}', function () {
        Http::fake(['api.mercadolibre.com/can_invoice/items/MLB1984512046' => Http::response(['item_id' => 'MLB1984512046', 'status' => true])]);

        $out = invoiceFiscalRulesMethods()->canInvoice('MLB1984512046');

        expect($out['status'])->toBeTrue();
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/can_invoice/items/MLB1984512046'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('canInvoice por variacao faz GET /can_invoice/items/{id}/variations/{vid}', function () {
        Http::fake(['api.mercadolibre.com/can_invoice/items/MLB1398143045/variations/94754627308' => Http::response(['variation_id' => '94754627308', 'status' => false])]);

        $out = invoiceFiscalRulesMethods()->canInvoice('MLB1398143045', 94754627308);

        expect($out['status'])->toBeFalse();
        Http::assertSent(fn ($req) => $req->url() === 'https://api.mercadolibre.com/can_invoice/items/MLB1398143045/variations/94754627308');
    });

    it('taxRules lista paginado em /users/{id}/invoices/tax_rules', function () {
        Http::fake(['api.mercadolibre.com/users/359450559/invoices/tax_rules*' => Http::response(['paging' => ['total' => 0], 'results' => []])]);

        invoiceFiscalRulesMethods()->taxRules(359450559, offset: 50, limit: 50);

        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && str_starts_with($req->url(), 'https://api.mercadolibre.com/users/359450559/invoices/tax_rules?')
            && str_contains($req->url(), 'offset=50')
            && str_contains($req->url(), 'limit=50')
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('taxRule faz GET /users/{id}/invoices/tax_rules/{ruleId}', function () {
        Http::fake(['api.mercadolibre.com/users/359450559/invoices/tax_rules/7' => Http::response(['id' => 7, 'description' => 'regras nacionais'])]);

        $out = invoiceFiscalRulesMethods()->taxRule(359450559, 7);

        expect($out['id'])->toBe(7);
        Http::assertSent(fn ($req) => $req->method() === 'GET' && $req->url() === 'https://api.mercadolibre.com/users/359450559/invoices/tax_rules/7');
    });

    it('createTaxRule faz POST com o JSON completo', function () {
        Http::fake(['api.mercadolibre.com/users/359450559/invoices/tax_rules' => Http::response(['id' => 8])]);

        $rule = ['description' => 'regras nacionais - revendedor', 'user_id' => 359450559, 'transactions' => [['transaction_type' => 'sale', 'operations' => []]]];
        $out = invoiceFiscalRulesMethods()->createTaxRule(359450559, $rule);

        expect($out['id'])->toBe(8);
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/users/359450559/invoices/tax_rules'
            && $req->data() === $rule
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('updateTaxRule faz PUT /users/{id}/invoices/tax_rules/{ruleId}', function () {
        Http::fake(['api.mercadolibre.com/users/359450559/invoices/tax_rules/6' => Http::response(['id' => 6])]);

        invoiceFiscalRulesMethods()->updateTaxRule(359450559, 6, ['description' => 'teste de PUT']);

        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === 'https://api.mercadolibre.com/users/359450559/invoices/tax_rules/6'
            && $req->data() === ['description' => 'teste de PUT']);
    });

    it('taxRuleMessages e taxRuleMessage fazem GET nos paths certos', function () {
        Http::fake([
            'api.mercadolibre.com/users/359450559/invoices/tax_rules/messages/49' => Http::response(['id' => 49, 'type' => 'item']),
            'api.mercadolibre.com/users/359450559/invoices/tax_rules/messages' => Http::response(['paging' => ['total' => 1], 'results' => [['id' => 49]]]),
        ]);

        $list = invoiceFiscalRulesMethods()->taxRuleMessages(359450559);
        $one = invoiceFiscalRulesMethods()->taxRuleMessage(359450559, 49);

        expect($list['results'][0]['id'])->toBe(49)->and($one['type'])->toBe('item');
        Http::assertSent(fn ($req) => $req->method() === 'GET' && $req->url() === 'https://api.mercadolibre.com/users/359450559/invoices/tax_rules/messages');
        Http::assertSent(fn ($req) => $req->method() === 'GET' && $req->url() === 'https://api.mercadolibre.com/users/359450559/invoices/tax_rules/messages/49');
    });

    it('createTaxRuleMessage faz POST com user_id, message e type', function () {
        Http::fake(['api.mercadolibre.com/users/359450559/invoices/tax_rules/messages' => Http::response(['id' => 49])]);

        invoiceFiscalRulesMethods()->createTaxRuleMessage(359450559, 'Pedido N: $EXTERNAL_ORDER_ID', 'COMPL');

        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/users/359450559/invoices/tax_rules/messages'
            && $req->data() === ['user_id' => 359450559, 'message' => 'Pedido N: $EXTERNAL_ORDER_ID', 'type' => 'COMPL']
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('updateTaxRuleMessage faz PUT e deleteTaxRuleMessage faz DELETE em /messages/{id}', function () {
        Http::fake(['api.mercadolibre.com/users/359450559/invoices/tax_rules/messages/2' => Http::response(['id' => 2])]);

        invoiceFiscalRulesMethods()->updateTaxRuleMessage(359450559, 2, 'Outra mensagem', 'ITEM');
        invoiceFiscalRulesMethods()->deleteTaxRuleMessage(359450559, 2);

        Http::assertSent(fn ($req) => $req->method() === 'PUT'
            && $req->url() === 'https://api.mercadolibre.com/users/359450559/invoices/tax_rules/messages/2'
            && $req->data() === ['user_id' => 359450559, 'message' => 'Outra mensagem', 'type' => 'ITEM']);
        Http::assertSent(fn ($req) => $req->method() === 'DELETE'
            && $req->url() === 'https://api.mercadolibre.com/users/359450559/invoices/tax_rules/messages/2');
    });

    it('additionalMessages CRUD usa /invoices/fiscal_rules/v2/additional-messages', function () {
        Http::fake([
            'api.mercadolibre.com/users/123456/invoices/fiscal_rules/v2/additional-messages/20775' => Http::response(['id' => 20775]),
            'api.mercadolibre.com/users/123456/invoices/fiscal_rules/v2/additional-messages' => Http::response([['id' => 20775]]),
        ]);

        $payload = ['title' => 'Mensagem custom', 'message' => 'IBPT: $IBPT_TOTAL_VALUE', 'type' => 'custom_with_filter', 'filters' => [['name' => 'transactiontype', 'operation' => 'eq', 'value' => 'SALE']]];

        $m = invoiceFiscalRulesMethods();
        $m->additionalMessages(123456);
        $m->additionalMessage(123456, 20775);
        $m->createAdditionalMessage(123456, $payload);
        $m->updateAdditionalMessage(123456, 20775, $payload);
        $m->deleteAdditionalMessage(123456, 20775);

        $base = 'https://api.mercadolibre.com/users/123456/invoices/fiscal_rules/v2/additional-messages';
        Http::assertSent(fn ($req) => $req->method() === 'GET' && $req->url() === $base);
        Http::assertSent(fn ($req) => $req->method() === 'GET' && $req->url() === "{$base}/20775");
        Http::assertSent(fn ($req) => $req->method() === 'POST' && $req->url() === $base && $req->data() === $payload && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
        Http::assertSent(fn ($req) => $req->method() === 'PUT' && $req->url() === "{$base}/20775" && $req->data() === $payload);
        Http::assertSent(fn ($req) => $req->method() === 'DELETE' && $req->url() === "{$base}/20775");
        Http::assertSentCount(5);
    });
});

describe('InvoiceMethods Faturador — emissao, erros, XML, lote mensal', function () {
    it('emitForOrders faz POST /users/{id}/invoices/orders com {orders: [...]}', function () {
        Http::fake(['api.mercadolibre.com/users/154808171/invoices/orders' => Http::response(['id' => 1, 'status' => 'authorized'])]);

        $out = invoiceFiscalRulesMethods()->emitForOrders(154808171, [2365536536, '4568709123']);

        expect($out['status'])->toBe('authorized');
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/users/154808171/invoices/orders'
            && $req->data() === ['orders' => [2365536536, 4568709123]]
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('emitForOrders estoura exception com error_code no corpo (400)', function () {
        Http::fake(['api.mercadolibre.com/users/1/invoices/orders' => Http::response(['message' => 'CPF do destinatário inválido', 'error_code' => '14'], 400)]);

        try {
            invoiceFiscalRulesMethods()->emitForOrders(1, [1]);
            $this->fail('deveria estourar');
        } catch (MercadoLivreRequestException $e) {
            expect($e->status())->toBe(400)->and($e->getMessage())->toContain('"error_code":"14"');
        }
    });

    it('invoiceError faz GET /users/invoices/errors/{site}/{code}', function () {
        Http::fake(['api.mercadolibre.com/users/invoices/errors/MLB/14' => Http::response(['id' => '14', 'display_message' => 'CPF do destinatário inválido'])]);

        $out = invoiceFiscalRulesMethods()->invoiceError(14);

        expect($out['display_message'])->toContain('CPF');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/users/invoices/errors/MLB/14'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('downloadAuthorizedXml devolve o XML cru de /documents/xml/{id}/authorized', function () {
        Http::fake(['api.mercadolibre.com/users/134608322/invoices/documents/xml/1377978/authorized' => Http::response('<?xml version="1.0"?><nfeProc/>')]);

        $xml = invoiceFiscalRulesMethods()->downloadAuthorizedXml(134608322, 1377978);

        expect($xml)->toContain('<nfeProc/>');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/users/134608322/invoices/documents/xml/1377978/authorized'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('batchDownloadInvoicesZipByMonth faz GET .../batch_request/period/AAAAMM e aceita AAAA-MM', function () {
        Http::fake(['api.mercadolibre.com/users/64196652/invoices/sites/MLB/batch_request/period/202605' => Http::response("PK\x03\x04zip")]);

        $zip = invoiceFiscalRulesMethods()->batchDownloadInvoicesZipByMonth(64196652, '2026-05');

        expect($zip)->toStartWith('PK');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/users/64196652/invoices/sites/MLB/batch_request/period/202605'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('batchDownloadInvoicesZipByMonth recusa periodo fora de AAAAMM', function () {
        Http::fake();

        expect(fn () => invoiceFiscalRulesMethods()->batchDownloadInvoicesZipByMonth(1, '2026-05-01'))->toThrow(InvalidArgumentException::class);
        Http::assertNothingSent();
    });
});

describe('InvoiceMethods DC-e (MLB)', function () {
    it('dceEmit faz POST /mlb/order/{id}/dce/emission', function () {
        Http::fake(['api.mercadolibre.com/mlb/order/2000000483899592/dce/emission' => Http::response(['id' => '94e95c5fc1356f8e4c2bf779dce40000'])]);

        $out = invoiceFiscalRulesMethods()->dceEmit(2000000483899592);

        expect($out['id'])->toBe('94e95c5fc1356f8e4c2bf779dce40000');
        Http::assertSent(fn ($req) => $req->method() === 'POST'
            && $req->url() === 'https://api.mercadolibre.com/mlb/order/2000000483899592/dce/emission'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('dceInfo faz GET /mlb/order/{id}/dce/info', function () {
        Http::fake(['api.mercadolibre.com/mlb/order/2000000483899592/dce/info' => Http::response(['status' => 'completed', 'documents' => [['dce_key' => 'doc-789']]])]);

        $out = invoiceFiscalRulesMethods()->dceInfo(2000000483899592);

        expect($out['documents'][0]['dce_key'])->toBe('doc-789');
        Http::assertSent(fn ($req) => $req->method() === 'GET' && $req->url() === 'https://api.mercadolibre.com/mlb/order/2000000483899592/dce/info');
    });

    it('dceDownload faz GET /mlb/order/{id}/dce/info/{dceKey}?doctype= e devolve o binario', function () {
        Http::fake(['api.mercadolibre.com/mlb/order/2000000987654321/dce/info/doc-789*' => Http::response('%PDF-1.4')]);

        $pdf = invoiceFiscalRulesMethods()->dceDownload(2000000987654321, 'doc-789', 'pdf');

        expect($pdf)->toStartWith('%PDF');
        Http::assertSent(fn ($req) => $req->method() === 'GET'
            && $req->url() === 'https://api.mercadolibre.com/mlb/order/2000000987654321/dce/info/doc-789?doctype=pdf'
            && $req->hasHeader('Authorization', 'Bearer ml-bearer'));
    });

    it('dceDownload recusa doctype desconhecido', function () {
        Http::fake();

        expect(fn () => invoiceFiscalRulesMethods()->dceDownload(1, 'doc', 'docx'))->toThrow(InvalidArgumentException::class);
        Http::assertNothingSent();
    });
});

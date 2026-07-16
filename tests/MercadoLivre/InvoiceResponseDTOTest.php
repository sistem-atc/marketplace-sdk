<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice\InvoiceAttributes;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice\InvoiceItem;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice\InvoiceResponseDTO;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common\ReferenceInvoice;

/**
 * Payload SINTETICO (mesma shape da API ML, dados fake — sem PII real) da
 * resposta de invoice-metadata (getByOrderId/getById/getByShipment).
 */
function fakeMlInvoicePayload(): array
{
    return [
        'id' => 6481341052,
        'status' => 'authorized',
        'transaction_status' => 'authorized',
        'issuer' => [
            'user_id' => '64196652',
            'name' => 'EMPRESA FAKE LTDA',
            'phone' => ['area_code' => '11', 'number' => '900000000'],
            'address' => ['street_name' => 'Rua Fake', 'street_number' => '100', 'city' => 'Sao Paulo', 'state' => 'SP', 'zip_code' => '00000000', 'country' => 'BR'],
            'identifications' => ['cnpj' => '00000000000000', 'crt' => 'normal', 'ie' => '000000000000'],
        ],
        'recipient' => [
            'external_recipient_id' => '111',
            'name' => 'Cliente Fake',
            'identifications' => ['cpf' => '00000000000'],
        ],
        'shipment' => [
            'id' => 47476563359,
            'site_id' => 'MLB',
            'mode' => 'ME2',
            'logistic_type' => 'fulfillment',
            'buyer_cost' => 0,
            'carrier' => ['name' => 'OPERADOR FAKE', 'identifications' => ['cnpj' => '11111111111111']],
            'volumes' => [['net_weight' => 1.04, 'gross_weight' => 1.04]],
            'fiscal_model_id' => 'OLSS',
        ],
        'items' => [[
            'id' => 'X_1',
            'invoice_id' => '6481341052',
            'seller_id' => '64196652',
            'external_order_id' => '2000017314856630',
            'external_product_id' => 'MLB2766771378',
            'attributes' => ['ean' => '7909389213120', 'sku' => 'SKU-1', 'type' => 'single', 'bundle_quantity' => 1],
            'product_name' => 'Produto Fake',
            'quantity' => 1,
            'total_amount' => 59.9,
            'discount_amount' => ['unconditional' => 5, 'conditional' => null],
            'fiscal_data' => [
                'attributes' => ['ncm' => '21069030', 'cest' => '2806200', 'cfop' => '6106', 'measurement_unit' => 'UN', 'origin_type' => 'reseller'],
                'messages' => [['type' => 'ITEM', 'content' => 'tributos ...']],
                'rules' => [
                    ['name' => 'ICMS', 'attributes' => ['cst' => '00', 'vicms' => 6.59, 'picms' => 12]],
                    ['name' => 'PIS', 'attributes' => ['vpis' => 0.8, 'cst' => '01']],
                ],
            ],
        ]],
        'issued_date' => '2026-07-10T17:15:37.243Z',
        'invoice_series' => '2',
        'invoice_number' => 8197641,
        'attributes' => [
            'invoice_key' => '35260724829330000152550020081976411514323543',
            'xml_location' => 'https://api.mercadolibre.com/xml/abc',
            'status_code' => '138',
            'invoice_type' => 'SALE',
            'reference_invoices' => [['id' => 6523665791, 'invoice_key' => '35260724829330000152550020082523711783524856']],
        ],
        'fiscal_data' => [
            'customer_type' => 'b2c',
            'transaction_type' => 'sale',
            'transaction_type_description' => 'Venda de mercadorias',
            'fiscal_amounts' => [['name' => 'icms', 'attributes' => ['vicms' => 6.59]]],
        ],
        'amount' => 54.9,
        'items_amount' => 59.9,
        'items_quantity' => 1,
        'pack_id' => null,
        'site_id' => 'mlb',
    ];
}

it('hidrata a árvore INTEIRA da resposta ML (raiz + nested tipados)', function () {
    $dto = InvoiceResponseDTO::fromArray(fakeMlInvoicePayload());

    // raiz
    expect($dto)->toBeInstanceOf(InvoiceResponseDTO::class);
    expect($dto->id)->toBe(6481341052);
    expect($dto->status)->toBe('authorized');
    expect($dto->invoiceNumber)->toBe(8197641);
    expect($dto->amount)->toBe(54.9);

    // bloco fiscal/SEFAZ em ->attributes (InvoiceAttributes)
    expect($dto->attributes)->toBeInstanceOf(InvoiceAttributes::class);
    expect($dto->attributes->invoiceKey)->toBe('35260724829330000152550020081976411514323543');
    expect($dto->attributes->xmlLocation)->toBe('https://api.mercadolibre.com/xml/abc');
    expect($dto->attributes->statusCode)->toBe('138');
    expect($dto->isAuthorized())->toBeTrue();

    // reference_invoices aninhado tipado
    expect($dto->attributes->referenceInvoices[0])->toBeInstanceOf(ReferenceInvoice::class);
    expect($dto->attributes->referenceInvoices[0]->id)->toBe(6523665791);

    // issuer/recipient/shipment
    expect($dto->issuer->identifications->cnpj)->toBe('00000000000000');
    expect($dto->issuer->phone->areaCode)->toBe('11');
    expect($dto->recipient->identifications->cpf)->toBe('00000000000');
    expect($dto->shipment->logisticType)->toBe('fulfillment');
    expect($dto->shipment->volumes[0]->grossWeight)->toBe(1.04);

    // items + CFOP (o sinal de classificação — de bandeja)
    expect($dto->items[0])->toBeInstanceOf(InvoiceItem::class);
    expect($dto->items[0]->fiscalData->attributes->cfop)->toBe('6106');
    expect($dto->items[0]->fiscalData->rules[0]->name)->toBe('ICMS');
    expect($dto->items[0]->fiscalData->rules[0]->attributes)->toBe(['cst' => '00', 'vicms' => 6.59, 'picms' => 12]);

    // fiscal_data raiz (transaction_type = sinal do tipo de operação)
    expect($dto->fiscalData->transactionType)->toBe('sale');
    expect($dto->fiscalData->transactionTypeDescription)->toBe('Venda de mercadorias');
});

it('devolve null/[] nos campos ausentes sem quebrar', function () {
    $dto = InvoiceResponseDTO::fromArray(['id' => 1]);

    expect($dto->id)->toBe(1);
    expect($dto->attributes)->toBeNull();
    expect($dto->items)->toBe([]);
    expect($dto->isAuthorized())->toBeFalse();
});

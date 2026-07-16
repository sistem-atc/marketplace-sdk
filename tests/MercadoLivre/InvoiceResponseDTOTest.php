<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice\InvoiceResponseDTO;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common\ReferenceInvoice;

it('hidrata o DTO a partir do attributes cru (snake_case) do ML', function () {
    $attributes = [
        'invoice_key' => '35260724829330000152550020081976411514323543',
        'xml_location' => 'https://api.mercadolibre.com/xml/abc',
        'danfe_location' => 'https://api.mercadolibre.com/danfe/abc',
        'status_code' => '138',
        'status_description' => 'Documento localizado',
        'invoice_type' => 'SALE',
        'emission_type' => 'NORMAL',
        'authorization_date' => '2026-07-10T14:15:31.000Z',
        'include_freight' => true,
        'reference_invoices' => [
            ['id' => 6523665791, 'invoice_key' => '35260724829330000152550020082523711783524856'],
        ],
        'tags' => ['fulfillment'],
    ];

    $dto = InvoiceResponseDTO::fromArray($attributes);

    expect($dto)->toBeInstanceOf(InvoiceResponseDTO::class);
    // snake_case -> camelCase idiomatico
    expect($dto->invoiceKey)->toBe('35260724829330000152550020081976411514323543');
    expect($dto->xmlLocation)->toBe('https://api.mercadolibre.com/xml/abc');
    expect($dto->statusCode)->toBe('138');
    expect($dto->includeFreight)->toBeTrue();
    expect($dto->isAuthorized())->toBeTrue();

    // nested: reference_invoices vira lista de ReferenceInvoice tipado
    expect($dto->referenceInvoices)->toHaveCount(1);
    expect($dto->referenceInvoices[0])->toBeInstanceOf(ReferenceInvoice::class);
    expect($dto->referenceInvoices[0]->id)->toBe(6523665791);
    expect($dto->referenceInvoices[0]->invoiceKey)->toBe('35260724829330000152550020082523711783524856');
});

it('devolve null nos campos ausentes sem quebrar', function () {
    $dto = InvoiceResponseDTO::fromArray(['invoice_key' => '35'.str_repeat('0', 42)]);

    expect($dto->invoiceKey)->toBe('35'.str_repeat('0', 42));
    expect($dto->xmlLocation)->toBeNull();
    expect($dto->referenceInvoices)->toBe([]);
    expect($dto->isAuthorized())->toBeFalse();
});

it('toArray round-trip volta pra snake_case, omitindo nulos e aninhando', function () {
    $dto = InvoiceResponseDTO::fromArray([
        'invoice_key' => 'K',
        'status_code' => '138',
        'reference_invoices' => [['id' => 1, 'invoice_key' => 'R']],
    ]);

    $arr = $dto->toArray();

    expect($arr['invoice_key'])->toBe('K');
    expect($arr['status_code'])->toBe('138');
    expect($arr)->not->toHaveKey('xml_location'); // nulo omitido
    expect($arr['reference_invoices'][0])->toBe(['id' => 1, 'invoice_key' => 'R']);
});

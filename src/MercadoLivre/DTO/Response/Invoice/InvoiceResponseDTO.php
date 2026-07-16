<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * RAIZ da resposta de invoice-metadata do ML — GET de:
 *   - /users/{seller}/invoices/orders/{orderId}     (getByOrderId)
 *   - /users/{userId}/invoices/{invoiceId}          (getById)
 *   - /users/{seller}/invoices/shipments/{shipment} (getByShipment)
 *
 * As 3 chamadas devolvem esta mesma shape. Este DTO e' o UNICO ponto de parse —
 * a arvore inteira e' tipada (issuer/recipient/shipment/items/fiscal_data), com
 * o bloco de emissao/SEFAZ em `->attributes` (InvoiceAttributes). Consumidores
 * usam `->attributes->invoiceKey`, `->items[0]->fiscalData->attributes->cfop`,
 * `->issuer->identifications->cnpj` etc. em vez de array cru.
 *
 * @property list<InvoiceItem> $items
 * @property array<int|string, mixed> $errors
 * @property array<int|string, mixed> $payments
 */
final class InvoiceResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<InvoiceItem>  $items
     * @param  array<int|string, mixed>  $errors
     * @param  array<int|string, mixed>  $payments
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $status = null,
        public readonly ?string $transactionStatus = null,
        public readonly ?Issuer $issuer = null,
        public readonly ?Recipient $recipient = null,
        public readonly ?Shipment $shipment = null,
        #[ArrayOf(InvoiceItem::class)]
        public readonly array $items = [],
        public readonly ?string $issuedDate = null,
        public readonly ?string $invoiceSeries = null,
        public readonly ?int $invoiceNumber = null,
        public readonly ?InvoiceAttributes $attributes = null,
        public readonly ?InvoiceFiscalData $fiscalData = null,
        public readonly ?float $amount = null,
        public readonly ?float $itemsAmount = null,
        public readonly array $errors = [],
        public readonly ?int $itemsQuantity = null,
        public readonly ?int $packId = null,
        public readonly ?string $customIssuerAddress = null,
        public readonly ?string $siteId = null,
        public readonly ?float $otherAmount = null,
        public readonly ?string $additionalInfo = null,
        public readonly array $payments = [],
    ) {}

    /** NFe localizada/autorizada na SEFAZ — delega pro bloco `attributes`. */
    public function isAuthorized(): bool
    {
        return $this->attributes?->isAuthorized() ?? false;
    }
}

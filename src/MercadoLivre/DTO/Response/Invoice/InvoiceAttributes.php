<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common\ReferenceInvoice;

/**
 * Bloco `attributes` da nota — metadados de emissao/SEFAZ (chave, protocolo,
 * status, localizacao do XML/DANFE, notas referenciadas etc.).
 *
 * Consumidores usam `->invoiceKey`, `->xmlLocation`, `->referenceInvoices[0]->id`
 * em vez de array cru.
 */
final class InvoiceAttributes implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<ReferenceInvoice>  $referenceInvoices
     * @param  array<int|string, mixed>  $tags
     * @param  array<int|string, mixed>  $thirdPartyAuthorizations
     */
    public function __construct(
        public readonly ?string $invoiceKey = null,
        public readonly ?string $xmlLocation = null,
        public readonly ?string $danfeLocation = null,
        public readonly ?string $statusCode = null,
        public readonly ?string $statusDescription = null,
        public readonly ?string $invoiceType = null,
        public readonly ?string $emissionType = null,
        public readonly ?string $environmentType = null,
        public readonly ?string $documentType = null,
        public readonly ?string $orderSource = null,
        public readonly ?string $invoiceSource = null,
        public readonly ?string $xmlVersion = null,
        public readonly ?string $protocol = null,
        public readonly ?string $protocolDueDate = null,
        public readonly ?string $receipt = null,
        public readonly ?string $receiptDate = null,
        public readonly ?string $invoiceCreationDate = null,
        public readonly ?string $authorizationDate = null,
        public readonly ?string $cancellationDate = null,
        public readonly ?string $cancellationProtocol = null,
        public readonly ?string $cancellationReason = null,
        public readonly ?string $cancellationErrorCode = null,
        public readonly ?string $cancellationErrorDescription = null,
        public readonly ?string $document = null,
        public readonly ?string $danfe = null,
        public readonly ?string $cnf = null,
        public readonly ?string $correctionLetter = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?bool $includeFreight = null,
        public readonly ?ReferenceInvoice $referenceInvoice = null,
        #[ArrayOf(ReferenceInvoice::class)]
        public readonly array $referenceInvoices = [],
        public readonly array $tags = [],
        public readonly array $thirdPartyAuthorizations = [],
    ) {}

    /** NFe localizada/autorizada na SEFAZ (cStat 138 = documento localizado). */
    public function isAuthorized(): bool
    {
        return $this->statusCode === '138' || $this->authorizationDate !== null;
    }
}

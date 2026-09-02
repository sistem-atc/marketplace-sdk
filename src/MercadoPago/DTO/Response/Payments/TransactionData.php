<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents transaction-level data generated at the point of interaction in the MercadoPago API.
 * Contains QR code content, ticket URLs, and bank transfer details for Pix, boleto, and other
 * payment methods. Nested within PointOfInteraction.
 */
final class TransactionData implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** QR code content string (e.g. Pix EMV payload). */
        public readonly ?string $qrCode = null,

        /** Base64-encoded QR code image for rendering in the payer's UI. */
        public readonly ?string $qrCodeBase64 = null,

        /** Unique transaction identifier within the point of interaction. */
        public readonly ?string $transactionId = null,

        /** Identifier of the bank transfer operation. */
        public readonly ?int $bankTransferId = null,

        /** Numeric identifier of the financial institution processing the transfer. */
        public readonly ?int $financialInstitution = null,

        /** Bank account details of payer and collector for bank transfers. */
        public readonly ?BankInfo $bankInfo = null,

        /** URL where the payer can view/download the payment ticket (e.g. boleto PDF). */
        public readonly ?string $ticketUrl = null,

        /** End-to-end identifier for Pix transactions, used for reconciliation. */
        public readonly ?string $e2eId = null,

        /** Legacy card-network transaction identifier within transaction data. */
        public readonly ?string $networkTransactionId = null,

        /** Card-network identifiers for this COF transaction. */
        public readonly ?NetworkData $networkData = null,

        /** Indicates whether this is the first transaction in a CREDENTIAL_ON_FILE agreement. */
        public readonly ?bool $firstTransaction = null,

        /** Storage type for the credential-on-file agreement (e.g. "ON_DEMAND", "RECURRING"). */
        public readonly ?string $storage = null,

        /** Initiator of the transaction in a CREDENTIAL_ON_FILE flow (e.g. "MERCHANT", "CUSTOMER"). */
        public readonly ?string $transactionInitiator = null,

        /** Reference to the original stored-credential transaction. */
        public readonly ?TransactionDataReference $reference = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the financial details of a payment transaction in the MercadoPago API. Contains
 * amounts (net, total, installment), payment processor references, and offline payment data
 * (barcode, digitable line). Nested within Payment.
 */
final class TransactionDetails implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Name or code of the financial institution that processed the payment. */
        public readonly ?string $financialInstitution = null,

        /** Net amount received by the seller after all fees are deducted. */
        public readonly ?float $netReceivedAmount = null,

        /** Total amount paid by the buyer, including fees and taxes. */
        public readonly ?float $totalPaidAmount = null,

        /** Amount of each installment when paying in installments. */
        public readonly ?float $installmentAmount = null,

        /** Amount overpaid by the buyer (applies only to ticket/offline payment methods). */
        public readonly ?float $overpaidAmount = null,

        /** URL to the payment resource on the processor's system (e.g. boleto PDF). */
        public readonly ?string $externalResourceUrl = null,

        /** Reference ID from the payment method processor. For credit card payments this is the USN (Unique Sequence Number). For offline methods, it is the reference code to present at the cashier or ATM. */
        public readonly ?string $paymentMethodReferenceId = null,

        /** Reference identifier assigned by the acquirer for reconciliation. */
        public readonly ?string $acquirerReference = null,

        /** Deferral period before the payment becomes payable to the seller. */
        public readonly ?string $payableDeferralPeriod = null,

        /** Identifier of the bank transfer operation. */
        public readonly ?string $bankTransferId = null,

        /** Transaction identifier within the payment processor. */
        public readonly ?string $transactionId = null,

        /** Barcode data for offline payment methods (e.g. boleto). */
        public readonly ?Barcode $barcode = null,

        /** Digitable line for boleto payments (typed barcode representation). */
        public readonly ?string $digitableLine = null,

        /** Verification code for the payment transaction. */
        public readonly ?string $verificationCode = null,
    ) {}
}

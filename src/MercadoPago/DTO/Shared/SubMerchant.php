<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Shared;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a sub-merchant (payment facilitator participant) in the MercadoPago API. Contains
 * identification and location data for the sub-merchant receiving the payment in a payment
 * facilitator (PayFac) model. Sent within ForwardData to comply with card brand and regulatory
 * requirements.
 */
final class SubMerchant implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique identifier assigned to the sub-merchant by the payment facilitator. */
        public readonly ?string $subMerchantId = null,

        /** Merchant Category Code (MCC) per ABECS/CNAE classification. */
        public readonly ?string $mcc = null,

        /** ISO 3166-1 country code where the sub-merchant operates. */
        public readonly ?string $country = null,

        /** Street/door number of the sub-merchant's address. */
        public readonly ?MercadoPago\Resources\Common\number $addressDoorNumber = null,

        /** Postal code (CEP) of the sub-merchant's address. */
        public readonly ?string $zip = null,

        /** Tax identification number of the sub-merchant (e.g. CPF or CNPJ). */
        public readonly ?string $documentNumber = null,

        /** City where the sub-merchant is located. */
        public readonly ?string $city = null,

        /** Street name of the sub-merchant's address. */
        public readonly ?string $addressStreet = null,

        /** Registered business name of the sub-merchant. */
        public readonly ?string $businessName = null,

        /** ISO state/region code where the sub-merchant is located. */
        public readonly ?string $regionCodeIso = null,

        /** Region or state code of the sub-merchant. */
        public readonly ?string $regionCode = null,

        /** Type of identification document (e.g. "CPF", "CNPJ"). */
        public readonly ?string $documentType = null,

        /** Contact phone number of the sub-merchant. */
        public readonly ?string $phone = null,

        /** URL of the payment facilitator or sub-merchant website. */
        public readonly ?string $url = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Identification;

/**
 * User resource. Represents a MercadoPago/MercadoLibre user account. Contains comprehensive
 * profile information including personal details, identification, contact info, company data,
 * seller/buyer reputation metrics, account status, and credit information.
 */
final class UserResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The user's ID. */
        public readonly ?int $id = null,

        /** The user's nickname. */
        public readonly ?string $nickname = null,

        /** The registration date and time. */
        public readonly ?string $registrationDate = null,

        /** The user's first name. */
        public readonly ?string $firstName = null,

        /** The user's last name. */
        public readonly ?string $lastName = null,

        /** The user's gender. */
        public readonly ?string $gender = null,

        /** The country ID (e.g., "BR" for Brazil). */
        public readonly ?string $countryId = null,

        /** The user's email. */
        public readonly ?string $email = null,

        /** User identification data. */
        public readonly ?Identification $identification = null,

        /** User address data. */
        public readonly ?Address $address = null,

        /** User phone data. */
        public readonly ?Phone $phone = null,

        /** User alternative phone data. */
        public readonly ?AlternativePhone $alternativePhone = null,

        /** The user type (e.g., "normal"). */
        public readonly ?string $userType = null,

        /** User tags. */
        public readonly ?array $tags = null,

        /** User logo. */
        public readonly mixed $logo = null,

        /** User points. */
        public readonly ?int $points = null,

        /** The site ID (e.g., "MLB" for MercadoLibre Brazil). */
        public readonly ?string $siteId = null,

        /** User permalink. */
        public readonly ?string $permalink = null,

        /** Seller experience (e.g., "NEWBIE"). */
        public readonly ?string $sellerExperience = null,

        /** User bill data. */
        public readonly ?BillData $billData = null,

        /** User seller reputation data. */
        public readonly ?SellerReputation $sellerReputation = null,

        /** User buyer reputation data. */
        public readonly ?BuyerReputation $buyerReputation = null,

        /** User status data. */
        public readonly ?Status $status = null,

        /** Secure email. */
        public readonly ?string $secureEmail = null,

        /** User company data. */
        public readonly ?Company $company = null,

        /** User credit data. */
        public readonly ?Credit $credit = null,

        /** User context data. */
        public readonly ?Context $context = null,

        /** User registration identifiers. */
        public readonly ?array $registrationIdentifiers = null,
    ) {}
}

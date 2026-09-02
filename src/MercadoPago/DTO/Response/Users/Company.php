<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Company resource. Represents the business/company information associated with a user
 * account, including brand name, corporate name, tax identifiers, and payment descriptor.
 */
final class Company implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The brand name of the company. */
        public readonly ?string $brandName = null,

        /** The city tax ID of the company. */
        public readonly ?string $cityTaxId = null,

        /** The corporate name of the company. */
        public readonly ?string $corporateName = null,

        /** The identification of the company. */
        public readonly ?string $identification = null,

        /** The state tax ID of the company. */
        public readonly ?string $stateTaxId = null,

        /** The customer type ID of the company (e.g., "CO"). */
        public readonly ?string $custTypeId = null,

        /** The soft descriptor of the company. */
        public readonly ?string $softDescriptor = null,
    ) {}
}

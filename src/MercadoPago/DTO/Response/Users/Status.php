<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Account Status resource. Represents the overall account status of a
 * MercadoPago/MercadoLibre user, including buying/selling permissions, email confirmation,
 * MercadoEnvios status, account type, and per-feature status details (billing, shopping cart,
 * listing). Fields are mapped to nested DTOs: - billing -> StatusBilling - buy -> StatusList -
 * shopping_cart -> StatusShoppingCart - list -> StatusList - sell -> StatusList
 */
final class Status implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** User billing status data. */
        public readonly ?StatusBilling $billing = null,

        /** User buy status data. */
        public readonly ?StatusList $buy = null,

        /** Indicates whether the user's email has been confirmed (true/false). */
        public readonly ?bool $confirmedEmail = null,

        /** User shopping cart status data. */
        public readonly ?StatusShoppingCart $shoppingCart = null,

        /** Indicates whether immediate payment is enabled (true/false). */
        public readonly ?bool $immediatePayment = null,

        /** User list status data. */
        public readonly ?StatusList $list = null,

        /** Indicates the MercadoEnvios status. */
        public readonly ?string $mercadoenvios = null,

        /** Indicates the MercadoPago account type (e.g., "personal"). */
        public readonly ?string $mercadopagoAccountType = null,

        /** Indicates whether MercadoPago credit card is accepted (true/false). */
        public readonly ?bool $mercadopagoTcAccepted = null,

        /** Required action. */
        public readonly mixed $requiredAction = null,

        /** User sell status data. */
        public readonly ?StatusList $sell = null,

        /** Site status (e.g., "active"). */
        public readonly ?string $siteStatus = null,

        /** User type. */
        public readonly ?string $userType = null,
    ) {}
}

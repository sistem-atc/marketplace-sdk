<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\MerchantOrders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Merchant Order Payer (buyer) resource. Represents the buyer who pays for a merchant order.
 * Contains basic identification and contact details of the paying party.
 */
final class Payer implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Payer ID. */
        public readonly ?int $id = null,

        /** Payer nickname. */
        public readonly ?string $nickname = null,

        /** Payer email. */
        public readonly ?string $email = null,
    ) {}
}

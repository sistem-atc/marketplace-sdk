<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\AuthorizedPayments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Invoice Payment resource. Represents the payment resulting from a subscription invoice charge
 * attempt. Contains the payment ID, its processing status, and a detailed status reason.
 */
final class Payment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The ID of the payment. */
        public readonly ?int $id = null,

        /** Status of the invoice. */
        public readonly ?string $status = null,

        /** Status detail. */
        public readonly ?string $statusDetail = null,
    ) {}
}

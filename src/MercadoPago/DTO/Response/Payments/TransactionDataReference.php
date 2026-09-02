<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents the reference data associated with a CREDENTIAL_ON_FILE transaction in the
 * MercadoPago API. Contains identifiers that link the current transaction to a prior agreement or
 * stored credential. Nested within TransactionData.
 */
final class TransactionDataReference implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Identifier of the original transaction used as the stored credential reference. */
        public readonly ?string $id = null,
    ) {}
}

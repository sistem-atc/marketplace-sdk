<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Shared;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a personal or legal identification document in the MercadoPago API. Used to identify
 * payers, cardholders, and sub-merchants through government-issued documents such as CPF, CNPJ,
 * DNI, or similar national IDs.
 */
final class Identification implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Document type code (e.g. "CPF", "CNPJ", "DNI", "CC"). */
        public readonly ?string $type = null,

        /** Document number corresponding to the identification type. */
        public readonly ?string $number = null,
    ) {}
}

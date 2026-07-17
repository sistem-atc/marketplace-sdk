<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Comprador (`customer`). `documentNumber` = CPF/CNPJ; `customerType` = pessoa
 * física/jurídica. PII sem máscara no Magalu.
 */
final class Customer implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $documentNumber = null,
        public readonly ?string $customerType = null,
        public readonly ?string $birthDate = null,
    ) {}
}

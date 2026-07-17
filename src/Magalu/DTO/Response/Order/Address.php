<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Endereço (destinatário da entrega e retirada em loja — mesma shape). */
final class Address implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $street = null,
        public readonly ?string $number = null,
        public readonly ?string $complement = null,
        public readonly ?string $district = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?string $country = null,
        public readonly ?string $zipcode = null,
        public readonly ?string $reference = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common\Address;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common\Identifications;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common\Phone;

/**
 * Emissor da nota (`issuer`) — o seller.
 */
final class Issuer implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $userId = null,
        public readonly ?string $brandName = null,
        public readonly ?string $name = null,
        public readonly ?Phone $phone = null,
        public readonly ?Address $address = null,
        public readonly ?Identifications $identifications = null,
    ) {}
}

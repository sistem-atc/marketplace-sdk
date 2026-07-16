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
 * Destinatario da nota (`recipient`) — o comprador.
 */
final class Recipient implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $externalRecipientId = null,
        public readonly ?string $name = null,
        public readonly ?Phone $phone = null,
        public readonly ?Address $address = null,
        public readonly ?Identifications $identifications = null,
    ) {}
}

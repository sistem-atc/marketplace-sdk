<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Contato do fabricante embutido no produto (`manufacturer`).
 *
 * DEPRECIADO pela API: quando ha mais de um fabricante, so' o PRIMEIRO
 * cadastrado aparece aqui. A fonte correta e' `manufacturerIds` +
 * Search Manufacturers. Mapeado assim mesmo pra nao perder dado de
 * produtos antigos que so' tem este campo.
 */
final class GlobalManufacturerContact implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $address = null,
        public readonly ?string $phoneNumber = null,
        public readonly ?string $email = null,
    ) {}
}

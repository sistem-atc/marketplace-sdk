<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Comprador (`buyer`) de um pedido ML.
 */
final class Buyer implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int|string, mixed>|null $billingInfo */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $nickname = null,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?array $billingInfo = null,
    ) {}
}

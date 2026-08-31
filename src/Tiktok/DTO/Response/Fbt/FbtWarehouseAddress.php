<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Endereco fisico de um armazem FBT (`data.warehouses[].addresses[]`).
 *
 * ARMADILHA DE CHAVE: aqui o TikTok manda `address_line_1` (COM underscore
 * antes do digito), enquanto no endereco do PEDIDO manda `address_line1` (SEM).
 * Sao APIs do mesmo canal com convencoes diferentes — por isso o #[JsonKey]
 * explicito: a conversao automatica camelCase->snake_case geraria
 * `address_line1` e o campo sumiria em silencio no roundtrip.
 */
final class FbtWarehouseAddress implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $postalCode = null,
        // ISO 3166-1 alpha-2 (US, GB, BR...)
        public readonly ?string $regionCode = null,
        public readonly ?string $state = null,
        public readonly ?string $district = null,
        public readonly ?string $city = null,
        #[JsonKey('address_line_1')]
        public readonly ?string $addressLine1 = null,
        #[JsonKey('address_line_2')]
        public readonly ?string $addressLine2 = null,
        #[JsonKey('address_line_3')]
        public readonly ?string $addressLine3 = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Coordenada do endereço do armazém. Lat/long vêm como STRING ("45.41634") —
 * tipar float aqui perderia dígitos de precisão no roundtrip.
 */
final class Geolocation implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $latitude = null,
        public readonly ?string $longitude = null,
    ) {}
}

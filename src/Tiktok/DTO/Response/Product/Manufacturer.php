<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Fabricante cadastrado (compliance GPSR da UE).
 *
 * E' cadastro de LOJA, nao de produto: o produto so' referencia o id em
 * `manufacturerIds`. Aplicavel so' ao mercado UE, em certas categorias.
 *
 * @property list<ManufacturerRegionalProfile>|null $regionalProfiles
 */
final class Manufacturer implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        #[ArrayOf(ManufacturerRegionalProfile::class)]
        public readonly ?array $regionalProfiles = null,
    ) {}
}

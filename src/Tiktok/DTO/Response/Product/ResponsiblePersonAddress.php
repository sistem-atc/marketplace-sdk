<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Endereco da pessoa responsavel — objeto estruturado (o do fabricante e'
 * string unica).
 *
 * A API DEPRECIOU line2/district/city/province: passaram a devolver string
 * vazia e todo o detalhe vive em `streetAddressLine1`. Mapeados mesmo assim
 * porque a API continua mandando as chaves — descartar quebraria o roundtrip
 * e esconderia dado de cadastros antigos.
 */
final class ResponsiblePersonAddress implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $streetAddressLine1 = null,
        // Depreciado: volta vazio.
        public readonly ?string $streetAddressLine2 = null,
        // Depreciado: volta vazio.
        public readonly ?string $district = null,
        // Depreciado: volta vazio.
        public readonly ?string $city = null,
        // Depreciado: volta vazio.
        public readonly ?string $province = null,
        public readonly ?string $postalCode = null,
        // ISO 3166 de 2 letras; tem que ser pais da UE.
        public readonly ?string $country = null,
    ) {}
}

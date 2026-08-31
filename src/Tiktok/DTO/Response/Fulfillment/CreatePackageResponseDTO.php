<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado de POST /fulfillment/202512/packages.
 *
 * Cria o pacote mas NAO despacha: depois disto ainda e' preciso chamar
 * shipPackage. Peso e dimensao voltam ECOADOS/normalizados pela plataforma —
 * podem diferir do que foi enviado.
 */
final class CreatePackageResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $packageId = null,
        public readonly ?PackageDimension $dimension = null,
        public readonly ?PackageWeight $weight = null,
        public readonly ?ShippingServiceInfo $shippingServiceInfo = null,
        public readonly ?int $createTime = null,
    ) {}
}

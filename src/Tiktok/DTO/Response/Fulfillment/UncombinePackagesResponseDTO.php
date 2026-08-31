<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado de POST /fulfillment/202309/packages/{id}/uncombine.
 *
 * Devolve os pacotes NOVOS gerados pela separacao — o pacote original
 * deixa de existir.
 *
 * @property list<CombinedPackage>|null $packages
 */
final class UncombinePackagesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(CombinedPackage::class)]
        public readonly ?array $packages = null,
    ) {}
}

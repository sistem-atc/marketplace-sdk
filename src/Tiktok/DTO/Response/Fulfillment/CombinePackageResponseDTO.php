<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado de POST /fulfillment/202309/packages/combine.
 *
 * Parcial dos dois lados: `packages` traz o que combinou, `errors` o que
 * nao. Ambos podem vir preenchidos na MESMA resposta.
 *
 * @property list<CombinedPackage>|null $packages
 * @property list<FulfillmentError>|null $errors
 */
final class CombinePackageResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(CombinedPackage::class)]
        public readonly ?array $packages = null,
        #[ArrayOf(FulfillmentError::class)]
        public readonly ?array $errors = null,
    ) {}
}

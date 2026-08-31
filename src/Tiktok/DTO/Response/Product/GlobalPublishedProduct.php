<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto LOCAL gerado pela publicacao do global num mercado.
 * Um mercado tem uma unica loja, por isso `shopId` e' 1:1 com `region`.
 *
 * @property list<GlobalPublishedSku>|null $skus
 */
final class GlobalPublishedProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $region = null,
        public readonly ?string $shopId = null,
        #[ArrayOf(GlobalPublishedSku::class)]
        public readonly ?array $skus = null,
    ) {}
}

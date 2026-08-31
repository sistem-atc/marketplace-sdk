<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de criacao de produto global.
 *
 * Criar NAO publica: o produto nasce so' no catalogo global. Pra virar
 * anuncio em algum mercado e' preciso chamar Publish Global Product.
 *
 * @property list<GlobalCreatedSku>|null $globalSkus
 */
final class GlobalProductCreateResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $globalProductId = null,
        #[ArrayOf(GlobalCreatedSku::class)]
        public readonly ?array $globalSkus = null,
    ) {}
}

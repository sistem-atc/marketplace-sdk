<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de Edit Global Product (edicao completa) e de Partial Edit Global
 * Product — a shape e' identica, por isso um DTO so'.
 *
 * `publishResults` conta o que aconteceu ao propagar a edicao pros mercados
 * onde o produto ja' estava publicado; falha ali nao reverte a edicao do
 * produto global.
 *
 * @property list<GlobalCreatedSku>|null $globalSkus
 * @property list<GlobalPublishResult>|null $publishResults
 */
final class GlobalProductEditResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(GlobalCreatedSku::class)]
        public readonly ?array $globalSkus = null,
        #[ArrayOf(GlobalPublishResult::class)]
        public readonly ?array $publishResults = null,
    ) {}
}

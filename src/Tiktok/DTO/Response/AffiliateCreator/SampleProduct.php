<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto da amostra pedida pelo creator.
 *
 * `skuSalePropertyValueNames` e' declarado `[]string` na doc mas o exemplo
 * OFICIAL manda uma STRING ("red, large size"). Tipar array descartaria o
 * valor quando vem string — por isso fica `mixed`, sem cast.
 */
final class SampleProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $skuId = null,
        public readonly mixed $skuSalePropertyValueNames = null,
    ) {}
}

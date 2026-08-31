<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.product` do topico 56 — o item que o criador pediu como amostra.
 *
 * ⚠️ Doc declara `[]object`, exemplo manda UM OBJETO. Seguimos o exemplo.
 * `sku_id` e' a variacao exata pedida — e' ele que casa com o item que sairia
 * numa nota, nao o `id` do produto pai.
 */
final class SampleApplicationProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        /** SKU (variacao) que o criador pediu. */
        public readonly ?string $skuId = null,
    ) {}
}

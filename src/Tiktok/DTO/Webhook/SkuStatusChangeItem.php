<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Item de `data.skus[]` do webhook TYPE 50 — uma variacao que mudou de estado. */
final class SkuStatusChangeItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $skuId = null,
        // CREATED | DELETED | ACTIVATED | DEACTIVATED
        public readonly ?string $status = null,
        // SELLER | PLATFORM | COMBO_RELATION. So' vem quando status = DEACTIVATED
        // (o item CREATED do exemplo oficial nao traz a chave).
        public readonly ?string $deactivationSource = null,
    ) {}
}

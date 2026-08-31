<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Item de `goods` de um consign order (webhook type 58). */
final class McfConsignGoods implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** goods_id gerado pelo FBT — NAO e' o sku_id da loja. */
        public readonly ?string $id = null,
        /** int16 na doc; quantidade dessa mercadoria no consign order. */
        public readonly ?int $quantity = null,
    ) {}
}

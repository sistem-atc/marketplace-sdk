<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de uma consign order MCF.
 *
 * Aqui a chave e' `id` (nao `goods_id`, como no inbound) e EXISTE `quantity` —
 * diferente do pedido do TikTok Shop, onde cada line_item e' uma unidade.
 */
final class FbtMcfGoodsItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?int $quantity = null,
    ) {}
}

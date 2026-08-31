<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do webhook type 23 — Goods match.
 *
 * E' o de/para FBT: amarra o `goods_id` do armazem do TikTok ao par
 * product_id/sku_id da loja. Sem esse vinculo, saldo e movimento FBT nao tem
 * como virar SKU nosso — e' o webhook que sustenta o de/para de estoque.
 */
final class GoodsMatchWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** MATCH | UNMATCH — UNMATCH desfaz o de/para, nao e' delecao de produto. */
        public readonly ?string $matchType = null,
        /** LOCAL | GLOBAL. */
        public readonly ?string $productType = null,
        /** Id do goods no FBT (lado armazem). */
        public readonly ?string $goodsId = null,
        /** Id do produto na loja TikTok (lado catalogo). */
        public readonly ?string $productId = null,
        public readonly ?string $skuId = null,
        /** Epoch em SEGUNDOS da acao. */
        public readonly ?int $updateTime = null,
    ) {}
}

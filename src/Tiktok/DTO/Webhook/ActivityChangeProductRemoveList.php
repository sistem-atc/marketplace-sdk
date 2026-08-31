<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.product_remove_list` do topico 63 — o que SAIU da campanha.
 *
 * ⚠️ Shape diferente do update_list: aqui existe `sku_ids`, porque da' pra
 * remover uma VARIACAO sem remover o produto inteiro. Nao reuse o DTO de
 * update, o campo sumiria.
 *
 * As listas podem chegar VAZIAS (`[]`) em vez de ausentes, como no exemplo
 * oficial — vazio significa "nada removido nessa dimensao", nao "desconhecido".
 */
final class ActivityChangeProductRemoveList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<string>|null  $productIds
     * @param  list<string>|null  $skuIds
     * @param  list<string>|null  $benefitProductIds
     * @param  list<string>|null  $excludeProductIds
     */
    public function __construct(
        public readonly ?array $productIds = null,
        /** Variacoes removidas sem tirar o produto pai. */
        public readonly ?array $skuIds = null,
        public readonly ?array $benefitProductIds = null,
        /** Exclusoes BXGY removidas. Max. 100. */
        public readonly ?array $excludeProductIds = null,
    ) {}
}

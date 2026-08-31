<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.product_update_list` do topico 63 — o escopo de produtos INCLUIDO na
 * campanha depois da mudanca.
 *
 * Os tres campos convivem porque campanha BXGY tem tres papeis diferentes:
 * `product_ids` sao os que participam, `benefit_product_ids` sao os que dao o
 * beneficio (o "get" do buy-X-get-Y) e `exclude_product_ids` sao exclusoes
 * (max. 100). Nao sao a mesma lista sob nomes diferentes.
 */
final class ActivityChangeProductUpdateList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<string>|null  $productIds
     * @param  list<string>|null  $excludeProductIds
     * @param  list<string>|null  $benefitProductIds
     */
    public function __construct(
        public readonly ?array $productIds = null,
        /** Exclusoes BXGY. Max. 100. */
        public readonly ?array $excludeProductIds = null,
        /** Produtos que CONCEDEM o beneficio. */
        public readonly ?array $benefitProductIds = null,
    ) {}
}

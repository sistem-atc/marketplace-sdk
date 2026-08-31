<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 42 — Combined listing change.
 *
 * "Combined listing" agrupa produtos distintos numa unica pagina de exibicao.
 * `products` e' a lista COMPLETA depois da mudanca (nao o delta); o delta vem
 * separado em `addedProductIds`/`removedProductIds`.
 *
 * Nao tem `update_time` — a hora e' a do envelope.
 */
final class CombinedListingChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  list<CombinedListingProduct>|null  $products
     * @param  list<string>|null  $addedProductIds
     * @param  list<string>|null  $removedProductIds
     */
    public function __construct(
        public readonly ?string $combinedListingId = null,
        #[ArrayOf(CombinedListingProduct::class)]
        public readonly ?array $products = null,
        // LIVE | SYSTEM_DEACTIVATED | SELLER_DEACTIVATED | DELETED.
        // So' o LIVE aparece na pagina de exibicao do produto.
        public readonly ?string $status = null,
        // CREATE | UPDATE | DELETE
        public readonly ?string $changeType = null,
        public readonly ?array $addedProductIds = null,
        public readonly ?array $removedProductIds = null,
    ) {}
}

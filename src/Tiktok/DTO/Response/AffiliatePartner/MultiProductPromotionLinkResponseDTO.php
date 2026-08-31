<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de .../promotion_links/generate_batch (até 50 produtos).
 *
 * ARMADILHA: lote parcial. Produto que não gerou link cai em
 * `failed_product_ids` com `code: 0` no envelope — quem não conferir acha que
 * gerou 50 e distribui 47.
 */
final class MultiProductPromotionLinkResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ProductPromotionLink::class)]
        public readonly ?array $productPromotionLinks = null,
        /** Ids que falharam. Lista crua — a doc declara []string. */
        public readonly ?array $failedProductIds = null,
    ) {}
}

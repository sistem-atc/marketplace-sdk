<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Item;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * GET /items/{id}/sale_price — preço EFETIVO (com campanha/oferta do ML), que o
 * `price` do anúncio NÃO reflete.
 *
 * `amount` = PARA (o que o cliente paga) · `regularAmount` = DE (riscado).
 * `metadata` traz campaign_id/promotion_type (shape volátil → cru).
 * Pode dar 404 quando não há price rule ativa — caller trata (fallback no price).
 */
final class SalePriceResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $priceId = null,
        public readonly ?float $amount = null,
        public readonly ?float $regularAmount = null,
        public readonly ?string $currencyId = null,
        public readonly ?string $referenceDate = null,
        public readonly mixed $metadata = null,
    ) {}
}

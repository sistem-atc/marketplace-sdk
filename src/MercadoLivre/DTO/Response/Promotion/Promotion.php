<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Promotion;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Promoção/campanha do ML. O MESMO shape serve pros 3 usos:
 *   - campanha do seller  (list/listAll/get)
 *   - item dentro de uma campanha (listItems)
 *   - campanha de um item (getItemPromotions — traz ref_id/percentuais)
 *
 * `refId` = o resource do webhook (public_candidates/public_offers).
 * `meliPercentage`/`sellerPercentage` = split do subsídio.
 */
final class Promotion implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $type = null,
        public readonly ?string $subType = null,
        public readonly ?string $status = null,
        public readonly ?string $refId = null,
        public readonly ?string $offerId = null,
        // preço
        public readonly ?float $price = null,
        public readonly ?float $originalPrice = null,
        public readonly ?float $fixedAmount = null,
        public readonly ?float $fixedPercentage = null,
        public readonly ?float $meliPercentage = null,
        public readonly ?float $sellerPercentage = null,
        public readonly ?float $suggestedDiscountedPrice = null,
        public readonly ?float $minDiscountedPrice = null,
        public readonly ?float $maxDiscountedPrice = null,
        public readonly ?float $maxTopDiscountedPrice = null,
        public readonly ?float $minPurchaseAmount = null,
        // datas
        public readonly ?string $startDate = null,
        public readonly ?string $finishDate = null,
        public readonly ?string $endDate = null,
        public readonly ?string $deadlineDate = null,
        // outros
        // stock vem como OBJETO {min,max,remaining_stock} em campanhas de
        // estoque limitado (e ausente nas demais) — mixed preserva.
        public readonly mixed $stock = null,
        public readonly ?string $paymentMethod = null,
    ) {}
}

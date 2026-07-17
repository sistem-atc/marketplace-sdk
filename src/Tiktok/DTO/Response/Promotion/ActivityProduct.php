<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto participante da promoção (`products[]`, só no getActivity/detalhe).
 * `id` é o product_id. IDs são STRING (snowflake).
 */
final class ActivityProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?ActivityPrice $activityPrice = null,
        public readonly ?int $quantityLimit = null,
        public readonly ?int $quantityPerUser = null,
    ) {}
}

<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Motivo pelo qual o pedido TEM de ser dividido.
 *
 * `maxCount` vem como STRING ("2") mesmo sendo contagem — a API declara
 * string; tipar int quebraria o roundtrip.
 */
final class MustSplitReason implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** CATEGORY_ITEM_LIMITATION | TOTAL_COUNT_LIMITATION */
        public readonly ?string $type = null,
        /** So' preenchido quando type = CATEGORY_ITEM_LIMITATION. */
        public readonly ?string $categoryId = null,
        public readonly ?string $maxCount = null,
    ) {}
}

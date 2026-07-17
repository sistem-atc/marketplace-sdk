<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/** `items[].BuyerInfo` — gift wrap por item. */
final class ItemBuyerInfo implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?Money $giftWrapPrice = null,
        public readonly ?Money $giftWrapTax = null,
        public readonly ?string $giftMessageText = null,
        public readonly ?string $giftWrapLevel = null,
    ) {}
}

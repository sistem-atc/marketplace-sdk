<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado de POST /fulfillment/202601/redeem_info/callback.
 *
 * Falha PARCIAL por item: o envelope volta 0 e cada `orderStatuses[]` traz
 * seu proprio `statusCode`. Precisa varrer item a item.
 *
 * @property list<RedeemOrderStatus>|null $orderStatuses
 */
final class RedeemInfoCallbackResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(RedeemOrderStatus::class)]
        public readonly ?array $orderStatuses = null,
    ) {}
}

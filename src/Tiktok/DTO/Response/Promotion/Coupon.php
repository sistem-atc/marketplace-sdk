<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Cupom do TikTok Shop (/promotion/202406/coupons).
 *
 * Cupom NÃO é Activity: Activity é campanha de preço no produto (flash sale,
 * desconto direto); cupom é resgatado pelo comprador e aplicado no checkout.
 * Vivem em versões diferentes da mesma API (202309 x 202406).
 *
 * ARMADILHA DE UNIDADE: `createTime`/`updateTime` vêm em MILISSEGUNDOS
 * (1661756811000), enquanto `claimDuration`/`redemptionDuration` vêm em
 * SEGUNDOS (1709568000). Misturar as duas escalas joga a data pra 1970 ou pro
 * ano 54000. Preservamos o valor cru — a conversão é do consumidor.
 *
 * O Search Coupon List devolve um SUBCONJUNTO destes campos: sem
 * `usageStats`, `displayChannels`, `productIds`, `sellerTnc` e `liveTasks`.
 * O mesmo DTO serve aos dois; ausência vira null.
 *
 * @property list<string>|null $displayChannels
 * @property list<string>|null $productIds
 * @property list<CouponLiveTask>|null $liveTasks
 */
final class Coupon implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // Snowflake STRING.
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        // REGULAR | LIVE | CREATOR_EXCLUSIVE | CHAT | PROMO_CODE
        public readonly ?string $displayType = null,
        // NOT_START | ONGOING | EXPIRED | DEACTIVATED
        public readonly ?string $status = null,
        // MILISSEGUNDOS (ao contrário das durations abaixo)
        public readonly ?int $createTime = null,
        public readonly ?int $updateTime = null,
        public readonly ?CouponClaimDuration $claimDuration = null,
        public readonly ?CouponRedemptionDuration $redemptionDuration = null,
        public readonly ?array $displayChannels = null,
        public readonly ?string $promoCode = null,
        public readonly ?string $targetBuyerSegment = null,
        public readonly ?CouponUsageLimits $usageLimits = null,
        public readonly ?CouponUsageStats $usageStats = null,
        public readonly ?CouponDiscount $discount = null,
        public readonly ?CouponThreshold $threshold = null,
        // FULL_SHOP | SPECIFIC_PRODUCTS
        public readonly ?string $productScope = null,
        public readonly ?array $productIds = null,
        public readonly ?string $sellerTnc = null,
        // SELLER_CENTER | SELLER_APP | TTS_CRM
        public readonly ?string $creationSource = null,
        #[ArrayOf(CouponLiveTask::class)]
        public readonly ?array $liveTasks = null,
    ) {}
}

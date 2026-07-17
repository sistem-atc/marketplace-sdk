<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Programa de assinatura do produto (`subscribe_info`) — compra recorrente
 * com desconto.
 *
 * @property list<SubscribePromotionConfig>|null $subscribePromotionConfig
 */
final class SubscribeInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $subscribeStatus = null,
        public readonly ?bool $supportSubscribe = null,
        #[ArrayOf(SubscribePromotionConfig::class)]
        public readonly ?array $subscribePromotionConfig = null,
    ) {}
}

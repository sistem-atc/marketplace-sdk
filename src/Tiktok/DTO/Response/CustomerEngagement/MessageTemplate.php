<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Template de mensagem de engajamento predefinido pelo TikTok. */
final class MessageTemplate implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $messageTitle = null,
        public readonly ?string $messageBody = null,
        public readonly ?ProductCardRules $productCardRules = null,
        public readonly ?CouponCardRules $couponCardRules = null,
    ) {}
}

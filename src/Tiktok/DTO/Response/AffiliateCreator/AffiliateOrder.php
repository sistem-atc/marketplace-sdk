<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pedido de afiliado do creator — item de `orders[]`.
 *
 * `status`: UNSPECIFIED | AWAITING PAYMENT | To-SETTLE | SETTLED | REFUNDED |
 * FROZEN. Datas em epoch de SEGUNDOS (UTC+0).
 *
 * @property list<AffiliateOrderSku>|null $skus
 */
final class AffiliateOrder implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?int $createTime = null,
        public readonly ?int $deliveryTime = null,
        public readonly ?string $status = null,
        #[ArrayOf(AffiliateOrderSku::class)]
        public readonly ?array $skus = null,
    ) {}
}

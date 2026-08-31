<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado do callback de resgate por item do pedido.
 * `statusCode` diferente de zero = aquele item falhou.
 */
final class RedeemOrderStatus implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderLineId = null,
        public readonly ?int $statusCode = null,
    ) {}
}

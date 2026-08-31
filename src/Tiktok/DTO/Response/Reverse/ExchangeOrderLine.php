<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Linha do pedido de TROCA.
 *
 * A doc do TikTok marca TODOS estes campos como "Ignore this field. It is not
 * yet supported." — o exemplo oficial vem preenchido com "0". Mapeamos assim
 * mesmo: o dia que o TikTok ligar troca no BR o dado ja' chega inteiro, em vez
 * de morrer em silencio na desserializacao. Nao consuma antes de validar.
 */
final class ExchangeOrderLine implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderLineId = null,
        public readonly ?string $skuId = null,
        public readonly ?string $skuName = null,
        public readonly ?string $productName = null,
        public readonly ?string $sellerSkuName = null,
        public readonly ?ProductImage $productImage = null,
        /** Mesma shape do refund_amount, sem retail_delivery_fee/buyer_service_fee. */
        public readonly ?RefundAmount $priceDetail = null,
    ) {}
}

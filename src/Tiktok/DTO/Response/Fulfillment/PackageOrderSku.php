<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * SKU dentro de `orders[].skus[]` de um pacote.
 *
 * A doc marca o bloco inteiro como [Deprecated] — o TikTok manda hoje, mas
 * pretende parar. Mapeado assim mesmo: enquanto vier, nao se joga fora.
 * Quem precisa do item do pacote deve usar `orderLineItemIds` do pacote.
 */
final class PackageOrderSku implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $imageUrl = null,
        // Unico lugar do fulfillment que traz quantidade como INT — o pedido
        // (Order API) nao tem quantity: la cada line_item e' 1 unidade.
        public readonly ?int $quantity = null,
    ) {}
}

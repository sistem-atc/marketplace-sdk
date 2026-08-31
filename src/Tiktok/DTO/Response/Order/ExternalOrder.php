<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * A NOSSA referencia gravada dentro do pedido do TikTok.
 *
 * O par (`platform`, `id`) e' a chave: o TikTok guarda UMA referencia por
 * plataforma no mesmo pedido. Regravar com a MESMA `platform` sobrescreve;
 * gravar com outra `platform` acrescenta uma segunda referencia. Nao existe
 * endpoint de delete — so' sobrescrita.
 *
 * @property list<ExternalOrderLineItem>|null $lineItems
 */
final class ExternalOrder implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Id do pedido no NOSSO sistema. */
        public readonly ?string $id = null,
        /** Alias do nosso OMS — vocabulario FECHADO do TikTok (ver OrderMethods::EXTERNAL_ORDER_PLATFORMS). */
        public readonly ?string $platform = null,
        #[ArrayOf(ExternalOrderLineItem::class)]
        public readonly ?array $lineItems = null,
    ) {}
}

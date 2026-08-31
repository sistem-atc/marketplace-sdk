<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 17 — Shoppable content posting.
 *
 * Evento de AFILIADO, nao de loja: o envelope traz `creator_open_id` no lugar
 * de `shop_id`, e so' chega se o criador tiver autorizado o escopo
 * "Read Creator Affiliate Collaborations".
 */
final class ShoppableContentPostingWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productId = null,
        public readonly ?ShoppableContentEvent $event = null,
    ) {}
}

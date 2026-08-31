<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 50 — SKU status change.
 *
 * ARMADILHA: o exemplo oficial ainda traz `"type": "TBD"` (a doc diz que o
 * numero do topico esta' "pending final assignment"). Quem rotear por type
 * numerico precisa aguentar receber string aqui — o WebhookEnvelope tipa `type`
 * como int, e "TBD" vira 0.
 *
 * Um evento pode carregar VARIAS variacoes de uma vez: o consumidor tem que
 * iterar `skus`, nao ler so' o primeiro.
 */
final class SkuStatusChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<SkuStatusChangeItem>|null $skus */
    public function __construct(
        public readonly ?string $productId = null,
        #[ArrayOf(SkuStatusChangeItem::class)]
        public readonly ?array $skus = null,
        public readonly ?int $updateTime = null,
    ) {}
}
